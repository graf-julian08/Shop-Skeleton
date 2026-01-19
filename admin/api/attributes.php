<?php
/**
 * Attributes API
 * Endpoints: get_attributes, get_attribute, save_attribute, delete_attribute,
 *           get_attribute_groups, save_attribute_group, delete_attribute_group,
 *           save_attribute_options, get_stats
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

Database::configure($database);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_attributes':
            handleGetAttributes($shopId);
            break;
        case 'get_attribute':
            handleGetAttribute($shopId);
            break;
        case 'save_attribute':
            handleSaveAttribute($shopId);
            break;
        case 'delete_attribute':
            handleDeleteAttribute($shopId);
            break;
        case 'get_attribute_groups':
            handleGetAttributeGroups($shopId);
            break;
        case 'save_attribute_group':
            handleSaveAttributeGroup($shopId);
            break;
        case 'delete_attribute_group':
            handleDeleteAttributeGroup($shopId);
            break;
        case 'save_attribute_options':
            handleSaveAttributeOptions($shopId);
            break;
        case 'get_stats':
            handleGetStats($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET ATTRIBUTES
// =====================================================================
function handleGetAttributes(int $shopId): void
{
    $search = trim($_GET['search'] ?? '');
    $type = $_GET['type'] ?? '';

    $where = ["a.shop_id = ?"];
    $params = [$shopId];

    if ($search) {
        $where[] = "(a.name LIKE ? OR a.code LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    if ($type && $type !== 'all') {
        $where[] = "a.type = ?";
        $params[] = $type;
    }

    $whereClause = implode(' AND ', $where);

    $query = "
        SELECT a.*,
               (SELECT COUNT(*) FROM attribute_options WHERE attribute_id = a.id) as options_count,
               (SELECT COUNT(DISTINCT pav.product_id) FROM product_attribute_values pav WHERE pav.attribute_id = a.id) as products_count
        FROM attributes a
        WHERE {$whereClause}
        ORDER BY a.sort_order ASC, a.name ASC
    ";

    $attributes = Database::fetchAll($query, $params);

    // Type labels in German
    $typeLabels = [
        'text' => 'Text',
        'textarea' => 'Textbereich',
        'number' => 'Zahl',
        'select' => 'Dropdown',
        'multiselect' => 'Mehrfachauswahl',
        'boolean' => 'Ja/Nein',
        'color' => 'Farbe',
        'date' => 'Datum',
        'price' => 'Preis'
    ];

    foreach ($attributes as &$attr) {
        $attr['type_label'] = $typeLabels[$attr['type']] ?? $attr['type'];
    }

    echo json_encode([
        'success' => true,
        'attributes' => $attributes,
        'types' => $typeLabels
    ]);
}

// =====================================================================
// GET SINGLE ATTRIBUTE
// =====================================================================
function handleGetAttribute(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid attribute ID']);
        return;
    }

    $attribute = Database::fetch(
        "SELECT a.*,
                (SELECT COUNT(DISTINCT pav.product_id) FROM product_attribute_values pav WHERE pav.attribute_id = a.id) as products_count
         FROM attributes a
         WHERE a.id = ? AND a.shop_id = ?",
        [$id, $shopId]
    );

    if (!$attribute) {
        echo json_encode(['success' => false, 'error' => 'Attribut nicht gefunden']);
        return;
    }

    // Get options for select/multiselect/color types
    $options = [];
    if (in_array($attribute['type'], ['select', 'multiselect', 'color'])) {
        $options = Database::fetchAll(
            "SELECT * FROM attribute_options WHERE attribute_id = ? ORDER BY sort_order ASC, label ASC",
            [$id]
        );
    }

    $attribute['options'] = $options;

    echo json_encode([
        'success' => true,
        'attribute' => $attribute
    ]);
}

// =====================================================================
// SAVE ATTRIBUTE
// =====================================================================
function handleSaveAttribute(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $type = $_POST['type'] ?? 'text';
    $isRequired = (int) ($_POST['is_required'] ?? 0);
    $isFilterable = (int) ($_POST['is_filterable'] ?? 0);
    $isSearchable = (int) ($_POST['is_searchable'] ?? 0);
    $isVisibleOnFrontend = (int) ($_POST['is_visible_on_frontend'] ?? 1);
    $usedForVariants = (int) ($_POST['used_for_variants'] ?? 0);

    // Validation
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Attributname ist erforderlich';
    }

    // Generate code if empty
    if (empty($code)) {
        $code = generateCode($name);
    } else {
        $code = generateCode($code);
    }

    // Check code uniqueness
    $existingCode = Database::fetch(
        "SELECT id FROM attributes WHERE code = ? AND shop_id = ? AND id != ?",
        [$code, $shopId, $id]
    );
    if ($existingCode) {
        $code = $code . '_' . time();
    }

    // Valid types
    $validTypes = ['text', 'textarea', 'number', 'select', 'multiselect', 'boolean', 'color', 'date', 'price'];
    if (!in_array($type, $validTypes)) {
        $errors[] = 'Ungültiger Attributtyp';
    }

    // Only select, multiselect, and color support variants
    $variantSupportedTypes = ['select', 'multiselect', 'color'];
    if ($usedForVariants && !in_array($type, $variantSupportedTypes)) {
        $usedForVariants = 0; // Force disable for incompatible types
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }

    if ($id > 0) {
        // Update - but don't allow type change if attribute is used
        $existing = Database::fetch("SELECT type FROM attributes WHERE id = ?", [$id]);
        $usedCount = Database::fetch(
            "SELECT COUNT(*) as count FROM product_attribute_values WHERE attribute_id = ?",
            [$id]
        )['count'];

        if ($usedCount > 0 && $existing && $existing['type'] !== $type) {
            echo json_encode([
                'success' => false,
                'errors' => ["Attributtyp kann nicht geändert werden, da {$usedCount} Produkte dieses Attribut verwenden."]
            ]);
            return;
        }

        Database::query(
            "UPDATE attributes SET 
                name = ?, code = ?, type = ?, is_required = ?, is_filterable = ?,
                is_searchable = ?, is_visible_on_frontend = ?, used_for_variants = ?,
                updated_at = NOW()
             WHERE id = ? AND shop_id = ?",
            [
                $name,
                $code,
                $type,
                $isRequired,
                $isFilterable,
                $isSearchable,
                $isVisibleOnFrontend,
                $usedForVariants,
                $id,
                $shopId
            ]
        );

        $message = 'Attribut aktualisiert';
    } else {
        // Insert
        $id = Database::insert('attributes', [
            'shop_id' => $shopId,
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'is_required' => $isRequired,
            'is_unique' => 0,
            'is_filterable' => $isFilterable,
            'is_searchable' => $isSearchable,
            'is_visible_on_frontend' => $isVisibleOnFrontend,
            'is_user_defined' => 1,
            'used_for_variants' => $usedForVariants,
            'sort_order' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $message = 'Attribut erstellt';
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'id' => $id
    ]);
}

// =====================================================================
// DELETE ATTRIBUTE
// =====================================================================
function handleDeleteAttribute(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid attribute ID']);
        return;
    }

    // Check if used by products
    $usedCount = Database::fetch(
        "SELECT COUNT(*) as count FROM product_attribute_values WHERE attribute_id = ?",
        [$id]
    )['count'];

    if ($usedCount > 0) {
        echo json_encode([
            'success' => false,
            'error' => "Attribut kann nicht gelöscht werden. Es wird von {$usedCount} Produkten verwendet."
        ]);
        return;
    }

    // Delete options first
    Database::delete('attribute_options', 'attribute_id = ?', [$id]);

    // Delete attribute
    Database::delete('attributes', 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => true,
        'message' => 'Attribut gelöscht'
    ]);
}

// =====================================================================
// GET ATTRIBUTE GROUPS
// =====================================================================
function handleGetAttributeGroups(int $shopId): void
{
    $groups = Database::fetchAll(
        "SELECT ag.*,
                (SELECT COUNT(*) FROM attributes a WHERE a.shop_id = ag.shop_id) as attributes_count
         FROM attribute_groups ag
         WHERE ag.shop_id = ?
         ORDER BY ag.sort_order ASC, ag.name ASC",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'groups' => $groups
    ]);
}

// =====================================================================
// SAVE ATTRIBUTE GROUP
// =====================================================================
function handleSaveAttributeGroup(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');

    $errors = [];
    if (empty($name)) {
        $errors[] = 'Gruppenname ist erforderlich';
    }

    if (empty($code)) {
        $code = generateCode($name);
    }

    // Check code uniqueness
    $existing = Database::fetch(
        "SELECT id FROM attribute_groups WHERE code = ? AND shop_id = ? AND id != ?",
        [$code, $shopId, $id]
    );
    if ($existing) {
        $code = $code . '_' . time();
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }

    if ($id > 0) {
        Database::query(
            "UPDATE attribute_groups SET name = ?, code = ? WHERE id = ? AND shop_id = ?",
            [$name, $code, $id, $shopId]
        );
        $message = 'Gruppe aktualisiert';
    } else {
        $id = Database::insert('attribute_groups', [
            'shop_id' => $shopId,
            'name' => $name,
            'code' => $code,
            'sort_order' => 0
        ]);
        $message = 'Gruppe erstellt';
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'id' => $id
    ]);
}

// =====================================================================
// DELETE ATTRIBUTE GROUP
// =====================================================================
function handleDeleteAttributeGroup(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid group ID']);
        return;
    }

    Database::delete('attribute_groups', 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => true,
        'message' => 'Gruppe gelöscht'
    ]);
}

// =====================================================================
// SAVE ATTRIBUTE OPTIONS (for select/multiselect/color)
// =====================================================================
function handleSaveAttributeOptions(int $shopId): void
{
    $attributeId = (int) ($_POST['attribute_id'] ?? 0);
    $optionsJson = $_POST['options'] ?? '[]';

    if ($attributeId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid attribute ID']);
        return;
    }

    $options = json_decode($optionsJson, true);
    if (!is_array($options)) {
        echo json_encode(['success' => false, 'error' => 'Invalid options format']);
        return;
    }

    // Get existing option IDs
    $existingIds = array_column(
        Database::fetchAll("SELECT id FROM attribute_options WHERE attribute_id = ?", [$attributeId]),
        'id'
    );

    $newIds = [];

    foreach ($options as $index => $option) {
        $optionId = (int) ($option['id'] ?? 0);
        $value = trim($option['value'] ?? '');
        $label = trim($option['label'] ?? $value);
        $colorHex = trim($option['color_hex'] ?? '');

        if (empty($value) && empty($label)) {
            continue;
        }

        if ($optionId > 0 && in_array($optionId, $existingIds)) {
            // Update existing
            Database::query(
                "UPDATE attribute_options SET value = ?, label = ?, color_hex = ?, sort_order = ? WHERE id = ?",
                [$value, $label, $colorHex, $index, $optionId]
            );
            $newIds[] = $optionId;
        } else {
            // Insert new
            $newId = Database::insert('attribute_options', [
                'attribute_id' => $attributeId,
                'value' => $value,
                'label' => $label,
                'color_hex' => $colorHex,
                'sort_order' => $index
            ]);
            $newIds[] = $newId;
        }
    }

    // Delete removed options (only if not used)
    $toDelete = array_diff($existingIds, $newIds);
    foreach ($toDelete as $deleteId) {
        Database::delete('attribute_options', 'id = ?', [$deleteId]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Optionen gespeichert'
    ]);
}

// =====================================================================
// GET STATS
// =====================================================================
function handleGetStats(int $shopId): void
{
    $stats = Database::fetch(
        "SELECT 
            (SELECT COUNT(*) FROM attributes WHERE shop_id = ?) as total_attributes,
            (SELECT COUNT(*) FROM attribute_groups WHERE shop_id = ?) as total_groups,
            (SELECT COUNT(*) FROM attributes WHERE shop_id = ? AND used_for_variants = 1) as variant_attributes,
            (SELECT COUNT(*) FROM attributes WHERE shop_id = ? AND is_filterable = 1) as filterable_attributes
        ",
        [$shopId, $shopId, $shopId, $shopId]
    );

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

// =====================================================================
// HELPER FUNCTIONS
// =====================================================================
function generateCode(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $text);
    $text = preg_replace('/[^a-z0-9]+/', '_', $text);
    return trim($text, '_');
}

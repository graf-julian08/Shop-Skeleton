<?php
/**
 * ============================================
 * ADMIN PANEL - ROUTER
 * ============================================
 * Zuständig NUR für:
 * - GET-Parameter lesen
 * - Erlaubte Seiten validieren
 * - Passende Page include()
 * 
 * KEIN HTML
 * KEIN Dashboard Rendering
 * KEIN Output außer include()
 * ============================================
 */

/**
 * Page-Datei ermitteln
 * 
 * @param string $page Der Page-Parameter
 * @return string|null Pfad zur Page-Datei oder null (Dashboard), 'error' bei Fehler
 */
function getPageFile($page) {
    global $allowedPages;
    
    // Leere Seite oder Dashboard = null (Dashboard wird von index.php gerendert)
    if (empty($page) || $page === 'dashboard') {
        return null;
    }
    
    // Sicherheit: Nur bekannte Seiten erlauben
    if (!in_array($page, $allowedPages)) {
        return 'error';
    }
    
    // Pfad-Traversal verhindern
    $page = str_replace(['..', '\\'], '', $page);
    
    // Datei-Existenz prüfen
    $filePath = __DIR__ . '/pages/' . $page . '.php';
    
    if (!file_exists($filePath)) {
        return 'error';
    }
    
    return $filePath;
}

/**
 * Seite laden und ausgeben
 * Diese Funktion wird von index.php aufgerufen
 */
function routePage() {
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
    $filePath = getPageFile($page);
    
    // null = Dashboard (wird in index.php definiert)
    if ($filePath === null) {
        return 'dashboard';
    }
    
    // Fehlerseite
    if ($filePath === 'error') {
        return 'error';
    }
    
    // Subpage includieren
    include $filePath;
    return 'page';
}

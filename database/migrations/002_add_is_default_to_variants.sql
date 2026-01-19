-- Migration: Add is_default column to product_variants table
-- This column tracks which variant is the default for display in product listings

ALTER TABLE product_variants 
ADD COLUMN IF NOT EXISTS is_default TINYINT(1) DEFAULT 0 COMMENT 'Marks the default variant for display'
AFTER is_active;

-- Set the first variant of each product as default if none is set
UPDATE product_variants pv
SET pv.is_default = 1
WHERE pv.id = (
    SELECT min_id FROM (
        SELECT MIN(id) as min_id FROM product_variants WHERE parent_product_id = pv.parent_product_id
    ) AS subquery
)
AND NOT EXISTS (
    SELECT 1 FROM product_variants pv2 
    WHERE pv2.parent_product_id = pv.parent_product_id AND pv2.is_default = 1
);

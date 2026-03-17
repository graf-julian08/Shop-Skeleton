-- ============================================
-- Migration: Add two_factor_recovery_codes to admin_users
-- ============================================
-- The admin_users table already has:
--   two_factor_enabled TINYINT(1) DEFAULT 0
--   two_factor_secret VARCHAR(255)
-- 
-- This adds the recovery codes column for backup login.
-- ============================================

ALTER TABLE admin_users
ADD COLUMN two_factor_recovery_codes TEXT NULL AFTER two_factor_secret;

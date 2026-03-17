<?php
/**
 * ============================================
 * TWO-FACTOR AUTHENTICATION (TOTP)
 * ============================================
 * Pure PHP implementation of RFC 6238 (TOTP)
 * No external dependencies required.
 * 
 * Compatible with Google Authenticator, Authy,
 * Microsoft Authenticator, etc.
 * ============================================
 */

class TwoFactorAuth {
    
    /** @var int TOTP time step in seconds (standard: 30s) */
    private const TIME_STEP = 30;
    
    /** @var int Number of digits in the OTP code */
    private const CODE_LENGTH = 6;
    
    /** @var string Base32 alphabet */
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    
    /**
     * Generate a new random secret key (Base32 encoded)
     * 
     * @param int $length Length of secret in bytes (default 20 = 160 bit)
     * @return string Base32-encoded secret
     */
    public static function generateSecret(int $length = 20): string {
        $secret = '';
        $randomBytes = random_bytes($length);
        
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[ord($randomBytes[$i]) & 31];
        }
        
        return $secret;
    }
    
    /**
     * Calculate the current TOTP code
     * 
     * @param string $secret Base32-encoded secret
     * @param int|null $timestamp Unix timestamp (null = current time)
     * @return string 6-digit TOTP code (zero-padded)
     */
    public static function getCode(string $secret, ?int $timestamp = null): string {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        // Calculate time counter
        $timeCounter = intdiv($timestamp, self::TIME_STEP);
        
        // Pack counter as 8-byte big-endian
        $time = pack('N*', 0, $timeCounter);
        
        // Decode Base32 secret to binary
        $secretBinary = self::base32Decode($secret);
        
        // Calculate HMAC-SHA1
        $hash = hash_hmac('sha1', $time, $secretBinary, true);
        
        // Dynamic truncation (RFC 4226 Section 5.4)
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
        $binary = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );
        
        // Generate code with modulo
        $code = $binary % pow(10, self::CODE_LENGTH);
        
        return str_pad((string) $code, self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }
    
    /**
     * Verify a TOTP code against a secret
     * 
     * @param string $secret Base32-encoded secret
     * @param string $code User-submitted code
     * @param int $window Number of time steps to check before/after current (default: 1)
     * @return bool True if code is valid
     */
    public static function verifyCode(string $secret, string $code, int $window = 1): bool {
        // Sanitize: remove spaces, ensure string
        $code = trim(str_replace(' ', '', $code));
        
        if (strlen($code) !== self::CODE_LENGTH || !ctype_digit($code)) {
            return false;
        }
        
        $timestamp = time();
        
        // Check current and adjacent time windows
        for ($i = -$window; $i <= $window; $i++) {
            $checkTime = $timestamp + ($i * self::TIME_STEP);
            $validCode = self::getCode($secret, $checkTime);
            
            if (hash_equals($validCode, $code)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate the otpauth:// URI for authenticator apps
     * 
     * @param string $email User's email (account name)
     * @param string $secret Base32-encoded secret
     * @param string $issuer Application/company name
     * @return string otpauth:// URI
     */
    public static function getOtpAuthUrl(string $email, string $secret, string $issuer = 'Admin Panel'): string {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($email),
            $secret,
            rawurlencode($issuer),
            self::CODE_LENGTH,
            self::TIME_STEP
        );
    }
    
    /**
     * Get a QR code image URL using external API
     * 
     * @param string $otpAuthUrl The otpauth:// URL
     * @param int $size QR code image size in pixels
     * @return string URL to QR code image
     */
    public static function getQrCodeImageUrl(string $otpAuthUrl, int $size = 200): string {
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=%dx%d&data=%s&ecc=M',
            $size,
            $size,
            urlencode($otpAuthUrl)
        );
    }
    
    /**
     * Generate single-use recovery codes
     * 
     * @param int $count Number of codes to generate (default: 8)
     * @return array Array of recovery code strings
     */
    public static function generateRecoveryCodes(int $count = 8): array {
        $codes = [];
        
        for ($i = 0; $i < $count; $i++) {
            // Format: XXXX-XXXX (8 alphanumeric chars)
            $part1 = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $part2 = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $codes[] = $part1 . '-' . $part2;
        }
        
        return $codes;
    }
    
    /**
     * Verify a recovery code against stored codes
     * 
     * @param string $inputCode Code entered by user
     * @param array $storedCodes Array of valid recovery codes
     * @return int|false Index of matched code or false
     */
    public static function verifyRecoveryCode(string $inputCode, array $storedCodes) {
        $inputCode = strtoupper(trim(str_replace(' ', '', $inputCode)));
        
        foreach ($storedCodes as $index => $storedCode) {
            if (hash_equals(strtoupper($storedCode), $inputCode)) {
                return $index;
            }
        }
        
        return false;
    }
    
    /**
     * Decode a Base32 encoded string to binary
     * 
     * @param string $input Base32-encoded string
     * @return string Binary data
     */
    private static function base32Decode(string $input): string {
        $input = strtoupper(rtrim($input, '='));
        $output = '';
        $buffer = 0;
        $bitsLeft = 0;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $charIndex = strpos(self::BASE32_CHARS, $input[$i]);
            if ($charIndex === false) {
                continue; // Skip invalid characters
            }
            
            $buffer = ($buffer << 5) | $charIndex;
            $bitsLeft += 5;
            
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        
        return $output;
    }
}

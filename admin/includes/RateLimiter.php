<?php
/**
 * ============================================
 * RATE LIMITER
 * ============================================
 * IP-based rate limiting for authentication actions.
 * Prevents brute-force attacks on login and 2FA verification.
 * 
 * Thresholds:
 * - login:      5 attempts → 15 min block
 * - 2fa_verify: 5 attempts → 5 min block
 * ============================================
 */

class RateLimiter
{
    /**
     * Rate limit configuration per action type
     * [max_attempts, block_duration_minutes, window_minutes]
     */
    private static array $limits = [
        'login' => ['max' => 5, 'block_minutes' => 15, 'window_minutes' => 15],
        '2fa_verify' => ['max' => 5, 'block_minutes' => 5, 'window_minutes' => 5],
    ];

    /**
     * Check if an IP is currently rate-limited for an action
     * 
     * @return array ['allowed' => bool, 'remaining' => int, 'retry_after' => int|null (seconds)]
     */
    public static function check(string $ip, string $actionType): array
    {
        self::cleanup();

        $config = self::$limits[$actionType] ?? self::$limits['login'];

        $record = Database::fetch(
            "SELECT * FROM auth_rate_limits 
             WHERE ip_address = ? AND action_type = ?",
        [$ip, $actionType]
        );

        // No record = no limits hit
        if (!$record) {
            return ['allowed' => true, 'remaining' => $config['max'], 'retry_after' => null];
        }

        // Check if currently blocked
        if ($record['blocked_until'] !== null) {
            $blockedUntil = strtotime($record['blocked_until']);
            $now = time();

            if ($blockedUntil > $now) {
                $retryAfter = $blockedUntil - $now;
                return ['allowed' => false, 'remaining' => 0, 'retry_after' => $retryAfter];
            }

            // Block has expired → reset
            self::reset($ip, $actionType);
            return ['allowed' => true, 'remaining' => $config['max'], 'retry_after' => null];
        }

        // Check if window has expired
        $windowStart = strtotime($record['first_attempt_at']);
        $windowEnd = $windowStart + ($config['window_minutes'] * 60);

        if (time() > $windowEnd) {
            // Window expired → reset
            self::reset($ip, $actionType);
            return ['allowed' => true, 'remaining' => $config['max'], 'retry_after' => null];
        }

        $remaining = max(0, $config['max'] - (int)$record['attempts']);
        return ['allowed' => $remaining > 0, 'remaining' => $remaining, 'retry_after' => null];
    }

    /**
     * Record a failed attempt. Returns rate limit status after recording.
     * 
     * @return array ['allowed' => bool, 'remaining' => int, 'retry_after' => int|null]
     */
    public static function record(string $ip, string $actionType): array
    {
        $config = self::$limits[$actionType] ?? self::$limits['login'];

        $record = Database::fetch(
            "SELECT * FROM auth_rate_limits 
             WHERE ip_address = ? AND action_type = ?",
        [$ip, $actionType]
        );

        if (!$record) {
            // First attempt
            Database::insert('auth_rate_limits', [
                'ip_address' => $ip,
                'action_type' => $actionType,
                'attempts' => 1,
            ]);

            return ['allowed' => true, 'remaining' => $config['max'] - 1, 'retry_after' => null];
        }

        // Check if window expired → reset and count as first
        $windowStart = strtotime($record['first_attempt_at']);
        $windowEnd = $windowStart + ($config['window_minutes'] * 60);

        if (time() > $windowEnd) {
            Database::update('auth_rate_limits', [
                'attempts' => 1,
                'first_attempt_at' => date('Y-m-d H:i:s'),
                'blocked_until' => null,
            ], 'id = ?', [$record['id']]);

            return ['allowed' => true, 'remaining' => $config['max'] - 1, 'retry_after' => null];
        }

        $newAttempts = (int)$record['attempts'] + 1;
        $blockedUntil = null;

        // Block if max attempts reached
        if ($newAttempts >= $config['max']) {
            $blockedUntil = date('Y-m-d H:i:s', time() + ($config['block_minutes'] * 60));
        }

        Database::update('auth_rate_limits', [
            'attempts' => $newAttempts,
            'blocked_until' => $blockedUntil,
        ], 'id = ?', [$record['id']]);

        $remaining = max(0, $config['max'] - $newAttempts);
        $retryAfter = $blockedUntil ? ($config['block_minutes'] * 60) : null;

        return ['allowed' => $remaining > 0, 'remaining' => $remaining, 'retry_after' => $retryAfter];
    }

    /**
     * Reset rate limit for an IP and action (on successful auth)
     */
    public static function reset(string $ip, string $actionType): void
    {
        Database::delete('auth_rate_limits', 'ip_address = ? AND action_type = ?', [$ip, $actionType]);
    }

    /**
     * Cleanup old rate limit records (> 1 hour since last attempt)
     */
    private static function cleanup(): void
    {
        try {
            Database::query(
                "DELETE FROM auth_rate_limits WHERE last_attempt_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)"
            );
        }
        catch (Exception $e) {
        // Silently ignore cleanup errors
        }
    }

    /**
     * Get remaining minutes until block expires (for display)
     */
    public static function getBlockMinutes(int $retryAfterSeconds): int
    {
        return (int)ceil($retryAfterSeconds / 60);
    }
}

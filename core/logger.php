<?php
/**
 * Logging System
 *
 * Provides structured logging for errors, security events, and system activity.
 * Logs are stored in the /logs directory with daily rotation.
 */

class Logger {
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';
    const SECURITY = 'SECURITY';

    private static $logDir = null;

    /**
     * Initialize logger with log directory
     */
    private static function init() {
        if (self::$logDir === null) {
            self::$logDir = ROOT_PATH . '/logs';

            // Create logs directory if it doesn't exist
            if (!is_dir(self::$logDir)) {
                mkdir(self::$logDir, 0755, true);
            }
        }
    }

    /**
     * Log a message
     *
     * @param string $level Log level (DEBUG, INFO, WARNING, ERROR, CRITICAL, SECURITY)
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public static function log(string $level, string $message, array $context = []) {
        self::init();

        // Don't log DEBUG messages in production
        if ($level === self::DEBUG && !($_ENV['DEBUG'] ?? false)) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $date = date('Y-m-d');

        // Format context data
        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' | ' . json_encode($context, JSON_UNESCAPED_SLASHES);
        }

        // Add request info for security logs
        if ($level === self::SECURITY) {
            $context['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
            $context['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
            $context['uri'] = $_SERVER['REQUEST_URI'] ?? 'CLI';
            $contextStr = ' | ' . json_encode($context, JSON_UNESCAPED_SLASHES);
        }

        // Format log entry
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        // Determine log file based on level
        $logFile = match($level) {
            self::SECURITY => self::$logDir . "/security-{$date}.log",
            self::ERROR, self::CRITICAL => self::$logDir . "/error-{$date}.log",
            default => self::$logDir . "/app-{$date}.log"
        };

        // Write to log file
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

        // Also write critical errors to PHP error log
        if ($level === self::CRITICAL || $level === self::ERROR) {
            error_log("[{$level}] {$message}");
        }
    }

    /**
     * Log debug message
     */
    public static function debug(string $message, array $context = []) {
        self::log(self::DEBUG, $message, $context);
    }

    /**
     * Log info message
     */
    public static function info(string $message, array $context = []) {
        self::log(self::INFO, $message, $context);
    }

    /**
     * Log warning message
     */
    public static function warning(string $message, array $context = []) {
        self::log(self::WARNING, $message, $context);
    }

    /**
     * Log error message
     */
    public static function error(string $message, array $context = []) {
        self::log(self::ERROR, $message, $context);
    }

    /**
     * Log critical error message
     */
    public static function critical(string $message, array $context = []) {
        self::log(self::CRITICAL, $message, $context);
    }

    /**
     * Log security event
     */
    public static function security(string $message, array $context = []) {
        self::log(self::SECURITY, $message, $context);
    }

    /**
     * Log order event
     */
    public static function order(int $orderId, string $message, array $context = []) {
        self::info("Order #{$orderId}: {$message}", $context);
    }

    /**
     * Log payment event
     */
    public static function payment(int $orderId, string $message, array $context = []) {
        $context['order_id'] = $orderId;
        self::info("Payment: {$message}", $context);
    }

    /**
     * Log admin action
     */
    public static function admin(string $action, array $context = []) {
        $adminId = $_SESSION['admin_id'] ?? 'unknown';
        $context['admin_id'] = $adminId;
        self::info("Admin action: {$action}", $context);
    }

    /**
     * Log user action
     */
    public static function user(string $action, array $context = []) {
        $userId = $_SESSION['user_id'] ?? 'guest';
        $context['user_id'] = $userId;
        self::info("User action: {$action}", $context);
    }

    /**
     * Archive old log files
     *
     * Moves log files older than specified days to an archive folder
     * instead of deleting them.
     *
     * @param int $days Number of days to keep logs in main directory (default 30)
     * @return array ['archived' => count, 'archive_dir' => path]
     */
    public static function cleanup(int $days = 30) {
        self::init();

        // Create archive directory if it doesn't exist
        $archiveDir = self::$logDir . '/archive';
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0755, true);
        }

        $cutoffTime = time() - ($days * 86400);
        $files = glob(self::$logDir . '/*.log');
        $archivedCount = 0;

        foreach ($files as $file) {
            $filename = basename($file);

            // Skip files that are already in archive or are .gitignore/.gitkeep
            if (strpos($file, '/archive/') !== false || $filename[0] === '.') {
                continue;
            }

            if (filemtime($file) < $cutoffTime) {
                $destination = $archiveDir . '/' . $filename;

                // If file already exists in archive, append timestamp
                if (file_exists($destination)) {
                    $destination = $archiveDir . '/' . pathinfo($filename, PATHINFO_FILENAME) .
                                 '-' . filemtime($file) . '.log';
                }

                if (rename($file, $destination)) {
                    $archivedCount++;
                }
            }
        }

        return [
            'archived' => $archivedCount,
            'archive_dir' => $archiveDir
        ];
    }

    /**
     * Get recent log entries
     *
     * @param string $type Log type (app, error, security)
     * @param int $lines Number of lines to return
     * @return array Log entries
     */
    public static function getRecent(string $type = 'app', int $lines = 100): array {
        self::init();

        $date = date('Y-m-d');
        $logFile = self::$logDir . "/{$type}-{$date}.log";

        if (!file_exists($logFile)) {
            return [];
        }

        $entries = [];
        $file = new SplFileObject($logFile);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key() + 1;

        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);

        while (!$file->eof()) {
            $line = trim($file->current());
            if (!empty($line)) {
                $entries[] = $line;
            }
            $file->next();
        }

        return $entries;
    }
}

/**
 * Helper function for quick logging
 */
function log_message(string $level, string $message, array $context = []) {
    Logger::log($level, $message, $context);
}

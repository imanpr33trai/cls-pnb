<?php
// /config/debug.php (Complete, Self-Contained Debugging System)

// =================================================================
//  PHP DEBUGGING & ERROR HANDLING SETUP
// =================================================================

// --- MASTER SWITCH: Set to `false` when your site goes live ---
define('IS_DEVELOPMENT_MODE', true);

// --- LOG FILE SETUP ---
define('DEBUG_LOG_FILE', __DIR__ . '/../logs/debug.log');
ini_set('log_errors', '1');
ini_set('error_log', DEBUG_LOG_FILE);
ini_set('display_errors', '0'); // We use our own handler for display
error_reporting(E_ALL);

// --- CONSOLE COMMUNICATION CORE ---

/**
 * A self-contained function to send data to the browser console.
 * It's the engine for all other debug functions.
 */
function _send_to_console(array $payload) {
    if (!IS_DEVELOPMENT_MODE || headers_sent()) {
        return;
    }
    $json_payload = json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    if ($json_payload === false) {
        $json_payload = json_encode(['JSON_ENCODE_ERROR' => json_last_error_msg()]);
    }
    // This is a self-invoking JS function to avoid polluting the global scope
    echo "<script>(function(){
        const payload = {$json_payload};
        const label = payload.label || 'PHP Debug';
        const color = payload.color || '#1e90ff';
        const timestamp = new Date().toLocaleTimeString();
        
        console.groupCollapsed(`%c[${label}] %c@ ${timestamp}`, `color: ${color}; font-weight: bold;`, 'color: gray; font-weight: normal;');
        if (payload.data && payload.data.JSON_ENCODE_ERROR) {
            console.warn('PHP data could not be JSON encoded:', payload.data.JSON_ENCODE_ERROR);
        } else {
            console.dir(payload.data);
        }
        
        if (payload.trace) {
            console.groupCollapsed('Stack Trace');
            console.log(payload.trace);
            console.groupEnd();
        }
        console.groupEnd();
    })();</script>";
}

// --- PHP ERROR AND EXCEPTION HANDLERS ---

/**
 * Catches all PHP errors, warnings, and notices.
 */
function custom_error_handler(int $errno, string $errstr, string $errfile, int $errline): bool {
    $log_message = "PHP Error: [{$errno}] {$errstr} in {$errfile} on line {$errline}";
    error_log($log_message);

    _send_to_console([
        'label' => 'PHP Error',
        'color' => '#dc3545',
        'data' => [
            'message' => $errstr,
            'file' => "{$errfile} on line {$errline}",
            'level' => $errno
        ]
    ]);
    return true; // Stop PHP's default handler
}

/**
 * Catches all uncaught exceptions.
 */
function custom_exception_handler(Throwable $exception): void {
    $log_message = "Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($log_message);
    error_log("Stack Trace:\n" . $exception->getTraceAsString());
    
    _send_to_console([
        'label' => 'PHP FATAL EXCEPTION',
        'color' => '#d8000c',
        'data' => [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile() . ' on line ' . $exception->getLine(),
        ],
        'trace' => $exception->getTraceAsString()
    ]);
}

/**
 * Catches fatal errors that other handlers miss.
 */
function custom_shutdown_handler(): void {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        // We can't send to console here as output has likely closed.
        // But the error is already logged to the file by `error_log`.
        // This ensures fatal errors are always recorded.
    }
}

set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');
register_shutdown_function('custom_shutdown_handler');


// --- PUBLIC-FACING DEBUG FUNCTIONS ---

/**
 * The main function you will use to debug variables on normal pages.
 */
function console_log($data, string $label = 'PHP Debug') {
    _send_to_console(['label' => $label, 'data' => $data]);
}

/**
 * The main function you will use to debug variables on pages that redirect.
 */
function debug_to_session($data, string $label = 'Redirect Debug') {
    if (!IS_DEVELOPMENT_MODE || session_status() !== PHP_SESSION_ACTIVE) return;
    if (!isset($_SESSION['php_to_console_debug'])) $_SESSION['php_to_console_debug'] = [];
    $_SESSION['php_to_console_debug'][] = ['label' => $label, 'data' => $data];
}

/**
 * Call this in your footer to print debug messages from the previous page.
 */
function render_session_debug_to_console() {
    if (!IS_DEVELOPMENT_MODE || empty($_SESSION['php_to_console_debug'])) return;
    foreach ($_SESSION['php_to_console_debug'] as $msg) {
        console_log($msg['data'], $msg['label'] . ' (from previous request)');
    }
    unset($_SESSION['php_to_console_debug']); // Clear after rendering
}

// --- AUTOMATIC SUPERGLOBAL LOGGING ---
if (IS_DEVELOPMENT_MODE) {
    if (!empty($_GET)) {
        console_log($_GET, '$_GET Data');
    }
    if (!empty($_POST)) {
        console_log($_POST, '$_POST Data');
    }
    if (!empty($_FILES)) {
        console_log($_FILES, '$_FILES Data');
    }
}
?>


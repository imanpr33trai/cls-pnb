<?php
// /config/debug.php (Complete, Self-Contained Debugging System)

// =================================================================
//  PHP DEBUGGING & ERROR HANDLING SETUP
// =================================================================

// --- MASTER SWITCH: Set to `false` when your site goes live ---
define('IS_DEVELOPMENT_MODE', false);

// --- LOG FILE SETUP ---
define('DEBUG_LOG_FILE', __DIR__ . '/../logs/debug.log');

// Auto-create logs directory if it doesn't exist
// $log_dir = dirname(DEBUG_LOG_FILE);
// if (!is_dir($log_dir)) {
//     // Attempt to create the directory with permissions that allow the web server to write to it.
//     // The umask will be applied, so 0777 usually results in 0755.
//     if (!mkdir($log_dir, 0777, true) && !is_dir($log_dir)) {
//         // If directory creation fails, trigger an error. This is a configuration problem.
//         trigger_error("Failed to create log directory: {$log_dir}", E_USER_WARNING);
//     }
// }

ini_set('log_errors', '1');
ini_set('error_log', DEBUG_LOG_FILE);
// We use our own handlers for displaying errors, so PHP's display can be off.
// This prevents duplicate error messages if the handler fails.
ini_set('display_errors', '1');
error_reporting(E_ALL);

// --- CONSOLE COMMUNICATION CORE ---

/**
 * A self-contained function to send data to the browser console.
 * It's the engine for all other debug functions.
 */
function _send_to_console(array $payload)
{
    if (!IS_DEVELOPMENT_MODE || headers_sent() || PHP_SAPI === 'cli') {
        return;
    }
    $json_payload = json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    if ($json_payload === false) {
        $json_payload = json_encode(['JSON_ENCODE_ERROR' => json_last_error_msg()]);
    }
    echo "<script>(function(){
        const payload = {$json_payload};
        const label = payload.label || 'PHP Debug';
        const color = payload.color || '#1e90ff';
        const timestamp = new Date().toLocaleTimeString();
        
        console.groupCollapsed(`%c[\${label}] %c@ \${timestamp}`, `color: \${color}; font-weight: bold;`, 'color: gray; font-weight: normal;');
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

/**
 * Displays a formatted error message directly on the web page.
 */
function _display_error_on_page(string $title, string $message, string $file, int $line, ?string $trace = null)
{
    if (PHP_SAPI === 'cli' || headers_sent()) {
        return; // Don't display HTML in CLI mode or if headers are already sent.
    }

    echo '<div style="border: 2px solid #dc3545; background-color: #f8d7da; color: #721c24; padding: 15px; margin: 15px; font-family: monospace; z-index: 99999; position: relative;">';
    echo '<h2 style="color: #721c24; margin-top: 0;">' . htmlspecialchars($title) . '</h2>';
    echo '<p style="margin-bottom: 10px;"><strong>Message:</strong> ' . htmlspecialchars($message) . '</p>';
    echo '<p style="margin-bottom: 10px;"><strong>Location:</strong> ' . htmlspecialchars($file) . ' (Line: ' . $line . ')</p>';

    if ($trace) {
        echo '<h3 style="margin-bottom: 5px;">Stack Trace:</h3><pre style="white-space: pre-wrap; background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 0; overflow-x: auto;">' . htmlspecialchars($trace) . '</pre>';
    }

    echo '</div>';
}


// --- PHP ERROR AND EXCEPTION HANDLERS ---

/**
 * Catches all PHP errors, warnings, and notices.
 * Logs to file, sends to console, and displays on page.
 */
function custom_error_handler(int $errno, string $errstr, string $errfile, int $errline): bool
{
    $log_message = "PHP Error: [{$errno}] {$errstr} in {$errfile} on line {$errline}";
    error_log($log_message);

    if (IS_DEVELOPMENT_MODE) {
        _display_error_on_page('PHP Error', $errstr, $errfile, $errline);
        _send_to_console([
            'label' => 'PHP Error',
            'color' => '#dc3545',
            'data' => [
                'message' => $errstr,
                'file' => "{$errfile} on line {$errline}",
                'level' => $errno
            ]
        ]);
    }
    // Prevent PHP's default handler from running, as we have our own display logic.
    return true;
}

/**
 * Catches all uncaught exceptions.
 * Logs to file, sends to console, displays on page, and halts execution.
 */
function custom_exception_handler(Throwable $exception): void
{
    $log_message = "Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($log_message);
    error_log("Stack Trace:\n" . $exception->getTraceAsString());

    if (IS_DEVELOPMENT_MODE) {
        _display_error_on_page(
            'Uncaught Exception',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
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

    // Halt script execution after a fatal exception.
    exit(1);
}

/**
 * Catches fatal errors that other handlers miss (e.g., parse errors).
 * Logs to file and attempts to display on page.
 */
function custom_shutdown_handler(): void
{
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        $log_message = "PHP Fatal Error: [{$error['type']}] {$error['message']} in {$error['file']} on line {$error['line']}";
        error_log($log_message);

        if (IS_DEVELOPMENT_MODE) {
            // Attempt to display the error. This might not always work if output has already started.
            _display_error_on_page(
                'PHP Fatal Error',
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }
}

// Only set up the handlers and automatic logging if this is NOT an AJAX request.
// if (!defined('AJAX_REQUEST')) {
//     set_error_handler('custom_error_handler');
//     set_exception_handler('custom_exception_handler');
//     register_shutdown_function('custom_shutdown_handler');

//     // --- AUTOMATIC SUPERGLOBAL LOGGING ---
//     if (IS_DEVELOPMENT_MODE) {
//         if (!empty($_GET)) {
//             console_log($_GET, '$_GET Data');
//         }
//         if (!empty($_POST)) {
//             console_log($_POST, '$_POST Data');
//         }
//         if (!empty($_FILES)) {
//             console_log($_FILES, '$_FILES Data');
//         }
//     }
// }
?>
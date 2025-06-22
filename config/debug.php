<?php
// /config/debug.php (Enhanced Version)

// =================================================================
//  PHP DEBUGGING & ERROR HANDLING SETUP
// =================================================================

// --- Core Settings ---
define('IS_DEVELOPMENT_MODE', true);
define('DEBUG_LOG_FILE', __DIR__ . '/../logs/debug.log');

// --- Error and Exception Handling ---
ini_set('display_errors', '0'); // We use our custom handler, so disable default display.
ini_set('log_errors', '1'); // Log errors, but not to the default file.
ini_set('error_log', DEBUG_LOG_FILE); // Send PHP's internal logs here too.
error_reporting(E_ALL);

/**
 * Custom error handler to format errors and log them.
 */
function customErrorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
{
    $message = "Error: [{$errno}] {$errstr} in {$errfile} on line {$errline}";
    error_log($message); // Use the built-in logger which respects error_log directive

    if (IS_DEVELOPMENT_MODE && !headers_sent()) {
        http_response_code(500);
        echo "<div style='position: fixed; top: 0; left: 0; width: 100%; padding: 15px; background-color: #ffbaba; border-bottom: 2px solid #d8000c; color: #d8000c; font-family: monospace; z-index: 9999;'>";
        echo "<b>PHP Error:</b> " . htmlspecialchars($errstr, ENT_QUOTES, 'UTF-8') . "<br>";
        echo "<b>File:</b> " . htmlspecialchars($errfile, ENT_QUOTES, 'UTF-8') . " on line <b>{$errline}</b>";
        echo "</div>";
    }
    // Returning false lets PHP's internal error handler continue, which we want for logging.
    // To completely silence it, return true.
    return false;
}

/**
 * Custom exception handler to format exceptions and log them.
 */
function customExceptionHandler(Throwable $exception): void
{
    $message = "Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($message);
    error_log("Stack Trace: " . $exception->getTraceAsString());

    if (IS_DEVELOPMENT_MODE && !headers_sent()) {
        http_response_code(500);
        echo "<div style='position: fixed; top: 0; left: 0; width: 100%; padding: 15px; background-color: #ffbaba; border-bottom: 2px solid #d8000c; color: #d8000c; font-family: monospace; z-index: 9999;'>";
        echo "<b>PHP Uncaught Exception:</b> " . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . "<br>";
        echo "<b>File:</b> " . htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8') . " on line <b>" . $exception->getLine() . "</b>";
        echo "<pre style='white-space: pre-wrap; word-wrap: break-word; margin-top: 10px;'>" . htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "</div>";
    }
}

set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// --- JavaScript Console Logging Functions ---

/**
 * Logs a PHP variable directly to the browser's console.
 * Use this on pages that render HTML and do NOT redirect.
 */
function console_log($data, string $label = 'PHP Debug'): void
{
    if (!IS_DEVELOPMENT_MODE)
        return;
    $json_data = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    if ($json_data === false) {
        $json_data = json_encode("JSON Error: " . json_last_error_msg());
    }
    echo "<script>
        console.groupCollapsed('{$label} (at " . date('H:i:s') . ")');
        console.log({$json_data});
        console.groupEnd();
    </script>";
}

/**
 * [NEW] Stores debug data in the session to be displayed on the NEXT page load.
 * Use this in scripts that will perform a header() redirect.
 */
function debug_to_session($data, string $label = 'Redirect Debug')
{
    if (!IS_DEVELOPMENT_MODE)
        return;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        error_log("Debug Warning: Cannot debug to session because session is not active.");
        return;
    }
    if (!isset($_SESSION['debug_messages'])) {
        $_SESSION['debug_messages'] = [];
    }
    // Use print_r for objects/arrays to get a string representation
    $output = is_string($data) ? $data : print_r($data, true);
    $_SESSION['debug_messages'][] = ['label' => $label, 'data' => $output];
}

/**
 * [NEW] Renders all debug messages from the session into the console.
 * Call this function just before the closing </body> tag in a common footer.
 */
function render_session_debug_to_console(): void
{
    if (!IS_DEVELOPMENT_MODE || empty($_SESSION['debug_messages'])) {
        return;
    }

    echo "<script>";
    echo "console.group('%cDebug Logs from Previous Request', 'color: #1e90ff; font-weight: bold;');";
    foreach ($_SESSION['debug_messages'] as $msg) {
        $label = addslashes($msg['label']);
        // Encode data to be a valid JavaScript string literal
        $data_json = json_encode($msg['data']);
        echo "console.log('%c{$label}:', 'font-weight: bold;', {$data_json});";
    }
    echo "console.groupEnd();";
    echo "</script>";

    // Clear messages after displaying them
    unset($_SESSION['debug_messages']);
}

// --- End of Debug Setup ---
?>
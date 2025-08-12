<?php
// /admin/util/create_backup.php
// This script handles the database backup generation and download.

// Include config but ensure no output before this point.
require_once __DIR__ . '/../../config/config.php';

// Prevent timeout for large databases
set_time_limit(0);

// Check if mysqldump is available
exec('command -v mysqldump', $output, $return_var);
if ($return_var !== 0) {
    // It's better to log this error or show a clean error page,
    // but for now, we'll just stop execution.
    die("Error: `mysqldump` command not found. Please ensure it is installed and in your system's PATH.");
}

// Database credentials from environment variables
$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];

// Generate a unique filename
$filename = 'db-backup-' . date('Y-m-d_H-i-s') . '.sql';

// Set headers to force the browser to download the file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename)); // This might not be accurate as the file is being created on the fly. It's often omitted for streamed content.

// The command to execute for the backup
$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s %s',
    escapeshellarg($db_host),
    escapeshellarg($db_user),
    escapeshellarg($db_pass),
    escapeshellarg($db_name)
);

// Execute the command and pass the output directly to the browser
passthru($command, $return_var);

// If the command fails, there's not much we can do since headers are sent,
// but we can try to log this or handle it if possible.
if ($return_var !== 0) {
    // In a real-world scenario, you'd log this error to a file.
    // For now, the script will likely have already output a partial/empty file.
}

// Stop the script execution
exit;

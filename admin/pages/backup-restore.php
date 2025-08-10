<?php
// /admin/pages/backup-restore.php
require_once __DIR__ . '/../../config/config.php';

// Handle the backup request
if (isset($_GET['action']) && $_GET['action'] === 'backup_db') {
    // Prevent timeout
    set_time_limit(0);

    // Check if mysqldump is available
    exec('command -v mysqldump', $output, $return_var);
    if ($return_var !== 0) {
        die("Error: `mysqldump` command not found. Please ensure it is installed and in your system's PATH.");
    }

    $db_host = DB_HOST;
    $db_user = DB_USER;
    $db_pass = DB_PASS;
    $db_name = DB_NAME;
    
    $filename = 'db-backup-' . date('Y-m-d_H-i-s') . '.sql';
    
    // Set headers to force download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    // The command to execute
    $command = sprintf(
        'mysqldump --host=%s --user=%s --password=%s %s',
        escapeshellarg($db_host),
        escapeshellarg($db_user),
        escapeshellarg($db_pass),
        escapeshellarg($db_name)
    );

    // Execute the command and pass through the output directly to the browser
    passthru($command, $return_var);

    if ($return_var !== 0) {
        // If passthru fails, it might not show an error if headers are already sent.
        // This is a fallback, though it may not be displayed to the user.
        die("Error creating database backup. Exit code: " . $return_var);
    }
    
    exit;
}
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Backup & Restore</h2>
        <p class="mt-2 text-gray-600">Create backups of your database.</p>

        <div class="mt-8 p-6 bg-white rounded-lg shadow max-w-md">
            <h3 class="text-lg font-semibold mb-4">Database Backup</h3>
            <p class="text-gray-600 mb-4">
                Click the button below to download a full backup of your site's database as a <code>.sql</code> file. 
                Keep this file in a safe place.
            </p>
            <a href="?action=backup_db" 
               class="w-full text-center bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-block">
                Download Database Backup
            </a>
        </div>

        <div class="mt-8 p-6 bg-white rounded-lg shadow max-w-md">
            <h3 class="text-lg font-semibold mb-4">Restore</h3>
            <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                <p class="font-bold">Manual Restore Required</p>
                <p>Restoring from a backup must be done manually using a database management tool like phpMyAdmin or the command line to prevent accidental data loss.</p>
            </div>
        </div>
    </div>
</div>

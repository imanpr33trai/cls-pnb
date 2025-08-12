<?php




require_once __DIR__ . '/../../config/config.php';


set_time_limit(0);


exec('command -v mysqldump', $output, $return_var);
if ($return_var !== 0) {
    
    
    die("Error: `mysqldump` command not found. Please ensure it is installed and in your system's PATH.");
}


$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];


$filename = 'db-backup-' . date('Y-m-d_H-i-s') . '.sql';


header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename)); 


$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s %s',
    escapeshellarg($db_host),
    escapeshellarg($db_user),
    escapeshellarg($db_pass),
    escapeshellarg($db_name)
);


passthru($command, $return_var);



if ($return_var !== 0) {
    
    
}


exit;

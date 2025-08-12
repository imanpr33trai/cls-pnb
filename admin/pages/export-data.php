<?php
require_once __DIR__ . '/../../config/config.php';

function output_csv($filename, $data) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    if (empty($data)) {
        return;
    }
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, array_keys($data[0]));
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}


if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    
    if ($export_type === 'users') {
        $stmt = $conn->query("SELECT id, first_name, last_name, email, country, status, created_at FROM users");
        $data = $stmt->fetch_all(MYSQLI_ASSOC);
        output_csv('users-export-' . date('Y-m-d') . '.csv', $data);
    }
    
    if ($export_type === 'subscribers') {
        $stmt = $conn->query("SELECT id, email, created_at FROM subscribers");
        $data = $stmt->fetch_all(MYSQLI_ASSOC);
        output_csv('subscribers-export-' . date('Y-m-d') . '.csv', $data);
    }

    if ($export_type === 'ads') {
        $stmt = $conn->query("SELECT * FROM ad_form");
        $data = $stmt->fetch_all(MYSQLI_ASSOC);
        output_csv('ads-export-' . date('Y-m-d') . '.csv', $data);
    }
}
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Export Data</h2>
        <p class="mt-2 text-gray-600">Download your site's data in CSV format.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Export Users</h3>
                <p class="text-gray-600 mb-4">Download a CSV file of all registered users.</p>
                <a href="?export=users" class="w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                    Export Users
                </a>
            </div>

            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Export Subscribers</h3>
                <p class="text-gray-600 mb-4">Download a CSV file of all newsletter subscribers.</p>
                <a href="?export=subscribers" class="w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                    Export Subscribers
                </a>
            </div>

            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Export Ads</h3>
                <p class="text-gray-600 mb-4">Download a CSV file of all ads.</p>
                <a href="?export=ads" class="w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                    Export Ads
                </a>
            </div>
        </div>
    </div>
</div>

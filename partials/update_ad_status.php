<?php
    
    

    
include '../config/config.php';

    
    
$sql = "UPDATE ad_form 
        SET status = 'expired' 
        WHERE status = 'live' AND expires_at <= NOW()";

$stmt = $conn->prepare($sql);

if ($stmt->execute()) {
        
        
    $updated_count = $stmt->affected_rows;
    error_log(date('Y-m-d H:i:s') . " - Ad Expiration Cron Job: Successfully updated $updated_count ads to 'expired'.\n", 3, "cron_log.log");
} else {
        
    error_log(date('Y-m-d H:i:s') . " - Ad Expiration Cron Job: FAILED. Error: " . $stmt->error . "\n", 3, "cron_log.log");
}

    
    

echo "Ad status update complete. Updated $updated_count ads.";

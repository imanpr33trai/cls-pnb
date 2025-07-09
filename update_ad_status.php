<?php
// This script is meant to be run by a cron job, not a user.
// It connects to the DB and updates the status of any expired ads.

// Include your database configuration
include 'config/config.php';

// Prepare the SQL UPDATE statement.
// Find all ads that are currently 'live' but their expiration date is in the past.
$sql = "UPDATE ad_form 
        SET status = 'expired' 
        WHERE status = 'live' AND expires_at <= NOW()";

$stmt = $conn->prepare($sql);

if ($stmt->execute()) {
    // Optional: Log the success
    // The affected_rows property tells you how many ads were updated.
    $updated_count = $stmt->affected_rows;
    error_log(date('Y-m-d H:i:s') . " - Ad Expiration Cron Job: Successfully updated $updated_count ads to 'expired'.\n", 3, "cron_log.log");
} else {
    // Optional: Log any errors
    error_log(date('Y-m-d H:i:s') . " - Ad Expiration Cron Job: FAILED. Error: " . $stmt->error . "\n", 3, "cron_log.log");
}

$stmt->close();
$conn->close();

echo "Ad status update complete. Updated $updated_count ads.";
?>
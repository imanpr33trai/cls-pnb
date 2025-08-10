<?php
// /admin/pages/send-newsletter.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle newsletter submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_newsletter'])) {
    $subject = trim($_POST['subject']);
    $message = $_POST['message'];

    if (empty($subject) || empty($message)) {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Subject and message cannot be empty.</div>";
    } else {
        // Fetch all subscriber emails
        $stmt = $conn->prepare("SELECT email FROM subscribers");
        $stmt->execute();
        $result = $stmt->get_result();
        $subscribers = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (empty($subscribers)) {
            echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4'>⚠️ There are no subscribers to send a newsletter to.</div>";
        } else {
            $mail = new PHPMailer(true);

            // Fetch SMTP settings from the database
            $settings_stmt = $conn->prepare("SELECT smtp_host, smtp_port, smtp_secure, smtp_user, smtp_pass, smtp_from_email, smtp_from_name FROM site_settings WHERE id = 1");
            $settings_stmt->execute();
            $settings_result = $settings_stmt->get_result();
            $smtp_settings = $settings_result->fetch_assoc();
            $settings_stmt->close();

            if (empty($smtp_settings['smtp_host'])) {
                 echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ SMTP settings are not configured. Please configure them in the Email Settings menu.</div>";
                 return;
            }

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = $smtp_settings['smtp_host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtp_settings['smtp_user'];
                $mail->Password   = $smtp_settings['smtp_pass'];
                $mail->SMTPSecure = $smtp_settings['smtp_secure'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $smtp_settings['smtp_port'];

                // Recipients
                $mail->setFrom($smtp_settings['smtp_from_email'], $smtp_settings['smtp_from_name']);
                foreach ($subscribers as $subscriber) {
                    $mail->addBCC($subscriber['email']);
                }

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->AltBody = strip_tags($message);

                $mail->send();
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Newsletter sent successfully to " . count($subscribers) . " subscribers!</div>";
            } catch (Exception $e) {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
            }
        }
    }
}
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <h2 class="text-2xl font-semibold leading-tight">Send Newsletter</h2>
        <p class="mt-2 text-gray-600">Compose and send an email to all your subscribers.</p>
        
        <div class="mt-8 p-6 bg-white rounded-lg shadow">
            <form action="" method="POST" class="max-w-4xl">
                <div class="mb-4">
                    <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Subject:</label>
                    <input type="text" id="subject" name="subject" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-6">
                    <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                    <textarea id="message" name="message" rows="10" required
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    <p class="text-xs text-gray-500 mt-1">You can use HTML tags for formatting.</p>
                </div>
                <div class="flex items-center">
                    <button type="submit" name="send_newsletter"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            onclick="return confirm('Are you sure you want to send this newsletter to all subscribers?');">
                        Send Newsletter
                    </button>
                </div>
            </form>
        </div>
        <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
            <p class="font-bold">Important!</p>
            <p>Please configure your SMTP settings in the file <code>admin/pages/send-newsletter.php</code> before sending emails.</p>
        </div>
    </div>
</div>

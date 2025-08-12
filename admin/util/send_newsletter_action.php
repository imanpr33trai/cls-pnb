<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? 'Newsletter');
    $body = $_POST['body'] ?? '';

    if (empty($subject) || empty($body)) {
        $response['message'] = 'Subject and body cannot be empty.';
        echo json_encode($response);
        exit;
    }

       $stmt = $conn->prepare("SELECT email FROM subscribers");
    $stmt->execute();
    $result = $stmt->get_result();
    $subscribers = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($subscribers)) {
        $response['message'] = 'There are no subscribers to send a newsletter to.';
        echo json_encode($response);
        exit;
    }

       $mail = new PHPMailer(true);
    try {
               $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

               $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        foreach ($subscribers as $subscriber) {
            $mail->addBCC($subscriber['email']);
        }

               $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
               $mail->AltBody = strip_tags($body);

        $mail->send();
        $response['success'] = true;
        $response['message'] = 'Newsletter sent successfully to ' . count($subscribers) . ' subscribers!';

    } catch (Exception $e) {
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>

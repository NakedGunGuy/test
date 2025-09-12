<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require ROOT_PATH . '/vendor/autoload.php';

function render_mail_template(string $template, array $data = []): string
{
    $path = __DIR__ . "/templates/{$template}.php";
    if (!file_exists($path)) {
        throw new RuntimeException("Template '{$template}' not found.");
    }

    extract($data, EXTR_SKIP);
    ob_start();
    include $path;
    return ob_get_clean();
}

function queue_email(
    string $to,
    string $subject,
    string $template,
    array $data,
    string $from = 'noreply@cardpoint.com',
    string $fromName = 'Cardpoint'
): bool {
    $pdo = db();
    
    $stmt = $pdo->prepare("
        INSERT INTO email_queue (to_email, subject, template, data, from_email, from_name)
        VALUES (:to, :subject, :template, :data, :from, :from_name)
    ");
    
    return $stmt->execute([
        ':to' => $to,
        ':subject' => $subject,
        ':template' => $template,
        ':data' => json_encode($data),
        ':from' => $from,
        ':from_name' => $fromName
    ]);
}

function send_mail(
    string $to,
    string $subject,
    string $template,
    array $data,
    string $from = 'noreply@cardpoint.com',
    string $fromName = 'Cardpoint'
): bool {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'localhost';
//        $mail->SMTPAuth   = true;
        $mail->SMTPAuth   = false;
//        $mail->Username   = 'smtp-username';
//        $mail->Password   = 'smtp-password';
//        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // or ENCRYPTION_STARTTLS
        $mail->Port       = 1025; // 465 for SSL

        // Recipients
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = render_mail_template($template, $data);

        return $mail->send();
    } catch (Exception $e) {
        var_dump($mail->ErrorInfo);
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

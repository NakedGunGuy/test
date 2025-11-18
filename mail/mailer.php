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
    ?string $from = null,
    ?string $fromName = null
): bool {
    // Use environment variables for from address/name if not provided
    $from = $from ?? ($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@cardpoint.com');
    $fromName = $fromName ?? ($_ENV['MAIL_FROM_NAME'] ?? 'Cardpoint');
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
    ?string $from = null,
    ?string $fromName = null
): bool {
    $mail = new PHPMailer(true);

    // Use environment variables for from address/name if not provided
    $from = $from ?? ($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@cardpoint.com');
    $fromName = $fromName ?? ($_ENV['MAIL_FROM_NAME'] ?? 'Cardpoint');

    try {
        // Server settings from environment
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'] ?? 'localhost';
        $mail->Port       = $_ENV['MAIL_PORT'] ?? 1025;

        // Authentication
        if (!empty($_ENV['MAIL_USERNAME']) && !empty($_ENV['MAIL_PASSWORD'])) {
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
        } else {
            $mail->SMTPAuth   = false;
        }

        // Encryption (tls, ssl, or none)
        $encryption = $_ENV['MAIL_ENCRYPTION'] ?? '';
        if (strtolower($encryption) === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif (strtolower($encryption) === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }

        // Enable debug output in development
        if ($_ENV['DEBUG'] ?? false) {
            $mail->SMTPDebug = 0; // 0 = off, 2 = detailed
        }

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
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

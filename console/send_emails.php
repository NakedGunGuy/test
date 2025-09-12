<?php
/**
 * Email Queue Processor
 * 
 * This script processes queued emails and sends them out.
 * Run this as a cronjob every minute.
 * 
 * Usage: php console/send_emails.php
 */

require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/autoload.php';
require_once MAIL_PATH . '/mailer.php';

function process_email_queue($limit = 10) {
    $pdo = db();
    
    // Get pending emails to send
    $stmt = $pdo->prepare("
        SELECT * FROM email_queue 
        WHERE status = 'pending' 
        AND attempts < max_attempts
        ORDER BY created_at ASC 
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sent_count = 0;
    $failed_count = 0;
    
    foreach ($emails as $email) {
        echo "Processing email ID {$email['id']} to {$email['to_email']}...\n";
        
        // Mark as processing and increment attempts
        $update_stmt = $pdo->prepare("
            UPDATE email_queue 
            SET attempts = attempts + 1, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $update_stmt->execute([':id' => $email['id']]);
        
        // Decode data and send email
        $data = json_decode($email['data'], true) ?? [];
        
        $success = send_mail(
            $email['to_email'],
            $email['subject'],
            $email['template'],
            $data,
            $email['from_email'],
            $email['from_name']
        );
        
        if ($success) {
            // Mark as sent
            $status_stmt = $pdo->prepare("
                UPDATE email_queue 
                SET status = 'sent', sent_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            $status_stmt->execute([':id' => $email['id']]);
            
            echo "✅ Email sent successfully\n";
            $sent_count++;
        } else {
            // Check if max attempts reached
            if ($email['attempts'] + 1 >= $email['max_attempts']) {
                $status_stmt = $pdo->prepare("
                    UPDATE email_queue 
                    SET status = 'failed', error_message = 'Max attempts reached', updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                ");
                $status_stmt->execute([':id' => $email['id']]);
                echo "❌ Email failed permanently (max attempts reached)\n";
            } else {
                echo "⚠️ Email failed, will retry later\n";
            }
            $failed_count++;
        }
        
        // Small delay to prevent overwhelming the mail server
        usleep(100000); // 0.1 seconds
    }
    
    return [
        'processed' => count($emails),
        'sent' => $sent_count,
        'failed' => $failed_count
    ];
}

function cleanup_old_emails($days = 7) {
    $pdo = db();
    
    $stmt = $pdo->prepare("
        DELETE FROM email_queue 
        WHERE (status = 'sent' OR status = 'failed') 
        AND created_at < date('now', '-{$days} days')
    ");
    $stmt->execute();
    
    return $stmt->rowCount();
}

// Main execution
echo "Starting email queue processor...\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";

try {
    $results = process_email_queue(10); // Process up to 10 emails per run
    
    echo "Results:\n";
    echo "- Processed: {$results['processed']}\n";
    echo "- Sent: {$results['sent']}\n";
    echo "- Failed: {$results['failed']}\n";
    
    // Cleanup old emails once per hour (when minute is 0)
    if (date('i') === '00') {
        $cleaned = cleanup_old_emails(7);
        echo "- Cleaned up {$cleaned} old email records\n";
    }
    
    echo "Email processing complete.\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
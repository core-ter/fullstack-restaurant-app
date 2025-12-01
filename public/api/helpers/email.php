<?php
/**
 * Email Helper - PHPMailer wrapper for sending emails
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';

/**
 * Send an email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject  
 * @param string $body HTML email body
 * @param string $toName Recipient name (optional)
 * @return bool Success status
 */
function sendEmail($to, $subject, $body, $toName = '') {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USERNAME');
        $mail->Password = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getenv('SMTP_PORT') ?: 587;
        $mail->CharSet = 'UTF-8';
        
        // Recipients
        $mail->setFrom(
            getenv('SMTP_FROM_EMAIL'),
            getenv('SMTP_FROM_NAME') ?: 'Debreceni Étterem'
        );
        $mail->addAddress($to, $toName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email send error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Send email verification email
 * 
 * @param string $email User email
 * @param string $name User first name
 * @param string $token Verification token
 * @return bool Success status
 */
function sendVerificationEmail($email, $name, $token) {
    $appUrl = getenv('APP_URL') ?: 'http://localhost:8000';
    $verifyUrl = $appUrl . '/verify-email.php?token=' . urlencode($token);
    
    $subject = 'Email cím megerősítés - Debreceni Étterem';
    
    $body = "
    <!DOCTYPE html>
    <html lang='hu'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #e63946 0%, #c62a35 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .button { display: inline-block; background: #e63946; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Üdvözlünk a Debreceni Étteremnél!</h1>
            </div>
            <div class='content'>
                <p>Kedves {$name}!</p>
                
                <p>Köszönjük a regisztrációt! Kattints az alábbi gombra az email címed megerősítéséhez:</p>
                
                <p style='text-align: center;'>
                    <a href='{$verifyUrl}' class='button'>Email cím megerősítése</a>
                </p>
                
                <p>Vagy másold be ezt a linket a böngésződbe:</p>
                <p style='word-break: break-all; background: #eee; padding: 10px; border-radius: 5px;'>{$verifyUrl}</p>
                
                <p>Ha nem te regisztráltál, kérjük hagyd figyelmen kívül ezt az emailt.</p>
                
                <p>Üdvözlettel,<br>Debreceni Étterem csapata</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Debreceni Étterem. Minden jog fenntartva.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body, $name);
}

/**
 * Send welcome email after successful verification
 * 
 * @param string $email User email
 * @param string $name User first name
 * @return bool Success status
 */
function sendWelcomeEmail($email, $name) {
    $subject = 'Üdvözlünk - Debreceni Étterem';
    
    $body = "
    <!DOCTYPE html>
    <html lang='hu'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #e63946 0%, #c62a35 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Email cím sikeresen megerősítve!</h1>
            </div>
            <div class='content'>
                <p>Kedves {$name}!</p>
                
                <p>Gratulálunk! Az email címed sikeresen megerősítésre került.</p>
                
                <p>Most már teljes mértékben használhatod a fiókod és leadhatod első rendelésed!</p>
                
                <p>Jó étvágyat kívánunk!<br>Debreceni Étterem csapata</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body, $name);
}


/**
 * Send order confirmation email to customer
 * 
 * @param string $email Customer email
 * @param string $name Customer name
 * @param string $orderNumber Order number
 * @param array $orderData Order details
 * @return bool Success status
 */
function sendOrderConfirmationEmail($email, $name, $orderNumber, $orderData) {
    $appUrl = getenv('APP_URL') ?: 'http://localhost:8000';
    $trackingUrl = $appUrl . '/track-order.php?number=' . urlencode($orderNumber);
    
    $subject = 'Rendelés megerősítés - Debreceni Étterem';
    
    $itemsHtml = '';
    foreach ($orderData['items'] as $item) {
        $itemTotal = $item['quantity'] * $item['price'];
        $itemsHtml .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$item['name']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$item['quantity']} db</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>" . number_format($itemTotal, 0, ',', ' ') . " Ft</td>
            </tr>
        ";
    }
    
    $body = "
    <!DOCTYPE html>
    <html lang='hu'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #e63946 0%, #c62a35 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .button { display: inline-block; background: #e63946; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .order-summary { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; }
            table { width: 100%; border-collapse: collapse; }
            .total-row { font-weight: bold; border-top: 2px solid #333; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🍽️ Köszönjük a rendelést!</h1>
            </div>
            <div class='content'>
                <p>Kedves {$name}!</p>
                
                <p>Sikeresen rögzítettük a rendelésedet. Hamarosan megkezdjük az elkészítését!</p>
                
                <div class='order-summary'>
                    <h2 style='margin-top: 0;'>Rendelés #{$orderNumber}</h2>
                    
                    <table>
                        <thead>
                            <tr style='background: #f0f0f0;'>
                                <th style='padding: 10px; text-align: left;'>Tétel</th>
                                <th style='padding: 10px; text-align: center;'>Mennyiség</th>
                                <th style='padding: 10px; text-align: right;'>Ár</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                            <tr>
                                <td colspan='2' style='padding: 10px;'>Szállítási díj:</td>
                                <td style='padding: 10px; text-align: right;'>" . number_format($orderData['delivery_fee'], 0, ',', ' ') . " Ft</td>
                            </tr>
                            <tr class='total-row'>
                                <td colspan='2' style='padding: 15px 10px;'>Végösszeg:</td>
                                <td style='padding: 15px 10px; text-align: right;'>" . number_format($orderData['total'], 0, ',', ' ') . " Ft</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p><strong>Szállítási cím:</strong><br>{$orderData['delivery_address']}</p>
                    <p><strong>Becsült kiszállítás:</strong> kb. " . ($orderData['delivery_time'] ?? 30) . " perc</p>
                </div>
                
                <p style='text-align: center;'>
                    <a href='{$trackingUrl}' class='button'>Rendelés követése</a>
                </p>
                
                <p style='font-size: 14px; color: #666;'>Kövesd nyomon a rendelésedet a fenti linken keresztül. Ezt a linket elmentheted és később is visszatérhetsz rá!</p>
                
                <p>Jó étvágyat kívánunk!<br>Debreceni Étterem csapata</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body, $name);
}


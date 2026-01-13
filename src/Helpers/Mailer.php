<?php
/**
 * Shabab Setif - Mailer Helper
 * 
 * Email sending using PHPMailer with Mailpit support
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Configure PHPMailer for DDEV Mailpit
     */
    private function configure(): void
    {
        // Use SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->Port = SMTP_PORT;
        $this->mailer->SMTPAuth = SMTP_AUTH;

        if (SMTP_AUTH) {
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
        }

        // From address
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

        // Encoding
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Encoding = 'base64';
        $this->mailer->isHTML(true);
    }

    /**
     * Send email
     */
    public function send(string $to, string $subject, string $body, ?string $altBody = null): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $altBody ?? strip_tags($body);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email to new member
     */
    public function sendWelcomeEmail(string $email, string $fullName, string $memberCardId): bool
    {
        $subject = 'مرحباً بك في جمعية شباب سطيف!';

        $body = $this->getEmailTemplate('welcome', [
            'full_name' => $fullName,
            'member_card_id' => $memberCardId,
            'login_link' => APP_URL . '/login'
        ]);

        return $this->send($email, $subject, $body);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email, string $fullName, string $resetToken): bool
    {
        $subject = 'إعادة تعيين كلمة المرور - شباب سطيف';

        $body = $this->getEmailTemplate('password_reset', [
            'full_name' => $fullName,
            'reset_link' => APP_URL . '/reset-password?token=' . $resetToken
        ]);

        return $this->send($email, $subject, $body);
    }

    /**
     * Get email template
     */
    private function getEmailTemplate(string $template, array $data): string
    {
        $templates = [
            'welcome' => $this->welcomeTemplate($data),
            'password_reset' => $this->passwordResetTemplate($data)
        ];

        return $templates[$template] ?? '';
    }

    /**
     * Welcome email template
     */
    private function welcomeTemplate(array $data): string
    {
        return <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; direction: rtl; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .card-id { background: #f8f9fa; border: 2px dashed #667eea; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
        .card-id strong { font-size: 28px; color: #667eea; letter-spacing: 2px; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 مرحباً بك في جمعية شباب سطيف!</h1>
        </div>
        <div class="content">
            <p>أهلاً <strong>{$data['full_name']}</strong>،</p>
            <p>يسعدنا انضمامك إلى عائلة شباب سطيف! أنت الآن جزء من مجتمع نابض بالحياة والنشاط.</p>
            
            <div class="card-id">
                <p style="margin: 0 0 10px; color: #666;">رقم بطاقة العضوية الخاص بك:</p>
                <strong>{$data['member_card_id']}</strong>
                <p style="margin: 10px 0 0; color: #999; font-size: 12px;">استخدم هذا الرقم ككلمة مرور أولية</p>
            </div>
            
            <p>للدخول إلى حسابك:</p>
            <ul>
                <li>البريد الإلكتروني: بريدك الإلكتروني</li>
                <li>كلمة المرور: رقم بطاقة العضوية</li>
            </ul>
            
            <center>
                <a href="{$data['login_link']}" class="btn">تسجيل الدخول الآن</a>
            </center>
        </div>
        <div class="footer">
            <p>© 2025 جمعية شباب سطيف - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Password reset email template
     */
    private function passwordResetTemplate(array $data): string
    {
        return <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; direction: rtl; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .btn { display: inline-block; background: #f5576c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 إعادة تعيين كلمة المرور</h1>
        </div>
        <div class="content">
            <p>مرحباً <strong>{$data['full_name']}</strong>،</p>
            <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p>
            <p>اضغط على الزر أدناه لإعادة تعيين كلمة المرور:</p>
            
            <center>
                <a href="{$data['reset_link']}" class="btn">إعادة تعيين كلمة المرور</a>
            </center>
            
            <p style="margin-top: 30px; color: #999; font-size: 12px;">
                إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد.
                الرابط صالح لمدة ساعة واحدة فقط.
            </p>
        </div>
        <div class="footer">
            <p>© 2025 جمعية شباب سطيف - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Static helper to send email
     */
    public static function sendMail(string $to, string $subject, string $body): bool
    {
        $mailer = new self();
        return $mailer->send($to, $subject, $body);
    }
}

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function kirim_email($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hifzhlyid@gmail.com';
        $mail->Password = 'ejnm hevu hkdl jnxs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('hifzhlyid@gmail.com', 'Hifzhly');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function kirim_kode_verifikasi($email, $nama, $kode) {
    $subject = 'Kode Verifikasi Email - Hifzhly';
    $body = "
    <div style='font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 30px 20px; background: #f7faf8; border-radius: 20px;'>
        <div style='text-align: center; margin-bottom: 24px;'>
            <h2 style='color: #0f172a; margin-top: 10px; font-size: 1.3rem;'>Verifikasi Email</h2>
        </div>
        <p style='color: #475569; font-size: 0.95rem; line-height: 1.6;'>Halo <b>$nama</b>,</p>
        <p style='color: #475569; font-size: 0.95rem; line-height: 1.6;'>Terima kasih telah mendaftar di Hifzhly. Gunakan kode berikut untuk memverifikasi email kamu:</p>
        <div style='text-align: center; margin: 28px 0;'>
            <span style='display: inline-block; background: #ffffff; border: 2px dashed #059669; border-radius: 16px; padding: 16px 40px; font-size: 2.2rem; font-weight: 800; letter-spacing: 10px; color: #059669;'>$kode</span>
        </div>
        <p style='color: #64748b; font-size: 0.85rem;'>Kode ini berlaku selama <b>15 menit</b>. Jika kamu tidak mendaftar di Hifzhly, abaikan email ini.</p>
        <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;'>
        <p style='color: #94a3b8; font-size: 0.75rem; text-align: center;'>&copy; 2026 Hifzhly.</p>
    </div>";
    return kirim_email($email, $subject, $body);
}

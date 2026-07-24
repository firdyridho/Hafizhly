<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function kirim_email($to, $subject, $body)
{
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
        $mail->Timeout = 10;
        $mail->SMTPKeepAlive = false;
        $mail->send();
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

function buat_template_email($judul, $konten)
{
    return '
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
    <body style="margin:0;padding:0;background:#f0f5f2;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Helvetica,Arial,sans-serif;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f5f2;padding:24px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" style="max-width:520px;">
                        <!-- Logo -->
                        <tr>
                            <td align="center" style="padding:0 0 20px;">
                                <table role="presentation" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="background:linear-gradient(135deg,#059669,#047857);border-radius:12px;padding:10px 20px;">
                                            <span style="font-size:1.2rem;font-weight:800;color:#ffffff;letter-spacing:0.5px;">Hifzhly</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- Card -->
                        <tr>
                            <td style="background:#ffffff;border-radius:20px;padding:36px 32px 28px;box-shadow:0 4px 24px rgba(5,150,105,0.08);">
                                <table role="presentation" width="100%">
                                    <tr>
                                        <td style="font-size:1.15rem;font-weight:700;color:#0f172a;padding-bottom:4px;">' . $judul . '</td>
                                    </tr>
                                    <tr><td style="height:1px;background:linear-gradient(90deg,#e5e7eb,#e5e7eb 60%,transparent);margin:14px 0 6px;display:block;"></td></tr>
                                    ' . $konten . '
                                </table>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td align="center" style="padding:20px 0 0;">
                                <p style="margin:0 0 4px;font-size:0.72rem;color:#94a3b8;">&copy; 2026 Hifzhly - Pendamping Murojaah Al-Qur\'an Berbasis AI</p>
                                <p style="margin:0;font-size:0.7rem;color:#b0b8c4;">Email ini dikirim otomatis, harap tidak membalas.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}

function kirim_kode_verifikasi($email, $nama, $kode)
{
    $subject = 'Kode Verifikasi Email - Hifzhly';
    $konten = '
        <tr><td style="padding:12px 0 6px;color:#475569;font-size:0.95rem;line-height:1.7;">Halo <b style="color:#0f172a;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#475569;font-size:0.95rem;line-height:1.7;padding-bottom:4px;">Terima kasih sudah mendaftar di Hifzhly. Silakan verifikasi email kamu dengan kode di bawah ini:</td></tr>
        <tr>
            <td align="center" style="padding:24px 0 20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:#f0fdf6;border-radius:14px;border:1.5px solid #a7f3d0;padding:14px 40px;display:inline-block;">
                    <tr>
                        <td style="font-size:2.2rem;font-weight:800;letter-spacing:10px;color:#059669;font-family:\'Courier New\',monospace;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:#64748b;font-size:0.85rem;line-height:1.6;">Kode ini berlaku selama <b>15 menit</b>. Jika kamu tidak merasa mendaftar, abaikan email ini.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Verifikasi Email', $konten));
}

function kirim_kode_reset($email, $nama, $kode)
{
    $subject = 'Kode Reset Password - Hifzhly';
    $konten = '
        <tr><td style="padding:12px 0 6px;color:#475569;font-size:0.95rem;line-height:1.7;">Halo <b style="color:#0f172a;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#475569;font-size:0.95rem;line-height:1.7;padding-bottom:4px;">Kami menerima permintaan reset password akun Hifzhly kamu. Gunakan kode berikut untuk melanjutkan:</td></tr>
        <tr>
            <td align="center" style="padding:24px 0 20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:#f0fdf6;border-radius:14px;border:1.5px solid #a7f3d0;padding:14px 40px;display:inline-block;">
                    <tr>
                        <td style="font-size:2.2rem;font-weight:800;letter-spacing:10px;color:#059669;font-family:\'Courier New\',monospace;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:#64748b;font-size:0.85rem;line-height:1.6;">Kode ini berlaku selama <b>15 menit</b>. Jika kamu tidak meminta reset password, abaikan email ini.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Reset Password', $konten));
}

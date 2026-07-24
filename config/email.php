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
        $mail->Timeout = 10;
        $mail->SMTPKeepAlive = false;
        $mail->send();
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

function buat_template_email($judul, $konten) {
    return '
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
    <body style="margin:0;padding:0;background:#e8f0ec;font-family:\'Segoe UI\',\'Inter\',Helvetica,Arial,sans-serif;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#e8f0ec;padding:30px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" style="max-width:520px;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(5,150,105,0.12);">
                        <!-- Header gradient -->
                        <tr>
                            <td style="background:linear-gradient(135deg,#059669,#047857);padding:32px 30px 28px;text-align:center;">
                                <table role="presentation" width="100%">
                                    <tr>
                                        <td align="center" style="padding-bottom:14px;">
                                            <table role="presentation" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="background:rgba(255,255,255,0.15);border-radius:14px;padding:10px 14px;">
                                                        <span style="font-size:1.6rem;font-weight:800;color:#ffffff;letter-spacing:1px;">﷽</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <span style="font-size:1.6rem;font-weight:800;color:#ffffff;letter-spacing:1px;">Hifzhly</span>
                                            <span style="display:block;font-size:0.7rem;color:rgba(255,255,255,0.6);letter-spacing:2px;text-transform:uppercase;margin-top:4px;">Pendamping Murojaah Al-Qur\'an</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- Body -->
                        <tr>
                            <td style="padding:34px 30px 20px;">
                                <table role="presentation" width="100%">
                                    <tr>
                                        <td style="font-size:1.2rem;font-weight:700;color:#0f172a;padding-bottom:6px;">' . $judul . '</td>
                                    </tr>
                                    ' . $konten . '
                                </table>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td style="background:#f8fafc;padding:20px 30px;text-align:center;border-top:1px solid #e5e7eb;">
                                <p style="margin:0 0 6px;font-size:0.72rem;color:#94a3b8;">&copy; 2026 Hifzhly &mdash; AI Quran Companion</p>
                                <p style="margin:0;font-size:0.7rem;color:#94a3b8;">Ini adalah email otomatis, mohon tidak membalas.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}

function kirim_kode_verifikasi($email, $nama, $kode) {
    $subject = '✧ Verifikasi Email — Hifzhly';
    $konten = '
        <tr><td style="padding:14px 0 6px;color:#475569;font-size:0.95rem;line-height:1.7;">Halo <b style="color:#0f172a;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#475569;font-size:0.95rem;line-height:1.7;padding-bottom:6px;">Terima kasih sudah mendaftar di Hifzhly. Silakan verifikasi email kamu dengan kode di bawah ini:</td></tr>
        <tr>
            <td align="center" style="padding:22px 0 18px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:#f0fdf6;border-radius:16px;border:2px dashed #059669;padding:18px 42px;display:inline-block;">
                    <tr>
                        <td style="font-size:2.4rem;font-weight:800;letter-spacing:12px;color:#059669;font-family:\'Courier New\',monospace;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:#64748b;font-size:0.85rem;line-height:1.6;">Kode ini berlaku selama <b>15 menit</b>. Jika kamu tidak merasa mendaftar, abaikan email ini.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Verifikasi Email', $konten));
}

function kirim_kode_reset($email, $nama, $kode) {
    $subject = '✧ Reset Password — Hifzhly';
    $konten = '
        <tr><td style="padding:14px 0 6px;color:#475569;font-size:0.95rem;line-height:1.7;">Halo <b style="color:#0f172a;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#475569;font-size:0.95rem;line-height:1.7;padding-bottom:6px;">Kami menerima permintaan reset password akun Hifzhly kamu. Gunakan kode berikut:</td></tr>
        <tr>
            <td align="center" style="padding:22px 0 18px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:#f0fdf6;border-radius:16px;border:2px dashed #059669;padding:18px 42px;display:inline-block;">
                    <tr>
                        <td style="font-size:2.4rem;font-weight:800;letter-spacing:12px;color:#059669;font-family:\'Courier New\',monospace;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:#64748b;font-size:0.85rem;line-height:1.6;">Kode ini berlaku selama <b>15 menit</b>. Jika kamu tidak meminta reset password, abaikan email ini.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Reset Password', $konten));
}

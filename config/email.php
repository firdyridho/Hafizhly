<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            putenv(trim($key) . '=' . $val);
            $_ENV[trim($key)] = $val;
        }
    }
}

function kirim_email($to, $subject, $body)
{
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hifzhlyid@gmail.com';
        $mail->Password = getenv('SMTP_PASSWORD') ?: 'ejnm hevu hkdl jnxs';
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
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light">
    </head>
    <body style="margin:0;padding:0;background:#0b1f16;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Helvetica,Arial,sans-serif;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(160deg,#0b1f16 0%,#0f2b1f 45%,#0b1f16 100%);padding:36px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" style="max-width:540px;">

                        <!-- Header / Logo mark -->
                        <tr>
                            <td align="center" style="padding:0 0 28px;">
                                <table role="presentation" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center" style="padding-bottom:14px;">
                                            <table role="presentation" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width:56px;height:56px;background:linear-gradient(145deg,#10b981,#047857);border-radius:16px;border:1px solid rgba(212,175,55,0.5);box-shadow:0 6px 20px rgba(4,120,87,0.45);" align="center" valign="middle">
                                                        <span style="font-size:1.6rem;line-height:56px;">&#9770;</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <span style="font-size:1.5rem;font-weight:800;color:#f5f9f7;letter-spacing:0.5px;font-family:Georgia,\'Times New Roman\',serif;">Hifzhly</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="padding-top:6px;">
                                            <table role="presentation" cellpadding="0" cellspacing="0" style="background:rgba(212,175,55,0.12);border:1px solid rgba(212,175,55,0.45);border-radius:20px;padding:4px 14px;">
                                                <tr>
                                                    <td style="font-size:0.65rem;font-weight:700;letter-spacing:1.5px;color:#d4af37;text-transform:uppercase;">Pendamping Murojaah AI</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Card -->
                        <tr>
                            <td style="background:#ffffff;border-radius:22px;padding:0;box-shadow:0 20px 45px rgba(0,0,0,0.35);overflow:hidden;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <!-- Gold top accent -->
                                    <tr>
                                        <td style="height:4px;background:linear-gradient(90deg,#047857,#d4af37,#047857);font-size:0;line-height:0;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:34px 32px 30px;">
                                            <table role="presentation" width="100%">
                                                <tr>
                                                    <td style="font-size:1.2rem;font-weight:800;color:#0f172a;padding-bottom:2px;font-family:Georgia,\'Times New Roman\',serif;">' . $judul . '</td>
                                                </tr>
                                                <tr><td style="height:1px;background:linear-gradient(90deg,#d4af37,#e5e7eb 55%,transparent);margin:14px 0 4px;display:block;font-size:0;">&nbsp;</td></tr>
                                                ' . $konten . '
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td align="center" style="padding:26px 0 0;">
                                <p style="margin:0 0 6px;font-size:0.72rem;color:#7fa694;letter-spacing:0.3px;">&copy; 2026 Hifzhly &mdash; Pendamping Murojaah Al-Qur\'an Berbasis AI</p>
                                <p style="margin:0;font-size:0.68rem;color:#4d6b5c;">Email ini dikirim otomatis, harap tidak membalas.</p>
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
        <tr><td style="padding:14px 0 6px;color:#475569;font-size:0.95rem;line-height:1.7;">Assalamu\'alaikum, <b style="color:#0f172a;">' . $nama . '</b> &#128075;</td></tr>
        <tr><td style="color:#475569;font-size:0.95rem;line-height:1.7;padding-bottom:4px;">Terima kasih sudah bergabung di Hifzhly. Satu langkah lagi untuk mulai perjalanan murojaahmu &mdash; masukkan kode berikut untuk verifikasi email:</td></tr>
        <tr>
            <td align="center" style="padding:26px 0 22px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#f0fdf6,#e6f9ef);border-radius:16px;border:1.5px solid #d4af37;padding:16px 44px;display:inline-block;box-shadow:0 8px 24px rgba(16,185,129,0.15);">
                    <tr>
                        <td style="font-size:2.3rem;font-weight:800;letter-spacing:11px;color:#047857;font-family:\'Courier New\',monospace;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="background:#fafbfc;border-left:3px solid #d4af37;border-radius:8px;padding:12px 16px;">
                <span style="color:#64748b;font-size:0.82rem;line-height:1.6;">&#9203; Kode berlaku selama <b style="color:#334155;">15 menit</b>. Jika kamu tidak merasa mendaftar, abaikan email ini dengan aman.</span>
            </td>
        </tr>';
    return kirim_email($email, $subject, buat_template_email('Verifikasi Email Kamu', $konten));
}

function kirim_kode_reset($email, $nama, $kode)
{
    $subject = 'Kode Reset Password - Hifzhly';
    $konten = '
        <tr><td style="padding:14px 0 6px;color:#475569;font-size:0.95rem;line-height:1.7;">Halo, <b style="color:#0f172a;">' . $nama . '</b></td></tr>
        <tr><td style="color:#475569;font-size:0.95rem;line-height:1.7;padding-bottom:4px;">Kami menerima permintaan reset password untuk akun Hifzhly kamu. Gunakan kode berikut untuk melanjutkan:</td></tr>
        <tr>
            <td align="center" style="padding:26px 0 22px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#f0fdf6,#e6f9ef);border-radius:16px;border:1.5px solid #d4af37;padding:16px 44px;display:inline-block;box-shadow:0 8px 24px rgba(16,185,129,0.15);">
                    <tr>
                        <td style="font-size:2.3rem;font-weight:800;letter-spacing:11px;color:#047857;font-family:\'Courier New\',monospace;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="background:#fff8f0;border-left:3px solid #d97706;border-radius:8px;padding:12px 16px;">
                <span style="color:#7c5a1a;font-size:0.82rem;line-height:1.6;">&#128274; Kode berlaku selama <b style="color:#5c4419;">15 menit</b>. Jika kamu tidak meminta reset password, segera abaikan email ini &mdash; akunmu tetap aman.</span>
            </td>
        </tr>';
    return kirim_email($email, $subject, buat_template_email('Reset Password', $konten));
}

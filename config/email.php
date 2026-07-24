<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function kirim_email($to, $subject, $body)
{
    for ($percobaan = 1; $percobaan <= 2; $percobaan++) {
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
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = false;
            $mail->send();
            return true;
        } catch (\Throwable $e) {
            if ($percobaan === 2) {
                return false;
            }
            usleep(500000);
        }
    }
    return false;
}

function buat_template_email($judul, $konten)
{
    return '
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
    <body style="margin:0;padding:0;background:#06120e;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Helvetica,Arial,sans-serif;">
        <div style="background:linear-gradient(170deg,#06120e 0%,#0a1f16 50%,#071410 100%);padding:48px 16px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table role="presentation" width="100%" style="max-width:540px;">

                            <!-- Bismillah -->
                            <tr>
                                <td align="center" style="padding:6px 0 4px;">
                                    <div style="font-family:\'Times New Roman\',\'Traditional Arabic\',serif;font-size:34px;color:#d4a72c;font-weight:400;opacity:0.9;line-height:1.5;letter-spacing:1px;">
                                        بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
                                    </div>
                                </td>
                            </tr>

                            <!-- Decorative line -->
                            <tr>
                                <td align="center" style="padding:20px 0 26px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="width:80px;">
                                        <tr>
                                            <td style="height:1.5px;background:linear-gradient(90deg,transparent,#d4a72c,transparent);"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Brand Badge -->
                            <tr>
                                <td align="center" style="padding-bottom:24px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#0a8a5c,#047857);border-radius:18px;padding:10px 28px;box-shadow:0 4px 24px rgba(5,150,105,0.25),inset 0 1px 0 rgba(255,255,255,0.08);">
                                        <tr>
                                            <td style="font-size:24px;font-weight:900;color:#ffffff;letter-spacing:1.5px;text-align:center;font-family:\'Helvetica Neue\',Arial,sans-serif;">
                                                هِفْظْلِي
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Card -->
                            <tr>
                                <td style="background:linear-gradient(160deg,#0f221c,#0b1d16);border-radius:22px;padding:36px 32px 30px;border:1px solid rgba(212,167,44,0.1);box-shadow:0 8px 48px rgba(0,0,0,0.5);">
                                    <table role="presentation" width="100%">
                                        <tr>
                                            <td style="font-size:18px;font-weight:800;color:#e8e6e1;letter-spacing:0.3px;padding-bottom:2px;">' . $judul . '</td>
                                        </tr>
                                        <tr><td style="height:1px;background:linear-gradient(90deg,rgba(212,167,44,0.25),rgba(212,167,44,0.05),transparent);margin:14px 0 8px;display:block;"></td></tr>
                                        ' . $konten . '
                                    </table>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="padding:28px 0 6px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="width:40px;">
                                        <tr>
                                            <td style="height:1px;background:linear-gradient(90deg,transparent,rgba(212,167,44,0.3),transparent);margin-bottom:16px;"></td>
                                        </tr>
                                    </table>
                                    <p style="margin:12px 0 4px;font-size:11px;color:rgba(255,255,255,0.2);letter-spacing:0.5px;">&copy; 2026 هِفْظْلِي · Pendamping Murojaah Al-Qur\'an Berbasis AI</p>
                                    <p style="margin:0;font-size:10px;color:rgba(255,255,255,0.15);">Email ini dikirim otomatis, harap tidak membalas.</p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
    </html>';
}

function kirim_kode_verifikasi($email, $nama, $kode)
{
    $subject = 'Kode Verifikasi Email - Hifzhly';
    $konten = '
        <tr><td style="padding:8px 0 6px;color:#889996;font-size:14px;line-height:1.8;">Halo <b style="color:#e8e6e1;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.8;padding-bottom:2px;">Terima kasih sudah mendaftar. Yuk verifikasi email kamu agar bisa langsung murojaah:</td></tr>
        <tr>
            <td align="center" style="padding:26px 0 20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(145deg,#0a2218,#0c1f18);border-radius:16px;border:1px solid rgba(5,150,105,0.25);padding:16px 48px;display:inline-block;box-shadow:0 0 40px rgba(5,150,105,0.06);">
                    <tr>
                        <td style="font-size:36px;font-weight:800;letter-spacing:14px;color:#34d399;font-family:\'Courier New\',monospace;text-align:center;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:rgba(255,255,255,0.25);font-size:12px;line-height:1.6;text-align:center;">Kode berlaku <b style="color:#d4a72c;">15 menit</b>. Abaikan jika kamu tidak mendaftar.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Verifikasi Email', $konten));
}

function kirim_kode_reset($email, $nama, $kode)
{
    $subject = 'Kode Reset Password - Hifzhly';
    $konten = '
        <tr><td style="padding:8px 0 6px;color:#889996;font-size:14px;line-height:1.8;">Halo <b style="color:#e8e6e1;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.8;padding-bottom:2px;">Kami menerima permintaan reset password akun Hifzhly kamu. Masukkan kode berikut:</td></tr>
        <tr>
            <td align="center" style="padding:26px 0 20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(145deg,#0a2218,#0c1f18);border-radius:16px;border:1px solid rgba(5,150,105,0.25);padding:16px 48px;display:inline-block;box-shadow:0 0 40px rgba(5,150,105,0.06);">
                    <tr>
                        <td style="font-size:36px;font-weight:800;letter-spacing:14px;color:#34d399;font-family:\'Courier New\',monospace;text-align:center;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:rgba(255,255,255,0.25);font-size:12px;line-height:1.6;text-align:center;">Kode berlaku <b style="color:#d4a72c;">15 menit</b>. Abaikan jika kamu tidak meminta reset.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Reset Password', $konten));
}

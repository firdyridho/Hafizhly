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
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="margin:0;padding:0;background:#06120e;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Helvetica,Arial,sans-serif;">
        <div style="background:linear-gradient(170deg,#06120e 0%,#0a1f16 50%,#071410 100%);padding:40px 16px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table role="presentation" width="100%" style="max-width:560px;">
 
                            <!-- ===== HEADER BANNER ===== -->
                            <tr>
                                <td style="border-radius:24px 24px 0 0;overflow:hidden;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#0d2b1f 0%,#0a8a5c 45%,#047857 100%);position:relative;">
                                        <tr>
                                            <td style="padding:0;">
                                                <!-- Ornamen garis emas atas -->
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr><td style="height:3px;background:linear-gradient(90deg,transparent,#d4a72c,#f2d675,#d4a72c,transparent);"></td></tr>
                                                </table>
 
                                                <!-- Bismillah -->
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="center" style="padding:28px 24px 6px;">
                                                            <div style="font-family:\'Times New Roman\',\'Traditional Arabic\',serif;font-size:30px;color:#f2d675;font-weight:400;opacity:0.95;line-height:1.6;letter-spacing:0.5px;">
                                                                بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
                                                            </div>
                                                            <div style="font-size:10px;color:rgba(255,255,255,0.45);letter-spacing:2px;text-transform:uppercase;margin-top:6px;">
                                                                Dengan Nama Allah Yang Maha Pengasih Lagi Maha Penyayang
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
 
                                                <!-- Ornamen diamond pemisah -->
                                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:18px auto 6px;">
                                                    <tr>
                                                        <td style="width:60px;height:1px;background:linear-gradient(90deg,transparent,rgba(242,214,117,0.5));"></td>
                                                        <td style="padding:0 10px;color:#f2d675;font-size:11px;">✦</td>
                                                        <td style="width:60px;height:1px;background:linear-gradient(90deg,rgba(242,214,117,0.5),transparent);"></td>
                                                    </tr>
                                                </table>
 
                                                <!-- Brand -->
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="center" style="padding:8px 24px 30px;">
                                                            <table role="presentation" cellpadding="0" cellspacing="0" style="background:rgba(6,18,14,0.35);border:1px solid rgba(242,214,117,0.35);border-radius:20px;padding:14px 34px;backdrop-filter:blur(6px);">
                                                                <tr>
                                                                    <td style="text-align:center;">
                                                                        <div style="font-size:11px;color:rgba(255,255,255,0.55);letter-spacing:4px;text-transform:uppercase;margin-bottom:4px;">Assalamu\'alaikum</div>
                                                                        <div style="font-size:26px;font-weight:900;color:#ffffff;letter-spacing:1px;font-family:\'Helvetica Neue\',Arial,sans-serif;">
                                                                            هِفْظْلِي <span style="color:#f2d675;">·</span> Hifzhly
                                                                        </div>
                                                                        <div style="font-size:11px;color:rgba(255,255,255,0.5);letter-spacing:1px;margin-top:4px;">Sahabat Murojaah Al-Qur\'an Kamu</div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
 
                            <!-- ===== CARD KONTEN ===== -->
                            <tr>
                                <td style="background:linear-gradient(160deg,#0f221c,#0b1d16);border-radius:0 0 24px 24px;padding:38px 32px 32px;border:1px solid rgba(212,167,44,0.1);border-top:none;box-shadow:0 12px 56px rgba(0,0,0,0.55);">
                                    <table role="presentation" width="100%">
                                        <tr>
                                            <td style="font-size:19px;font-weight:800;color:#e8e6e1;letter-spacing:0.3px;padding-bottom:2px;">' . $judul . '</td>
                                        </tr>
                                        <tr><td style="height:1px;background:linear-gradient(90deg,rgba(212,167,44,0.3),rgba(212,167,44,0.06),transparent);margin:14px 0 8px;display:block;"></td></tr>
                                        ' . $konten . '
                                    </table>
                                </td>
                            </tr>
 
                            <!-- ===== FOOTER ===== -->
                            <tr>
                                <td align="center" style="padding:30px 0 6px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
                                        <tr>
                                            <td style="width:40px;height:1px;background:linear-gradient(90deg,transparent,rgba(212,167,44,0.35));"></td>
                                            <td style="padding:0 8px;color:rgba(212,167,44,0.5);font-size:10px;">❖</td>
                                            <td style="width:40px;height:1px;background:linear-gradient(90deg,rgba(212,167,44,0.35),transparent);"></td>
                                        </tr>
                                    </table>
                                    <p style="margin:0 0 6px;font-size:11px;color:rgba(255,255,255,0.28);letter-spacing:0.4px;">"Sebaik-baik kalian adalah yang belajar Al-Qur\'an dan mengajarkannya."</p>
                                    <p style="margin:0 0 12px;font-size:10px;color:rgba(255,255,255,0.18);">— HR. Bukhari</p>
                                    <p style="margin:12px 0 4px;font-size:11px;color:rgba(255,255,255,0.2);letter-spacing:0.5px;">&copy; 2026 هِفْظْلِي Hifzhly · Pendamping Murojaah Al-Qur\'an Berbasis AI</p>
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
        <tr><td style="padding:8px 0 6px;color:#889996;font-size:14px;line-height:1.8;">Halo <b style="color:#e8e6e1;">' . $nama . '</b>, selamat datang! 🌙</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.8;padding-bottom:2px;">Satu langkah lagi menuju perjalanan murojaah kamu bersama Hifzhly. Masukkan kode di bawah ini untuk verifikasi email:</td></tr>
        <tr>
            <td align="center" style="padding:26px 0 20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(145deg,#0a2218,#0c1f18);border-radius:16px;border:1px solid rgba(5,150,105,0.25);padding:16px 48px;display:inline-block;box-shadow:0 0 40px rgba(5,150,105,0.08);">
                    <tr>
                        <td style="font-size:36px;font-weight:800;letter-spacing:14px;color:#34d399;font-family:\'Courier New\',monospace;text-align:center;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:rgba(255,255,255,0.25);font-size:12px;line-height:1.6;text-align:center;">Kode berlaku <b style="color:#d4a72c;">15 menit</b>. Abaikan email ini jika kamu tidak mendaftar di Hifzhly.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Verifikasi Email Kamu', $konten));
}

function kirim_kode_reset($email, $nama, $kode)
{
    $subject = 'Kode Reset Password - Hifzhly';
    $konten = '
        <tr><td style="padding:8px 0 6px;color:#889996;font-size:14px;line-height:1.8;">Halo <b style="color:#e8e6e1;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.8;padding-bottom:2px;">Kami menerima permintaan untuk mengatur ulang password akun Hifzhly kamu. Gunakan kode berikut untuk melanjutkan:</td></tr>
        <tr>
            <td align="center" style="padding:26px 0 20px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(145deg,#0a2218,#0c1f18);border-radius:16px;border:1px solid rgba(5,150,105,0.25);padding:16px 48px;display:inline-block;box-shadow:0 0 40px rgba(5,150,105,0.08);">
                    <tr>
                        <td style="font-size:36px;font-weight:800;letter-spacing:14px;color:#34d399;font-family:\'Courier New\',monospace;text-align:center;">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:rgba(255,255,255,0.25);font-size:12px;line-height:1.6;text-align:center;">Kode berlaku <b style="color:#d4a72c;">15 menit</b>. Jika kamu tidak meminta reset password, abaikan saja email ini — akun kamu tetap aman.</td></tr>';
    return kirim_email($email, $subject, buat_template_email('Reset Password Akun', $konten));
}

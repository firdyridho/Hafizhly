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
    <body style="margin:0;padding:0;background:#0a1a12;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Helvetica,Arial,sans-serif;">
        <div style="background:linear-gradient(170deg,#0a1a12 0%,#0f2a1c 50%,#0c2218 100%);padding:40px 16px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table role="presentation" width="100%" style="max-width:580px;">

                            <!-- ===== ORNAMEN ATAS ===== -->
                            <tr>
                                <td align="center" style="padding-bottom:8px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width:30px;height:2px;background:linear-gradient(90deg,transparent,rgba(212,167,44,0.6));border-radius:2px;"></td>
                                            <td style="padding:0 12px;color:rgba(212,167,44,0.5);font-size:14px;font-family:\'Times New Roman\',serif;">&#10087;</td>
                                            <td style="width:30px;height:2px;background:linear-gradient(90deg,rgba(212,167,44,0.6),transparent);border-radius:2px;"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- ===== HEADER PREMIUM ===== -->
                            <tr>
                                <td style="border-radius:28px 28px 0 0;overflow:hidden;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(145deg,#0f8a52 0%,#16a75c 40%,#0e7d47 100%);position:relative;">
                                        <tr>
                                            <td style="padding:0;position:relative;">
                                                <!-- Overlay pattern subtle -->
                                                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:radial-gradient(circle at 20% 30%,rgba(255,255,255,0.06) 0%,transparent 60%);pointer-events:none;"></div>

                                                <!-- Garis emas -->
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr><td style="height:5px;background:linear-gradient(90deg,transparent,rgba(212,167,44,0.7),#ffd966,rgba(212,167,44,0.7),transparent);"></td></tr>
                                                </table>

                                                <!-- Bismillah Signature -->
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="center" style="padding:32px 24px 8px;">
                                                            <div style="font-family:\'Times New Roman\',\'Traditional Arabic\',serif;font-size:32px;color:#ffd966;font-weight:400;opacity:0.95;line-height:1.8;letter-spacing:1px;text-shadow:0 2px 12px rgba(0,0,0,0.15);">
                                                                بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
                                                            </div>
                                                            <div style="font-size:9px;color:rgba(255,255,255,0.55);letter-spacing:3px;text-transform:uppercase;margin-top:6px;font-weight:300;">
                                                                Dengan Nama Allah Yang Maha Pengasih Lagi Maha Penyayang
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- Pemisah emas -->
                                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px auto 8px;">
                                                    <tr>
                                                        <td style="width:50px;height:1px;background:linear-gradient(90deg,transparent,rgba(255,217,102,0.5));border-radius:1px;"></td>
                                                        <td style="padding:0 10px;color:#ffd966;font-size:9px;letter-spacing:3px;">&#10022;</td>
                                                        <td style="width:50px;height:1px;background:linear-gradient(90deg,rgba(255,217,102,0.5),transparent);border-radius:1px;"></td>
                                                    </tr>
                                                </table>

                                                <!-- Brand Logo Area -->
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="center" style="padding:6px 24px 28px;">
                                                            <table role="presentation" cellpadding="0" cellspacing="0" style="background:rgba(8,23,15,0.5);border:1.5px solid rgba(255,217,102,0.5);border-radius:22px;padding:16px 38px;backdrop-filter:blur(8px);">
                                                                <tr>
                                                                    <td style="text-align:center;">
                                                                        <div style="font-size:10px;color:rgba(255,255,255,0.6);letter-spacing:5px;text-transform:uppercase;margin-bottom:6px;font-weight:300;">Assalamu\'alaikum Warahmatullah</div>
                                                                        <div style="font-size:28px;font-weight:900;color:#ffffff;letter-spacing:1px;font-family:\'Helvetica Neue\',Arial,sans-serif;">
                                                                            هِفْظْلِي <span style="color:#ffd966;font-weight:300;">✦</span> Hifzhly
                                                                        </div>
                                                                        <div style="font-size:11px;color:#ffd966;letter-spacing:2px;margin-top:6px;font-weight:400;text-transform:uppercase;">Sahabat Murojaah Al-Qur\'an Kamu</div>
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

                            <!-- ===== KONTEN UTAMA ===== -->
                            <tr>
                                <td style="background:linear-gradient(160deg,#132f1f 0%,#0f281b 50%,#0d2217 100%);border-radius:0 0 28px 28px;padding:40px 36px 32px;border:1px solid rgba(255,217,102,0.12);border-top:none;box-shadow:0 16px 64px rgba(0,0,0,0.5);">
                                    <table role="presentation" width="100%">
                                        <!-- Judul -->
                                        <tr>
                                            <td style="font-size:20px;font-weight:800;color:#ffd966;letter-spacing:0.5px;padding-bottom:4px;text-shadow:0 1px 4px rgba(0,0,0,0.1);">' . $judul . '</td>
                                        </tr>
                                        <tr><td style="height:1px;background:linear-gradient(90deg,rgba(255,217,102,0.35),rgba(255,217,102,0.05),transparent);margin:12px 0 10px;display:block;"></td></tr>
                                        ' . $konten . '
                                    </table>
                                </td>
                            </tr>

                            <!-- ===== QUOTE ISLAMI ===== -->
                            <tr>
                                <td align="center" style="padding:32px 24px 6px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,rgba(15,138,82,0.08),rgba(15,138,82,0.03));border-radius:16px;border:1px solid rgba(255,217,102,0.1);padding:20px 28px;">
                                        <tr>
                                            <td align="center">
                                                <div style="font-size:10px;color:rgba(255,217,102,0.4);letter-spacing:2px;text-transform:uppercase;margin-bottom:8px;">Mutiara Hikmah</div>
                                                <div style="font-family:\'Times New Roman\',\'Traditional Arabic\',serif;font-size:22px;color:#ffd966;line-height:1.7;margin-bottom:6px;opacity:0.85;">
                                                    خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ
                                                </div>
                                                <div style="font-size:13px;color:rgba(255,255,255,0.5);line-height:1.7;font-style:italic;letter-spacing:0.3px;">
                                                    "Sebaik-baik kalian adalah yang belajar Al-Qur\'an dan mengajarkannya."
                                                </div>
                                                <div style="font-size:10px;color:rgba(255,217,102,0.35);margin-top:6px;letter-spacing:0.5px;">— HR. Bukhari</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- ===== DIVIDER ===== -->
                            <tr>
                                <td align="center" style="padding:14px 0 8px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width:36px;height:1px;background:linear-gradient(90deg,transparent,rgba(255,217,102,0.35));border-radius:1px;"></td>
                                            <td style="padding:0 8px;color:rgba(255,217,102,0.45);font-size:8px;letter-spacing:2px;">&#9679;</td>
                                            <td style="width:36px;height:1px;background:linear-gradient(90deg,rgba(255,217,102,0.35),transparent);border-radius:1px;"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- ===== FOOTER ===== -->
                            <tr>
                                <td align="center" style="padding:0 24px 16px;">
                                    <p style="margin:0 0 8px;font-size:10px;color:rgba(255,255,255,0.25);letter-spacing:0.5px;line-height:1.6;">
                                        <span style="color:rgba(255,217,102,0.3);">"Ya Allah, bukalah hati kami untuk memahami Al-Qur\'an,<br>mudahkanlah lisan kami untuk membacanya,<br>dan amalkanlah dalam kehidupan kami."</span>
                                    </p>
                                    <p style="margin:12px 0 4px;font-size:10px;color:rgba(255,255,255,0.2);letter-spacing:0.8px;text-transform:uppercase;">&copy; 2026 <span style="color:rgba(255,217,102,0.3);">هِفْظْلِي</span> Hifzhly</p>
                                    <p style="margin:0;font-size:9px;color:rgba(255,255,255,0.15);letter-spacing:0.5px;">Pendamping Murojaah Al-Qur\'an Berbasis AI</p>
                                    <p style="margin:8px 0 0;font-size:9px;color:rgba(255,255,255,0.12);">Email ini dikirim otomatis — harap tidak membalas.</p>
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
    $subject = '✧ Verifikasi Email — Hifzhly';
    $konten = '
        <tr><td style="padding:10px 0 4px;color:#889996;font-size:14px;line-height:1.9;">Halo <b style="color:#e8e6e1;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.9;padding-bottom:6px;">Selamat datang di <b style="color:#34d399;">Hifzhly</b> — sahabat murojaah Al-Qur\'an kamu! ✨</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.9;padding-bottom:4px;">Kamu hanya selangkah lagi untuk memulai perjalanan indah menjaga hafalan Al-Qur\'an. Masukkan kode di bawah ini untuk memverifikasi email kamu:</td></tr>
        <tr>
            <td align="center" style="padding:28px 0 22px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(145deg,#0c2418,#0f2a1c);border-radius:18px;border:1px solid rgba(5,150,105,0.35);padding:18px 52px;display:inline-block;box-shadow:0 0 50px rgba(5,150,105,0.1),inset 0 1px 0 rgba(255,255,255,0.04);">
                    <tr>
                        <td style="font-size:38px;font-weight:800;letter-spacing:16px;color:#34d399;font-family:\'Courier New\',monospace;text-align:center;text-shadow:0 0 20px rgba(52,211,153,0.15);">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:rgba(255,255,255,0.22);font-size:11px;line-height:1.7;text-align:center;">Kode ini berlaku selama <b style="color:#d4a72c;">15 menit</b> dan hanya untuk satu kali penggunaan.</td></tr>
        <tr><td style="color:rgba(255,255,255,0.18);font-size:11px;line-height:1.7;text-align:center;padding-top:2px;">Jika kamu tidak mendaftar di Hifzhly, abaikan email ini — akun kamu tidak akan dibuat.</td></tr>
        <tr><td style="padding-top:16px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:rgba(255,217,102,0.04);border-radius:12px;border:1px solid rgba(255,217,102,0.08);padding:14px 18px;">
                <tr>
                    <td style="color:rgba(255,217,102,0.5);font-size:11px;line-height:1.7;text-align:center;font-style:italic;">
                        "Barangsiapa menempuh jalan untuk mencari ilmu, maka Allah akan memudahkan baginya jalan menuju surga." — HR. Muslim
                    </td>
                </tr>
            </table>
        </td></tr>';
    return kirim_email($email, $subject, buat_template_email('✧ Verifikasi Email Kamu', $konten));
}

function kirim_kode_reset($email, $nama, $kode)
{
    $subject = '✦ Reset Password — Hifzhly';
    $konten = '
        <tr><td style="padding:10px 0 4px;color:#889996;font-size:14px;line-height:1.9;">Halo <b style="color:#e8e6e1;">' . $nama . '</b>,</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.9;padding-bottom:6px;">Kami menerima permintaan untuk mengatur ulang password akun <b style="color:#34d399;">Hifzhly</b> kamu.</td></tr>
        <tr><td style="color:#889996;font-size:14px;line-height:1.9;padding-bottom:4px;">Jangan khawatir — ini hal yang biasa terjadi. Gunakan kode di bawah ini untuk membuat password baru:</td></tr>
        <tr>
            <td align="center" style="padding:28px 0 22px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:linear-gradient(145deg,#0c2418,#0f2a1c);border-radius:18px;border:1px solid rgba(5,150,105,0.35);padding:18px 52px;display:inline-block;box-shadow:0 0 50px rgba(5,150,105,0.1),inset 0 1px 0 rgba(255,255,255,0.04);">
                    <tr>
                        <td style="font-size:38px;font-weight:800;letter-spacing:16px;color:#34d399;font-family:\'Courier New\',monospace;text-align:center;text-shadow:0 0 20px rgba(52,211,153,0.15);">' . $kode . '</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td style="color:rgba(255,255,255,0.22);font-size:11px;line-height:1.7;text-align:center;">Kode ini berlaku selama <b style="color:#d4a72c;">15 menit</b>. Jangan bagikan kode ini kepada siapa pun.</td></tr>
        <tr><td style="color:rgba(255,255,255,0.18);font-size:11px;line-height:1.7;text-align:center;padding-top:2px;">Jika kamu tidak meminta reset password, abaikan email ini — akun kamu tetap aman.</td></tr>
        <tr><td style="padding-top:16px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:rgba(255,217,102,0.04);border-radius:12px;border:1px solid rgba(255,217,102,0.08);padding:14px 18px;">
                <tr>
                    <td style="color:rgba(255,217,102,0.5);font-size:11px;line-height:1.7;text-align:center;font-style:italic;">
                        "Dan Kami mudahkan Al-Qur\'an untuk pelajaran, maka adakah orang yang mau mengambil pelajaran?" — QS. Al-Qamar: 17
                    </td>
                </tr>
            </table>
        </td></tr>';
    return kirim_email($email, $subject, buat_template_email('✦ Reset Password Akun', $konten));
}
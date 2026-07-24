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
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="margin:0;padding:0;background-color:#f4f9f6;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f9f6;padding:30px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(5,150,105,0.08);">
                        
                        <!-- Header Section (Bismillah & Logo) -->
                        <tr>
                            <td align="center" style="background:linear-gradient(135deg, #059669, #047857);padding:35px 20px;">
                                <p style="color:#a7f3d0;font-size:22px;margin:0 0 12px;font-family:\'Amiri\', serif, Times;">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</p>
                                <h1 style="color:#ffffff;font-size:28px;margin:0;font-weight:800;letter-spacing:3px;text-transform:uppercase;">HIFZHLY</h1>
                                <p style="color:#d1fae5;font-size:13px;margin:8px 0 0;letter-spacing:0.5px;">Pendamping Murojaah Al-Qur\'an</p>
                            </td>
                        </tr>

                        <!-- Email Title -->
                        <tr>
                            <td align="center" style="padding:30px 30px 0;">
                                <h2 style="margin:0;color:#0f172a;font-size:22px;font-weight:800;">' . $judul . '</h2>
                                <div style="height:4px;width:60px;background-color:#10b981;margin:16px auto 0;border-radius:2px;"></div>
                            </td>
                        </tr>

                        <!-- Dynamic Content (Greeting & OTP) -->
                        ' . $konten . '

                        <!-- Footer Section -->
                        <tr>
                            <td align="center" style="background-color:#f8faf9;padding:24px 30px;border-top:1px solid #e2e8f0;">
                                <p style="margin:0 0 6px;font-size:13px;color:#475569;font-weight:600;">&copy; ' . date('Y') . ' Hifzhly.</p>
                                <p style="margin:0 0 10px;font-size:12px;color:#64748b;">Menjaga Hafalan, Meraih Keberkahan.</p>
                                <p style="margin:0;font-size:11px;color:#94a3b8;line-height:1.5;">Email ini dikirim otomatis oleh sistem keamanan Hifzhly.<br>Mohon untuk tidak membalas email ini.</p>
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
    $subject = 'Kode Verifikasi Akun - Hifzhly';
    $nama_aman = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');

    $konten = '
        <tr>
            <td style="padding: 30px 30px 15px; color: #334155; font-size: 16px; line-height: 1.6;">
                <p style="margin:0 0 15px; color: #059669; font-weight: 600;">Assalamu\'alaikum Warahmatullahi Wabarakatuh,</p>
                <p style="margin:0;">Ahlan wa sahlan, <strong style="color: #0f172a;">' . $nama_aman . '</strong>!</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 30px 25px; color: #475569; font-size: 15px; line-height: 1.7;">
                <p style="margin:0;">Alhamdulillah, langkah pertamamu untuk mulai menjaga hafalan bersama <strong>Hifzhly</strong> sudah hampir selesai. Semoga niat baik ini selalu dimudahkan dan diberkahi oleh Allah SWT.</p>
                <p style="margin:16px 0 0;">Untuk menyelesaikan pendaftaran, silakan masukkan kode verifikasi berikut:</p>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding: 10px 30px 35px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                    <tr>
                        <td align="center" style="background-color: #ecfdf5; border: 2px dashed #10b981; border-radius: 12px; padding: 16px 35px;">
                            <span style="font-family: \'Courier New\', Courier, monospace; font-size: 38px; font-weight: 800; letter-spacing: 12px; color: #047857; display: block; margin-right: -12px;">' . $kode . '</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 30px 35px;">
                <div style="background-color: #f1f5f9; padding: 16px 20px; border-radius: 10px; border-left: 4px solid #cbd5e1;">
                    <p style="margin:0; color: #64748b; font-size: 13px; line-height: 1.6;">
                        <strong style="color: #475569;">Catatan Keamanan:</strong><br>
                        Kode ini hanya berlaku selama <strong>15 menit</strong>. Mohon untuk tidak pernah memberikan kode ini kepada siapa pun demi keamanan akunmu.
                    </p>
                </div>
            </td>
        </tr>';

    return kirim_email($email, $subject, buat_template_email('Verifikasi Akun Baru', $konten));
}

function kirim_kode_reset($email, $nama, $kode)
{
    $subject = 'Pemulihan Password - Hifzhly';
    $nama_aman = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');

    $konten = '
        <tr>
            <td style="padding: 30px 30px 15px; color: #334155; font-size: 16px; line-height: 1.6;">
                <p style="margin:0 0 15px; color: #059669; font-weight: 600;">Assalamu\'alaikum Warahmatullahi Wabarakatuh,</p>
                <p style="margin:0;">Halo, <strong style="color: #0f172a;">' . $nama_aman . '</strong>.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 30px 25px; color: #475569; font-size: 15px; line-height: 1.7;">
                <p style="margin:0;">Kami menerima permintaan untuk mengatur ulang <em>(reset)</em> password akun Hifzhly milikmu. Jangan khawatir, kami siap membantu mengamankan kembali akunmu.</p>
                <p style="margin:16px 0 0;">Gunakan kode keamanan di bawah ini untuk membuat password baru:</p>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding: 10px 30px 35px;">
                <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                    <tr>
                        <td align="center" style="background-color: #fffbeb; border: 2px dashed #f59e0b; border-radius: 12px; padding: 16px 35px;">
                            <span style="font-family: \'Courier New\', Courier, monospace; font-size: 38px; font-weight: 800; letter-spacing: 12px; color: #b45309; display: block; margin-right: -12px;">' . $kode . '</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 30px 35px;">
                <div style="background-color: #fef2f2; padding: 16px 20px; border-radius: 10px; border-left: 4px solid #fca5a5;">
                    <p style="margin:0; color: #7f1d1d; font-size: 13px; line-height: 1.6;">
                        <strong style="color: #991b1b;">Peringatan Penting:</strong><br>
                        Kode pemulihan ini hangus dalam <strong>15 menit</strong>. Jika kamu tidak merasa meminta reset password, mohon abaikan email ini dan pastikan password akunmu aman.
                    </p>
                </div>
            </td>
        </tr>';

    return kirim_email($email, $subject, buat_template_email('Pemulihan Password', $konten));
}

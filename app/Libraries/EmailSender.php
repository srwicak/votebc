<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;

class EmailSender
{
    protected $email;
    
    public function __construct()
    {
        $this->email = \Config\Services::email();
        
        // Konfigurasi email
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => getenv('email.host'),
            'SMTPUser' => getenv('email.username'),
            'SMTPPass' => getenv('email.password'),
            'SMTPPort' => getenv('email.port'),
            'SMTPCrypto' => 'tls',
            'mailType' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];
        
        $this->email->initialize($config);
    }
    
    /**
     * Kirim email verifikasi
     *
     * @param string $to Alamat email penerima
     * @param string $name Nama penerima
     * @param string $token Token verifikasi
     * @return bool
     */
    public function sendVerificationEmail($to, $name, $token)
    {
        $verificationUrl = base_url('verify-email/' . $token);
        
        $this->email->setFrom(getenv('email.from'), 'E-Voting System');
        $this->email->setTo($to);
        $this->email->setSubject('Verifikasi Email - E-Voting System');
        
        $message = "
        <h2>Verifikasi Email</h2>
        <p>Halo {$name},</p>
        <p>Terima kasih telah mendaftar di E-Voting System. Silakan klik tombol di bawah untuk memverifikasi email Anda:</p>
        <p><a href='{$verificationUrl}' style='padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Verifikasi Email</a></p>
        <p>Atau kunjungi link berikut: <a href='{$verificationUrl}'>{$verificationUrl}</a></p>
        <p>Link ini akan kedaluwarsa dalam 24 jam.</p>
        <p>Jika Anda tidak mendaftar di E-Voting System, abaikan email ini.</p>
        <p>Terima kasih,<br>Tim E-Voting System</p>
        ";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    /**
     * Kirim email OTP
     *
     * @param string $to Alamat email penerima
     * @param string $name Nama penerima
     * @param string $otp Kode OTP
     * @return bool
     */
    public function sendOTPEmail($to, $name, $otp)
    {
        $this->email->setFrom(getenv('email.from'), 'E-Voting System');
        $this->email->setTo($to);
        $this->email->setSubject('Kode OTP - E-Voting System');
        
        $message = "
        <h2>Kode OTP untuk Login</h2>
        <p>Halo {$name},</p>
        <p>Berikut adalah kode OTP Anda untuk login ke E-Voting System:</p>
        <h1 style='font-size: 32px; letter-spacing: 5px; text-align: center; padding: 10px; background-color: #f0f0f0; border-radius: 5px;'>{$otp}</h1>
        <p>Kode ini akan kedaluwarsa dalam 5 menit.</p>
        <p>Jika Anda tidak mencoba login ke E-Voting System, abaikan email ini dan segera ubah password Anda.</p>
        <p>Terima kasih,<br>Tim E-Voting System</p>
        ";
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
    
    /**
     * Kirim email notifikasi verifikasi KTM
     *
     * @param string $to Alamat email penerima
     * @param string $name Nama penerima
     * @param string $status Status verifikasi (approved/rejected)
     * @param string $reason Alasan penolakan (jika ditolak)
     * @return bool
     */
    public function sendKTMVerificationEmail($to, $name, $status, $reason = null)
    {
        $this->email->setFrom(getenv('email.from'), 'E-Voting System');
        $this->email->setTo($to);
        
        if ($status === 'approved') {
            $this->email->setSubject('KTM Anda Telah Diverifikasi - E-Voting System');
            
            $message = "
            <h2>KTM Anda Telah Diverifikasi</h2>
            <p>Halo {$name},</p>
            <p>Kami dengan senang hati memberitahukan bahwa KTM Anda telah berhasil diverifikasi.</p>
            <p>Sekarang Anda dapat berpartisipasi dalam pemilihan yang tersedia.</p>
            <p>Terima kasih,<br>Tim E-Voting System</p>
            ";
        } else {
            $this->email->setSubject('Verifikasi KTM Ditolak - E-Voting System');
            
            $message = "
            <h2>Verifikasi KTM Ditolak</h2>
            <p>Halo {$name},</p>
            <p>Kami mohon maaf untuk memberitahukan bahwa verifikasi KTM Anda ditolak.</p>
            <p><strong>Alasan:</strong> {$reason}</p>
            <p>Silakan unggah ulang KTM Anda dengan memperhatikan alasan penolakan di atas.</p>
            <p>Terima kasih,<br>Tim E-Voting System</p>
            ";
        }
        
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
}
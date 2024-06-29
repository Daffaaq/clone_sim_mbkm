<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background-color: #ffffff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        .email-header {
            background-color: #0073e6;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .email-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .email-header p {
            margin: 5px 0 0;
            font-size: 16px;
        }

        .email-content {
            padding: 20px;
            color: #333333;
        }

        .email-content p {
            line-height: 1.6;
        }

        .email-content .verify-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0073e6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .email-content .verify-button:hover {
            background-color: #005cb2;
        }

        .email-footer {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: center;
            border-radius: 0 0 8px 8px;
        }

        .email-footer p {
            margin: 0;
            color: #777777;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Verifikasi Email</h2>
            <p>Panitia Magang JTI Polinema</p>
        </div>
        <div class="email-content">
            <p>Yth. {{ $user->name }},</p>
            <p>Anda menerima email ini karena kami menerima permintaan untuk verifikasi email Anda di sistem kami.</p>
            <p>Silakan klik tautan di bawah ini untuk mengaktifkan akun Anda:</p>
            <a class="verify-button" href="{{ url('/verify-email/' . $token) }}">Verifikasi Email</a>
            <p>Jika Anda tidak meminta verifikasi ini, Anda dapat mengabaikan email ini.</p>
            <p>Setelah akun Anda diaktifkan, harap segera login dan mengganti password Anda untuk keamanan akun Anda.
            </p>
            <p>Terima kasih atas perhatian Anda.</p>
        </div>
        <div class="email-footer">
            <p>Panitia Magang JTI Polinema</p>
            <p>Jl. Soekarno-Hatta No.9, Malang, Jawa Timur 65141</p>
        </div>
    </div>
</body>

</html>

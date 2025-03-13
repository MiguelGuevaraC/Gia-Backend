<!DOCTYPE html>
<html>

<head>
    <title>Token de Verificación</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
        }

        .message {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        .token {
            font-size: 24px;
            font-weight: bold;
            color: #007BFF;
            background: #e6f0ff;
            padding: 10px 20px;
            display: inline-block;
            border-radius: 5px;
            margin: 10px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="container">
        <img src="https://yourcompany.com/logo.png" alt="Logo" class="logo">
        <h2>¡Hola!</h2>
        <p class="message">Tu código de verificación para acceder a nuestros servicios es:</p>
        <p class="token">{{ $token }}</p>
        <p class="message">Por favor, usa este código para completar tu proceso de registro. Este código es válido por un tiempo limitado.</p>
        <p>Si no solicitaste este código, puedes ignorar este mensaje.</p>
        <p>Gracias,</p>
        <p><strong>El equipo de soporte</strong></p>
        <div class="footer">Este es un mensaje automático. No respondas a este correo.</div>
    </div>

</body>

</html>

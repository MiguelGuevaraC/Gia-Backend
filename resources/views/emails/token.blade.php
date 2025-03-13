<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta - {{ $name_aplication }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo {
            width: 170px;
            background-color: rgb(0, 0, 0);
            margin-bottom: 1px;
        }
        h2 {
            color: #000000;
            font-size: 22px;
            margin-bottom: 15px;
        }
        .name_aplication{
            color: #000000;
            font-weight: bold;
        }
        .message {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .token {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            background: #40d432;
            padding: 15px 30px;
            display: inline-block;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            background-color: #000000;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <img src="{{ asset('../storage/images/Gia.png') }}" alt="Logo de {{ $name_aplication }}" class="logo">
        <h2>¡Verifica tu cuenta en <span class="name_aplication">{{ $name_aplication }}</span>!</h2>
        <p class="message">
            ¡Gracias por registrarte en <strong>{{ $name_aplication }}</strong>! Para completar el proceso y acceder a nuestro servicio de reservas desde la aplicación móvil, 
            introduce el siguiente código de verificación en la app:
        </p>
        
        <p class="token">{{ $token }}</p>

        <p class="message">
            Este código tiene una validez limitada. Si no has solicitado este código, puedes ignorar este mensaje de manera segura.
        </p>
        <p class="message">Atentamente,</p>
        <p><strong>El equipo de Mr. Soft</strong></p>
        <div class="footer">Este es un mensaje automático, por favor no respondas a este correo.</div>
    </div>
</body>
</html>

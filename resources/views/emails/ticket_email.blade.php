<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ticket de Sorteo - {{ $lottery_ticket['lottery_name'] }}</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      text-align: center;
    }

    .container {
      max-width: 620px;
      margin: 40px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .logo {
      width: 180px;
      margin-bottom: 20px;
    }

    h2 {
      color: #1d1d1d;
      font-size: 24px;
      margin-bottom: 15px;
    }

    .highlight {
      font-weight: bold;
      color: #2c7efc;
    }

    .info {
      font-size: 16px;
      color: #444;
      margin-bottom: 14px;
      line-height: 1.6;
    }

    .ticket-code {
      font-size: 24px;
      font-weight: bold;
      background-color: #2c7efc;
      color: #ffffff;
      padding: 14px 28px;
      border-radius: 8px;
      display: inline-block;
      margin: 20px 0;
      letter-spacing: 1.2px;
    }

    .barcode {
      margin-top: 20px;
    }

    .barcode img {
      max-width: 260px;
    }

    .prizes-title {
      margin-top: 30px;
      font-size: 18px;
      font-weight: bold;
      color: #1a1a1a;
      border-top: 1px solid #ddd;
      padding-top: 20px;
      margin-bottom: 10px;
    }

    .prizes-grid {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 25px;
    }

    .prize-card {
      width:350px;
      background-color: #f9f9f9;
      border: 1px solid #dbeafe;
      border-radius: 12px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .prize-card img {
      width: 240px;
      height: 240px;
      object-fit: cover;
      border-radius: 10px;
      margin: 12px auto;
      display: block;
    }

    .prize-title {
      font-size: 16px;
      font-weight: bold;
      color: #1e40af;
      margin-bottom: 8px;
    }

    .prize-desc {
      font-size: 14px;
      color: #333;
    }

    .footer {
      font-size: 12px;
      color: #888;
      margin-top: 35px;
    }
  </style>
</head>

<body>
  <div class="container">
    <img src="{{ asset('storage/images/Gia.png') }}" alt="Logo {{ $name_aplication }}" class="logo" />

    <h2>¡Tu ticket para el <span class="highlight">{{ $lottery_ticket['lottery_name'] }}</span> ha sido generado!</h2>

    <p class="info">
      Estimado(a) <strong>{{ $lottery_ticket['user_owner_name'] }}</strong>,<br>
      Has recibido un ticket válido para participar en nuestro sorteo.
    </p>

    <div class="ticket-code">{{ $lottery_ticket['code_correlative'] }}</div>

    <p class="info">
      <strong>Fecha del sorteo:</strong> {{ \Carbon\Carbon::parse($lottery_ticket['lottery_date'])->format('d/m/Y H:i') }}<br>
      <strong>Empresa organizadora:</strong> {{ $lottery_ticket['lottery_company_business_name'] }}
    </p>

    @if(isset($lottery_ticket['code']['barcode_path']))
      <div class="barcode">
        <img src="{{ $lottery_ticket['code']['barcode_path'] }}" alt="Código de barras del ticket" />
      </div>
    @endif

    @if(!empty($lottery_ticket['prizes']))
      <div class="prizes-title">🎁 Podrás ganar uno de los siguientes premios:</div>
      <div class="prizes-grid">
        @foreach($lottery_ticket['prizes'] as $index => $prize)
          @php
            $emoji = match($index) {
              0 => '🥇',
              1 => '🥈',
              2 => '🥉',
              default => '📦'
            };
          @endphp
          <div class="prize-card">
            <div class="prize-title">{{ $emoji }} Premio Nro {{ $index + 1 }}</div>
            <div class="prize-title">{{ $prize['name'] }}</div>
            <div class="prize-desc">{{ $prize['description'] }}</div>
            @if($prize['route'])
              <img src="{{ $prize['route'] }}" alt="Imagen del premio" />
            @endif
          </div>
        @endforeach
      </div>
    @endif

    <p class="info">¡Mucha suerte y gracias por participar!</p>

    <p><strong>El equipo de Mr. Soft</strong></p>

    <div class="footer">Este es un mensaje automático, por favor no respondas a este correo.</div>
  </div>
</body>
</html>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Yönlendirme | BeArtShare</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 40px;
        }
        .logo {
            width: 120px;
            margin-bottom: 30px;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: #c9a962;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        h2 {
            color: #fff;
            font-weight: 400;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        p {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
        .bank-logo {
            margin-top: 30px;
            padding: 15px 30px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            display: inline-block;
        }
        .bank-logo img {
            height: 30px;
        }
        .bank-name {
            color: #00b050;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .secure-badge {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
        }
        .secure-badge svg {
            width: 14px;
            height: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('images/logo.svg') }}" alt="BeArtShare" class="logo" style="filter: brightness(0) invert(1);">

        <div class="spinner"></div>

        <h2>Güvenli Ödeme Sayfasına Yönlendiriliyorsunuz</h2>
        <p>Lütfen bekleyiniz, Garanti Bankası ödeme sayfasına aktarılıyorsunuz...</p>

        <div class="bank-logo">
            <span class="bank-name">Garanti BBVA</span>
        </div>

        <div class="secure-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            256-bit SSL ile güvenli bağlantı
        </div>
    </div>

    <!-- Hidden form that auto-submits to Garanti -->
    <form id="paymentForm" action="{{ $gatewayUrl }}" method="POST" style="display: none;">
        @foreach($formData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        // Sayfayı yükledikten hemen sonra formu gönder
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('paymentForm').submit();
            }, 1500); // 1.5 saniye bekle (kullanıcı deneyimi için)
        });
    </script>
</body>
</html>

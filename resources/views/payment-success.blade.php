<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 40px 36px;
            text-align: center;
            box-shadow: 0 6px 30px rgba(0,0,0,0.10);
            max-width: 380px;
            width: 90%;
        }
        .icon { font-size: 72px; margin-bottom: 20px; display: block; }
        h2 { color: #2e7d32; font-size: 26px; font-weight: 700; margin-bottom: 10px; }
        .subtitle { color: #555; font-size: 15px; margin-bottom: 32px; line-height: 1.5; }
        .redirect-msg { color: #888; font-size: 13px; margin-bottom: 10px; }
        .bar-wrap {
            background: #e8f5e9;
            border-radius: 99px;
            height: 6px;
            overflow: hidden;
        }
        .bar {
            height: 100%;
            background: #43a047;
            border-radius: 99px;
            width: 100%;
            transition: width linear;
        }
    </style>
</head>
<body>
    <div class="card">
        <span class="icon">✅</span>
        <h2>Payment Successful</h2>
        <p class="subtitle">Your order has been confirmed.<br>Thank you for your purchase!</p>

        <p class="redirect-msg" id="msg">Redirecting back to your orders in <strong id="count">5</strong>s…</p>
        <div class="bar-wrap">
            <div class="bar" id="bar"></div>
        </div>
    </div>

    <script>
        var seconds = 5;
        var redirectUrl = @json($callback ?? null);
        var bar = document.getElementById('bar');
        var countEl = document.getElementById('count');

        // Start the shrink animation
        bar.style.transition = 'width ' + seconds + 's linear';
        setTimeout(function() { bar.style.width = '0%'; }, 50);

        var interval = setInterval(function() {
            seconds--;
            countEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    // Signal Flutter WebView if no callback URL
                    document.getElementById('msg').textContent = 'You can close this window.';
                    if (window.flutter_inappwebview) {
                        window.flutter_inappwebview.callHandler('paymentSuccess');
                    }
                }
            }
        }, 1000);
    </script>
</body>
</html>

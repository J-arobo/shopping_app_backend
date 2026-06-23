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
        .subtitle { color: #555; font-size: 15px; margin-bottom: 28px; line-height: 1.5; }
        .redirect-msg { color: #888; font-size: 13px; margin-bottom: 10px; }
        .bar-wrap {
            background: #e8f5e9;
            border-radius: 99px;
            height: 6px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .bar {
            height: 100%;
            background: #43a047;
            border-radius: 99px;
            width: 100%;
            transition: width linear;
        }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: #43a047;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:active { background: #388e3c; }
    </style>
</head>
<body>
    <div class="card">
        <span class="icon">✅</span>
        <h2>Payment Successful</h2>
        <p class="subtitle">Your order has been confirmed.<br>Thank you for your purchase!</p>

        <p class="redirect-msg" id="msg">Closing automatically in <strong id="count">5</strong>s…</p>
        <div class="bar-wrap">
            <div class="bar" id="bar"></div>
        </div>

        <button class="btn" onclick="goBack()">View My Orders</button>
    </div>

    <script>
        var seconds = 5;
        var redirectUrl = @json($callback ?? null);

        function goBack() {
            // 1. If we have a callback URL, use it
            if (redirectUrl) {
                window.location.href = redirectUrl;
                return;
            }
            // 2. Try Flutter JS channel (flutter_inappwebview plugin)
            if (window.flutter_inappwebview) {
                window.flutter_inappwebview.callHandler('paymentSuccess');
                return;
            }
            // 3. Try Flutter JS channel (webview_flutter plugin)
            if (window.PaymentSuccess) {
                window.PaymentSuccess.postMessage('success');
                return;
            }
            // 4. Try going back in history
            if (window.history.length > 1) {
                window.history.go(-4);
            } else {
                window.close();
            }
        }

        // Start the shrink animation
        var bar = document.getElementById('bar');
        bar.style.transition = 'width ' + seconds + 's linear';
        setTimeout(function() { bar.style.width = '0%'; }, 50);

        var interval = setInterval(function() {
            seconds--;
            document.getElementById('count').textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                goBack();
            }
        }, 1000);
    </script>
</body>
</html>

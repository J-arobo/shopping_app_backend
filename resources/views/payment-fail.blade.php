<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Failed</title>
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: sans-serif; }
        .card { background: #fff; border-radius: 16px; padding: 48px 40px; text-align: center; box-shadow: 0 4px 24px rgba(0,0,0,0.08); max-width: 380px; width: 90%; }
        .icon { font-size: 64px; margin-bottom: 16px; }
        h2 { color: #dc3545; margin: 0 0 12px; font-size: 24px; }
        p { color: #666; margin: 0; font-size: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">❌</div>
        <h2>Payment Failed</h2>
        <p>Something went wrong with your payment. Please try again.</p>
    </div>
    <script>
        if (window.PaymentFail) { window.PaymentFail.postMessage('fail'); }
        if (window.flutter_inappwebview) { window.flutter_inappwebview.callHandler('paymentFail'); }
    </script>
</body>
</html>

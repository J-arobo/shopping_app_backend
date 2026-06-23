<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/vendor.min.css">
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: sans-serif; }
        .card { background: #fff; border-radius: 16px; padding: 48px 40px; text-align: center; box-shadow: 0 4px 24px rgba(0,0,0,0.08); max-width: 380px; width: 90%; }
        .icon { font-size: 64px; margin-bottom: 16px; }
        h2 { color: #28a745; margin: 0 0 12px; font-size: 24px; }
        p { color: #666; margin: 0; font-size: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✅</div>
        <h2>Payment Successful</h2>
        <p>Your order has been confirmed. Thank you for your purchase!</p>
    </div>
    <script>
        // Signal to the Flutter WebView that payment succeeded
        if (window.PaymentSuccess) { window.PaymentSuccess.postMessage('success'); }
        if (window.flutter_inappwebview) { window.flutter_inappwebview.callHandler('paymentSuccess'); }
    </script>
</body>
</html>

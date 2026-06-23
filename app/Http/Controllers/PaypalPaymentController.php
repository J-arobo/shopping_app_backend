<?php
namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PaypalPaymentController extends Controller
{
    private string $clientId;
    private string $secret;
    private string $baseUrl;
    private Client $http;

    public function __construct()
    {
        $paypal = Config::get('paypal');
        $this->clientId = $paypal['client_id'];
        $this->secret   = $paypal['secret'];
        $mode = $paypal['settings']['mode'] ?? 'sandbox';
        $this->baseUrl  = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->http = new Client(['timeout' => 30]);
    }

    private function getAccessToken(): string
    {
        $response = $this->http->post("{$this->baseUrl}/v1/oauth2/token", [
            'auth'        => [$this->clientId, $this->secret],
            'form_params' => ['grant_type' => 'client_credentials'],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    public function payWithpaypal(Request $request)
    {
        $order  = Order::with(['details'])->where(['id' => session('order_id')])->first();
        $tr_ref = Str::random(6) . '-' . rand(1, 1000);

        $amount = sprintf('%0.2f', $order['order_amount']);
        $currency = Helpers::currency_code();

        \session()->put('transaction_reference', $tr_ref);

        try {
            $token = $this->getAccessToken();

            $body = [
                'intent' => 'sale',
                'payer'  => ['payment_method' => 'paypal'],
                'redirect_urls' => [
                    'return_url' => URL::route('paypal-status'),
                    'cancel_url' => URL::route('payment-fail'),
                ],
                'transactions' => [[
                    'amount'      => ['currency' => $currency, 'total' => $amount],
                    'description' => $tr_ref,
                    'item_list'   => [
                        'items' => [[
                            'name'     => session('f_name') ?? 'Order',
                            'currency' => $currency,
                            'quantity' => 1,
                            'price'    => $amount,
                        ]],
                    ],
                ]],
            ];

            $response = $this->http->post("{$this->baseUrl}/v1/payments/payment", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $body,
            ]);

            $payment = json_decode($response->getBody(), true);
            $paymentId = $payment['id'];

            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'transaction_reference' => $paymentId,
                    'payment_method'        => 'paypal',
                    'order_status'          => 'success',
                    'failed'                => now(),
                    'updated_at'            => now(),
                ]);

            Session::put('paypal_payment_id', $paymentId);

            $approvalUrl = collect($payment['links'])
                ->firstWhere('rel', 'approval_url')['href'] ?? null;

            if ($approvalUrl) {
                return Redirect::away($approvalUrl);
            }

            Toastr::error('Could not get PayPal approval URL.');
            return back();

        } catch (\Throwable $ex) {
            \Log::error('PayPal payment error: ' . $ex->getMessage());
            Toastr::error(trans('messages.your_currency_is_not_supported', ['method' => trans('messages.paypal')]));
            return back();
        }
    }

    public function getPaymentStatus(Request $request)
    {
        $payment_id = Session::get('paypal_payment_id');

        if (empty($request['PayerID']) || empty($request['token'])) {
            Session::put('error', trans('messages.payment_failed'));
            return Redirect::back();
        }

        try {
            $token = $this->getAccessToken();

            $response = $this->http->post(
                "{$this->baseUrl}/v1/payments/payment/{$payment_id}/execute",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => ['payer_id' => $request['PayerID']],
                ]
            );

            $result = json_decode($response->getBody(), true);
            $order  = Order::where('transaction_reference', $payment_id)->first();

            if (($result['state'] ?? '') === 'approved') {
                $order->transaction_reference = $payment_id;
                $order->payment_method  = 'paypal';
                $order->payment_status  = 'paid';
                $order->order_status    = 'confirmed';
                $order->confirmed       = now();
                $order->save();

                if ($order->callback) {
                    return redirect($order->callback);
                }
                return \redirect()->route('payment-success');
            }

            $order->order_status = 'failed';
            $order->failed       = now();
            $order->save();

            if ($order->cancel_url) {
                return redirect($order->cancel_url);
            }
            if ($order->callback) {
                return redirect($order->callback);
            }
            return \redirect()->route('payment-fail');

        } catch (\Throwable $ex) {
            \Log::error('PayPal execute error: ' . $ex->getMessage());
            Session::put('error', trans('messages.payment_failed'));
            return Redirect::back();
        }
    }
}

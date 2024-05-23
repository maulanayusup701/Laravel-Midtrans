<?php

namespace App\Http\Controllers;

use \App\Models\Order;
use \Exception;
use \Midtrans\Notification;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function index()
    {
        return view('payment');
    }

    public function charge(Request $request)
    {
        $orderId = uniqid();
        $order = new Order();
        $order->order_id = $orderId;
        $order->status = 'pending';
        $order->save();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => 10000,
            ],
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '081234567890',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function notification(Request $request)
    {
        $notification = new Notification();

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;

        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $order->status = 'success';
                break;
            case 'pending':
                $order->status = 'pending';
                break;
            case 'deny':
                $order->status = 'failed';
                break;
            case 'expire':
                $order->status = 'expired';
                break;
            case 'cancel':
                $order->status = 'cancelled';
                break;
            default:
                $order->status = 'unknown';
                break;
        }

        // Save the updated order status
        $order->save();

        return response()->json(['status' => 'success']);
    }

    public function paymentIssuccess()
    {
        return 'Pembayan Success';
    }
}

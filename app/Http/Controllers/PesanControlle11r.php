<?php

//pelanggan

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Menu;
use App\Models\MenuOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Illuminate\support\Str;
use Illuminate\Support\Facades\Http;
use Midtrans\Notification;


class PesanController extends Controller
{
    public function show($mejaID)
    {
        return view('customer.pesan', ['idMeja' => $mejaID]);
    }

    public function menu($mejaID)
    {
        $menus = Menu::all();

        return view('customer.menu', compact('mejaID', 'menus'));
    }

    public function addMenuCart($mejaID, $id)
    {
        $menu = Menu::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = isset($cart[$id]) ? $cart[$id]['quantity'] + 1 : 1;
        $subtotal = $quantity * $menu->price_food;
        $total = $subtotal - ($subtotal * $menu->discount / 100);

        $cart[$id] = [
            'name' => $menu->name,
            'menuId' => $menu->id,
            'quantity' => $quantity,
            'price' => $menu->price_food,
            'image' => $menu->image_path,
            'discount' => $menu->discount,
            'subtotal' => $subtotal,
            'total' => $total,
        ];
        // Calculate subtotal and total for the item
        $cart[$id]['subtotal'] = ($cart[$id]['price'] - $cart[$id]['discount']) * $cart[$id]['quantity'];
        // Update the cart in the session
        session(['cart' => $cart]);

        // Deprecated, uncomment if you still want to use this

        // // Calculate total by summing up all subtotals in the cart
        // $total = array_sum(array_column($cart, 'subtotal'));

        // // Update total for each item in the cart
        // foreach ($cart as &$item) {
        //     $item['total'] = $total;
        // }

        // // Update the cart in the session with the recalculated totals
        // session(['cart' => $cart]);
        return redirect()->back();
    }

    public function cartdelete($mejaID, $id, Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart', []);

            if (isset($cart[$request->id])) {
                // Remove the item with the specified ID from the cart
                unset($cart[$request->id]);

                // Update the session cart with the modified array
                session(['cart' => $cart]);
            }
        }

        return redirect()->back();
    }

    public function flush($mejaID)
    {
        session()->forget('cart');

        return redirect()->back();
    }

    public function cartshow($mejaID)
    {
        return view('customer.cart ', ['idMeja' => $mejaID]);
    }

    public function checkOut(Request $request, $mejaID)
    {
        $cart = $request->session()->get('cart', []);
        $nameCus = $request->session()->get('nameCus');
        $totalPrice = 0;

        // Create new order
        $Order = new Order();

        // Override auto increment order
        if (Order::all()->isEmpty()) {
            $Order->id = rand(0, 9999);
        } else {
            $maxID = $Order->max('id');
            $Order->id = $maxID + 1;
        }

        $Order->id_customer = $request->session()->get('idCus');
        $Order->date = now();
        $Order->order_status = 'active';

        // Make an array of MenuOrder eloquent object
        $menuOrders = [];
        foreach ($cart as $item) {
            $menuOrder = new MenuOrder();
            $menuOrder->id_menu = $item['menuId'];
            $menuOrder->id_order = $Order->id; // Assuming you have the order ID
            $menuOrder->quantity = $item['quantity'];
            $menuOrder->price_food = $item['price'];
            $menuOrder->discount = $item['discount'];
            $menuOrder->subtotal = $item['subtotal'];
            $menuOrder->menu_note = 'None';

            $menuOrders[] = $menuOrder;
            $totalPrice += $item['subtotal'];
        }

        // Save order
        $Order->total_price = $totalPrice;
        $Order->table_number = rand(0, 99);
        $Order->save();

        // Save each MenuOrder
        foreach ($menuOrders as $menuOrder) {
            $menuOrder->save();
        }



        $data = Order::latest()->first();

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = false;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = false;

        $params = [
            'transaction_details' => [
                'order_id' => $Order->table_number,
                'gross_amount' => $data->total_price,
            ],
            'customer_details' => [
                'name' => $nameCus,
            ]
        ];

        $auth = base64_encode(env('MIDTRANS_SERVER_KEY'));
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '. $auth,
        ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);


        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Update the Order with the snap_token value
        $data->snap_token = $snapToken;
        $data->save();


        return response()->json([$response]);
        return view('customer.checkout', compact('snapToken', 'data'));
    }

    public function midtransCallback(Request $request)
    {
        $auth = base64_encode(env('MIDTRANS_SERVER_KEY'));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '. $auth,
        ])->post("https://api.sandbox.midtrans.com/v2/$request->order_id/status");

        $respones= json_decode($response->body());

        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if (!$order) {
            return response('Order not found', 404);
        }

        $transaction = $request->transaction_status;

        if ($transaction == 'capture') {
            if ($order->payment_status == 'unpaid') {
                $order->payment_status = 'paid';
                $order->save();
            }
        } else if ($transaction == 'settlement') {
            if ($order->payment_status == 'unpaid') {
                $order->payment_status = 'paid';
                $order->save();
            }
        }

        return response('Payment status updated', 200);
        return view('customer.checkoutpaid');
    }

    public function setupSession(Request $request)
    {
        $request->session()->put('nameCus', $request->input('namaCus'));
        $request->session()->put('idMeja', $request->input('id'));

        // Assign customer data
        $Customer = new Customer();
        $Customer->name = $request->input('namaCus');

        $Customer->save();

        $request->session()->put('idCus', $Customer->id);

        return redirect()->route('pesan.menu', $request->input('id'));
    }
}

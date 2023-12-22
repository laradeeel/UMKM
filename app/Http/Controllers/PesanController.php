<?php
//pelanggan
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Menu;
use App\Models\MenuOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Snap;
use App\Veritrans\Veritrans;
use facade\http_response_code;

class PesanController extends Controller
{
    public function show($mejaID){
        return view('customer.pesan',['idMeja' => $mejaID]);
    }
    public function menu($mejaID){
        $menus = Menu::all();
        return view('customer.menu',compact('mejaID', 'menus'));
    }
    public function addMenuCart($mejaID, $id)
    {
        $menu = Menu::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = isset($cart[$id]) ? $cart[$id]['quantity'] + 1 : 1;
        $subtotal = $quantity * $menu->price_food;
        $total = $subtotal - ($subtotal * $menu->discount / 100);

        $cart[$id] = [
            "name" => $menu->name,
            "menuId" => $menu->id,
            "quantity" => $quantity,
            "price" => $menu->price_food,
            "image" => $menu->image_path,
            "discount" => $menu->discount,
            "subtotal" => $subtotal,
            "total" => $total
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

    public function flush($mejaID){
        session()->forget('cart');
        return redirect()->back();
    }

    public function cartshow($mejaID){
        return view('customer.cart ',['idMeja' => $mejaID]);
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
    $Order->table_number = $mejaID;
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

    $params = array(
        'transaction_details' => array(
            'order_id' => rand(),
            'gross_amount' => $data->total_price,
        ),
        'customer_details' => array(
            'name' => $nameCus,
        ),
    );

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    // Update the Order with the snap_token value
    $data->snap_token = $snapToken;
    $data->save();
    

    return view('customer.checkout', compact('snapToken', 'data'));
}

    public function checkOutpaid(Request $request, $checkOut)
    {
        $auth = base64_encode(env('MIDTRANS_SERVER_KEY'));

        $respone = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic $auth'
        ])->get("http://api.sandbox.midtrans.com/v2/$request->order_id/status");

        $order = Order::where('snap_token', $checkOut)->first();

        if (!$order) {
            // Order not found, handle the error or return an appropriate response
            return response('Order not found', 404);
        }

        $order->payment_status = 'paid';
        $order->save();

        return response('Payment status updated', 200);
        return view('customer.checkoutpaid', compact('snapToken', 'data'));
    }

    public function setupSession(Request $request)
    {
        $request->session()->put('nameCus',$request->input('namaCus'));
        $request->session()->put('idMeja',$request->input('id'));

        // Assign customer data
        $Customer = new Customer();
        $Customer->name = $request->input('namaCus');

        $Customer->save();

        $request->session()->put('idCus',$Customer->id);

        return redirect()->route('pesan.menu', $request->input('id'));
    }

    
       
}

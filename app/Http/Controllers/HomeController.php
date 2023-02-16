<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommercetoolsController as CT;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $ct = new CT();
        $output['products'] = $ct->getProducts();
        $output['cart_item_count'] = getCartItemCount();
        $session_data = Session::all();
        dump($session_data);
        return view('pages.home', $output);
    }

    public function addTocart($product_id = null)
    {
        if ($product_id != null) {
            $ct = new CT();
            $ct->addToCart($product_id);
        }

        return redirect()->route('shop');
    }

    public function checkout()
    {
        $output['cart_items'] = getCartItemsForCheckout();
        if (!$output['cart_items']) {
            return redirect()->route('shop');
        }
        $output['cart_item_count'] = getCartItemCount();
        $output['supar_pay_offer'] = getSuperPayOffer();

        if (isset(Session::get('ct_customer')->id)) {
            $output['ct_customer'] =  Session::get('ct_customer');
            $output['supar_pay_payment'] =  getSuperPayment();
        }

        return view('pages.checkout', $output);
    }

    public function resetSession()
    {
        Session::flush();
        return redirect()->route('shop');
    }

    public function superpaymentsSuccess()
    {
        $session_data = Session::all();
        $suparpayment = DB::table('suparpayment')
            ->where('externalReference', $session_data['ct_cart']->id)
            ->first();
        if ($suparpayment->transactionStatus == 'PaymentSuccess') {
            createCommercetoolsOrder($session_data['ct_customer'], $session_data['ct_cart']);
        }
        dump($suparpayment);
        dd($session_data);
        $input =  [
            "eventType" => "PaymentStatus",
            "transactionId" => "12fc6f81-8ea4-4056-88c1-86fcaeb60046",
            "transactionReference" => "CKTFJ3GDS634CV4HTP",
            "transactionStatus" => "PaymentSuccess",
            "transactionAmount" => 1,
            "externalReference" => "c8e90011-1cc7-43e8-8979-a34b7b82ec4a",
        ];
        DB::table('suparpayment')->insert($input);
        dump($input);
        // Create Order order on CommerceTools
        $session_data = Session::all();
        Log::debug('Payment Success session ' . json_encode($session_data));
        Log::debug('Payment Success externalReference ' . $input['externalReference']);
        Log::debug('Payment Success transactionStatus ' . $input['transactionStatus']);
        Log::debug('Payment Success ct_cart ' . $session_data['ct_cart']->id);
        if ($input['externalReference'] == $session_data['ct_cart']->id && $input['transactionStatus'] == 'PaymentSuccess') {
            Log::debug('Payment Success');
        }
        dump($session_data);
        dd('Hello');

        Log::debug('A superpaymentsSuccess');
        $request_body = $request->all();
        Log::debug('URL' . $request->fullUrl());
        Log::debug(Session::all());
        Log::debug(json_encode($request_body));
        Log::debug('Log end superpaymentsSuccess');
        return response()->json([], 200);
    }

    public function webhook(Request $request)
    {
        $input = $request->all();
        if ($input['externalReference']) {
            DB::table('suparpayment')->insert($input);
            return response()->json([], 200);
        }
    }
}

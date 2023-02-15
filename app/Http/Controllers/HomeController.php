<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommercetoolsController as CT;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $ct = new CT();
        $output['products'] = $ct->getProducts();
        $output['cart_item_count'] = getCartItemCount();
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
        
        dump(Session::get('ct_suparpay_offer_id'));
        if (isset(Session::get('ct_customer')->id)) {
            $output['ct_customer'] =  Session::get('ct_customer');
            $output['supar_pay_payment'] =  getSuperPayment();
        }
        return view('pages.checkout', $output);
    }

    public function resetSession()
    {
       Session::remove();
    }

    public function superpaymentsSuccess(Request $request)
    {
        $request_body = $request->all();
        Log::debug('Log start superpaymentsSuccess');
        Log::debug('URL'.$request->fullUrl());
        Log::debug($request_body);
        Log::debug(json_encode($request_body));
        Log::debug('Log end superpaymentsSuccess');
        return response()->json([], 200);
    }
}

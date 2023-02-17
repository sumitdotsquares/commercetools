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
        // dump($session_data);
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
        dd( $session_data);

        
        
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

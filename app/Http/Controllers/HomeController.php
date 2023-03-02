<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommercetoolsController as CT;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function index()
    {
        $ct = new CT();
        $output['products'] = Session::get('ct_products');
        if ($output['products'] == null)
            $output['products'] = $ct->getProducts();
        $session_data = Session::all();
        // dump($session_data);
        return view('pages.home', $output);
    }

    public function phpinfo()
    {
        phpinfo();
        die;
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $output['msg'] = '';
        if (sizeof($input) > 0) {
            $ct = new CT();
            $output['msg'] = $ct->loginCustomer($input['email'], $input['password']);
        }
        if (isset(Session::get('ct_customer')->id)) {
            return redirect()->route('userDashboard');
        }
        return view('pages.login', $output);
    }

    public function logout()
    {
        Session::forget('ct_customer');
        return redirect()->route('login');
    }   

    public function userDashboard()
    {
        $session_data = Session::all();
       

        if (!isset(Session::get('ct_customer')->id)) {
            return redirect()->route('login');
        }

        $output['orders'] = [];
        $output['orders'] = DB::table('orders')->where('customer_id', $session_data['ct_customer']->id)
        ->orderBy('timestamp', 'DESC')->get();
        
        return view('pages.user-dashboard', $output);
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
        $output['supar_pay_offer'] = getSuperPayOffer();

        if (isset(Session::get('ct_customer')->id)) {
            $output['ct_customer'] =  Session::get('ct_customer');
            // $output['supar_pay_payment'] =  getSuperPayment();
        }
        // dump($output);
        return view('pages.checkout', $output);
    }

    public function processCheckout()
    {
        if (null == Session::get('supar_pay_payment')) {
            $order = Session::get('ct_order');
            if ($order == null) {
                $ct = new CT();
                $order = $ct->preCreateOrder();
            }
            $session_data = Session::all();

            $supar_pay_payment =  getSuperPayment($order->orderNumber);
            return redirect()->to($supar_pay_payment->redirectUrl);
        }
    }

    public function resetSession()
    {
        $ct_customer = Session::get('ct_customer');
        Session::flush();
        Session::put('ct_customer', $ct_customer);
        return redirect()->route('shop');
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

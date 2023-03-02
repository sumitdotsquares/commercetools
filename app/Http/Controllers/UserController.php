<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommercetoolsController as CT;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public  $url;
    public  $client;

    public function __construct()
    {
        $this->url = url('/api');
        $this->client = new \GuzzleHttp\Client();
    }

    public function orderById($orderId = null)
    {
        if (!getCustomer()) {
            return redirect()->route('login');
        }

        if ($orderId == null) {
            return redirect()->route('userDashboard');
        }

        $result = $this->client->post(url('/api') . '/order' . '/' . $orderId);
        $output['order'] = json_decode($result->getBody());

        $output['orderId'] = $orderId;
        $output['paymentId'] = $output['order']->paymentInfo->payments[0]->id;
        
        $output['orderAmount'] = DB::table('orders')
        ->where('order_id', $orderId)
        ->first();

        $totalAmount =  $output['order']->totalPrice->centAmount;
        $orderAmount =  $output['orderAmount']->centAmount;
        $output['offerPercentage'] = sprintf('%0.2f', (($totalAmount - $orderAmount) / $totalAmount) * 100);

        return view('pages.orderView', $output);
    }

    public function refundById($orderId = null, $paymnetId = null)
    {
        $output['msg'] = "";
        if (!getCustomer()) {
            return redirect()->route('login');
        }

        if ($orderId == null) {
            return redirect()->route('userDashboard');
        }

        $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/order' . '/' . $orderId);
        $result = $this->client->sendAsync($request)->wait();
        $order = json_decode($result->getBody());

        $suparpayment = DB::table('suparpayment')
            ->where('externalReference', $order->orderNumber)
            ->first();

        if (!isset(Session::get('ct_suparpay_refund')->transactionId)) {
            $result = refundSuperPayment($suparpayment->transactionId, $suparpayment->transactionAmount, $orderId);
            // $result = refundSuperPayment('4e726b93-4cca-47cc-922b-b7acd690a000', '10');
            Session::put('ct_suparpay_refund', $result);
        }
        $temp_transactionId = Session::get('ct_suparpay_refund')->transactionId;

        if (isset($result->statusCode) && $result->statusCode == 400) {
            $output['msg'] .= "This order already refunded. Please your payment will revert into same account.";
        } else {
            $refundDetail = $this->checkRefundSuperPayment($temp_transactionId);
            // dd($refundDetail);
            $refundDetail = json_encode($refundDetail);

            DB::table('orders')->where('order_id', $orderId)->update(array(
                'refundDetail' => $refundDetail,
            ));

            //Return Order From Commercetools
            $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/cancel-order' . '/' . $orderId . '/' . $paymnetId);
            $result = $this->client->sendAsync($request)->wait();
            $result = json_decode($result->getBody());

            $output['msg'] .= "Succesfully order cancelled.";
        }

        return view('pages.refundSuccess', $output);
    }

    public function checkRefundSuperPayment($transactionId = null)
    {
        if (!getCustomer()) {
            return redirect()->route('login');
        }

        if ($transactionId == null) {
            return redirect()->route('userDashboard');
        }

        return checkRefundSuperPayment($transactionId);
    }
}

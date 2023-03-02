@extends('layouts.default')
@section('content')

<div class="row">

    <div class="col-9">
        <h4 style="margin-bottom: 15px;">Order Detail</h4>
        <div style="border: 1px solid silver; border-radius: 4px; padding: 20px 20px 0px; box-shadow: silver 0px 0px 6px 0px;">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-2">Image</div>
                <div class="col-5">Name</div>
                <div class="col-2">Quantity</div>
                <div class="col-3">Amount</div>
            </div>
            @foreach ($order->lineItems as $item)
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-2"><img alt="shopping cart item" src="{{$item->variant->images[0]->url}}" style="height: 80px;"></div>
                <div class="col-5">{{ $item->name->en }}</div>
                <div class="col-2">{{ $item->quantity }}</div>
                <div class="col-3"><del>{{config('commercetools.currencySymbol')}}{{$item->price->value->centAmount/100}}</del> {{config('commercetools.currencySymbol')}}{{($item->price->value->centAmount - $item->price->value->centAmount * $offerPercentage/100)/100}}</div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-3">
        <h4 style="margin-bottom: 15px;">Action</h4>
        <div style="border: 1px solid silver; border-radius: 4px; padding: 20px; box-shadow: silver 0px 0px 6px 0px;">
            <!-- <a class="form-control btn btn-success text-white my-1">Pay</a> -->

            @if($orderAmount->refundDetail)
            <a class="form-control btn btn-secondary text-white my-1 disabled">Cancelled</a>
            @else
            <a class="form-control btn btn-danger text-white my-1" href="/refund/{{$orderId}}/{{$paymentId}}">Refund</a>
            @endif
        </div>
    </div>
</div>
@stop
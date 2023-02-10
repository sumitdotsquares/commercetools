@extends('layouts.default')
@section('content')

<div class="row">
   <div class="col-12">
      {!! $supar_pay_offer->content->title !!}
      {!! $supar_pay_offer->content->description !!}
   </div>
   <div class="col-6">
      <h4 style="margin-bottom: 15px;">Your Cart</h4>
      <div style="border: 1px solid silver; border-radius: 4px; padding: 20px 20px 0px; box-shadow: silver 0px 0px 6px 0px;">
         @foreach ($cart_items as $item)
         <div class="row" style="margin-bottom: 20px;">
            <div class="col-3"><img alt="shopping cart item" src="{{$item->variant->images[0]->url}}" style="height: 80px;"></div>
            <div class="col-5">{{ $item->name->en }}</div>
            <div class="col-4">{{config('commercetools.currencySymbol')}} {{$item->price->value->centAmount/100}}</div>
         </div>
         @endforeach
      </div>
   </div>
   <div class="col-6">
      <h4 style="margin-bottom: 15px;">Your Information</h4>
      @php
      $ct_customer_email = (isset($ct_customer->email)) ? $ct_customer->email : '';
      $ct_customer_name = (isset($ct_customer->firstName)) ? $ct_customer->firstName.' '. $ct_customer->lastName : '';
      $ct_customer_street = (isset($ct_customer->addresses[0]->streetName)) ? $ct_customer->addresses[0]->streetName : '';
      $ct_customer_city = (isset($ct_customer->addresses[0]->city)) ? $ct_customer->addresses[0]->city : '';
      $ct_customer_country = (isset($ct_customer->addresses[0]->country)) ? $ct_customer->addresses[0]->country : '';
      $redirectUrl = (isset($supar_pay_payment->redirectUrl)) ? $supar_pay_payment->redirectUrl : 'javascript:void(0);';
      @endphp
      <form id="checkout" method="post" action="/chechout" style="border: 1px solid silver; border-radius: 4px; padding: 20px 20px 10px; box-shadow: silver 0px 0px 6px 0px;">
         <div class="row mb-2">
            <div class="col"><label for="email">Email</label>
               <input type="text" class="form-control" id="email" value="{{ $ct_customer_email }}" onchange="checkUser(this);">
            </div>
            <div class="col"><label for="name">Password (N/A)</label>
               <input type="password" class="form-control" id="password" onchange="checkUser(this);" value="aaaaa">
            </div>
         </div>
         <div class="row mb-2">
            <div class="col"><label for="name">Name</label>
               <input type="text" class="form-control" id="name" value="{{ $ct_customer_name }}">
            </div>
            <div class="col"><label for="address">Address</label>
               <input type="text" class="form-control" id="address" value="{{ $ct_customer_street }}">
            </div>
         </div>
         <div class="row mb-3">
            <div class="col"><label for="city">City</label>
               <input type="text" class="form-control" id="city" value="{{ $ct_customer_city }}">
            </div>
            <div class="col">
               <label for="country">country</label>
               @include('includes.country')
            </div>
         </div>
         <div class="row mb-2 checkoutPane">
            <div class="col-12    mb-2">
               <a class="form-control btn btn-primary" href="{{$redirectUrl}}" style="color: white; border: 0px;">Pay with Super Pay € {{$supar_pay_offer->calculation->amountAfterSavings}} now</a>

            </div>
            <div class="col-12">
               {!! $supar_pay_offer->content->banner !!}
            </div>
         </div>

      </form>


   </div>
</div>
@stop
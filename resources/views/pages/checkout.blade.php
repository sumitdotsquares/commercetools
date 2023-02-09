@extends('layouts.default')
@section('content')
<div class="row">
   <?php getSuperPayOffer(); ?>
   <div class="col-6">
      <h4 style="margin-bottom: 15px;">Your Cart</h4>
      <div style="border: 1px solid silver; border-radius: 4px; padding: 20px 20px 0px; box-shadow: silver 0px 0px 6px 0px;">
         @foreach ($cart_items as $item)
         <div class="row" style="margin-bottom: 20px;">
            <div class="col-4"><img alt="shopping cart item" src="{{$item->variant->images[0]->url}}" style="height: 80px;"></div>
            <div class="col-4">{{ $item->name->en }}</div>
            <div class="col-2">{{config('commercetools.currencySymbol')}} {{$item->price->value->centAmount/100}}</div>
         </div>
         @endforeach
      </div>
   </div>
   <div class="col-6">
      <h4 style="margin-bottom: 15px;">Your Information</h4>
      <form id="checkout" method="post" action="/chechout" style="border: 1px solid silver; border-radius: 4px; padding: 20px 20px 10px; box-shadow: silver 0px 0px 6px 0px;">
         <div class="row mb-2">
            <div class="col"><label for="email">Email</label>
               <input type="text" class="form-control" id="email" value="" onchange="checkUser(this);">
            </div>
            <div class="col"><label for="name">Password (N/A)</label>
               <input type="password" class="form-control" id="password" onchange="checkUser(this);" value="aaaaa">
            </div>
         </div>
         <div class="row mb-2">
            <div class="col"><label for="name">Name</label>
               <input type="text" class="form-control" id="name" value="">
            </div>
            <div class="col"><label for="address">Address</label>
               <input type="text" class="form-control" id="address" value="">
            </div>
         </div>
         <div class="row mb-3">
            <div class="col"><label for="city">City</label>
               <input type="text" class="form-control" id="city" value="">
            </div>
            <div class="col">
               <label for="country">country</label>
               @include('includes.country')
            </div>
         </div>
         <div class="row mb-2 checkoutPane">
            <div class="col">
               <a class="form-control btn btn-primary" href="https://checkout.staging.superpayments.com/PaymentSummary/992c9559-8d73-4207-ad84-c4f7ee9d8941" style="color: white; border: 0px;">Pay with Super Pay € 149.00 now</a>
            </div>
         </div>

      </form>


   </div>
</div>
@stop
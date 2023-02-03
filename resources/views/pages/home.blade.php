@extends('layouts.default')
@section('content')
<div class="row row-cols-1 row-cols-lg-5 g-4">
   @foreach ($products as $product)
   <div class="col">
      <div class="h-100" style="border-radius: 5px; box-shadow: silver 0px 0px 6px 0px; cursor: pointer; border: 1px solid silver;">
      <img alt="product" src="{{$product['image']}}" class="w-100" style="max-height: 150px; object-fit: contain; margin-left: auto; margin-right: auto; border-radius: 5px 5px 0px 0px;">
         <div class="card-body" style="padding-bottom: 0px;">
            <h5 style="font-size: 1.1em; font-weight: 500;">{{$product['name']}}</h5>
            <p>{{config('commercetools.currencySymbol')}} {{$product['price']->getCentAmount()/100}}</p>
            <a href="/add-to-cart/{{$product['id']}}" class="btn">Add to Cart</a>
         </div>
      </div>
   </div>
   @endforeach

</div>
@stop
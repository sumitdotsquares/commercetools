@extends('layouts.default')
@section('content')

<div class="row">
   <div class="col-12">
      <h2>My Order</h2>
   </div>
</div>

<div class="row">
   <table class="table table-striped">
      <thead>
         <tr>
            <th scope="col">#</th>
            <th scope="col">Order Id</th>
            <th scope="col">Amount</th>
            <th scope="col">Create at</th>
            <th scope="col">Action</th>
         </tr>
      </thead>
      <tbody>
         @php($i = 0)
         @foreach ($orders as $order)
         <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $order->externalReference }}</td>
            <td>{{config('commercetools.currencySymbol')}} {{ $order->centAmount/100 }}</td>
            <td>{{ $order->timestamp }}</td>
            <td><a href="/order/{{ $order->order_id }}">View</a></td>
         </tr>
         @endforeach
      </tbody>
   </table>
</div>
@stop
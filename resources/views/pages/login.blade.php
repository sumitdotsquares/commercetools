@extends('layouts.default')
@section('content')

<div class="row">
   <div class="col-3"></div>
   <div class="col-6">
      <h4 style="margin-bottom: 15px;">Login</h4>
      @if($msg)
      <div class="alert alert-danger" role="alert">
         {{$msg}}
      </div>
      @endif
      <form id="checkout" method="post" action="/login" style="border: 1px solid silver; border-radius: 4px; padding: 20px 20px 10px; box-shadow: silver 0px 0px 6px 0px;">
         @csrf
         <div class="row mb-2">
            <div class="col"><label for="email">Email</label>
               <input type="text" class="form-control" id="email" name="email" value="">
            </div>
         </div>

         <div class="row mb-2">
            <div class="col"><label for="name">Password (N/A)</label>
               <input type="password" class="form-control" id="password" name="password" value="">
            </div>
         </div>

         <div class="row mb-2 checkoutPane">
            <div class="col-12 mb-2">
               <input type="submit" class="form-control btn btn-primary text-white" value="Login" />
            </div>
         </div>
      </form>

   </div>
   <div class="col-3"></div>
</div>

@stop
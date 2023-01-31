<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('includes.head')
</head>

<body>

    <div class="container h-100" id="app">
        <div class="row" style="margin-top: 30px; padding-top: 20px; margin-bottom: 40px;">
            @include('includes.header')
        </div>
        
        @yield('content')
        
    </div>


</body>

</html>
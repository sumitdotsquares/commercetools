<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommercetoolsController as CT;

class HomeController extends Controller
{
    public function index()
    {
        $ct = new CT();
        echo $ct->getProducts();
        return view( 'pages.home' );
    }
}

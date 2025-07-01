<?php
namespace App\Controllers;

class Mapcenter extends BaseController
{
    public function index()
    {
        return view('includes/header',['isMapPage' => true])
             . view('mapcenter')
             . view('includes/footer');
    }
}

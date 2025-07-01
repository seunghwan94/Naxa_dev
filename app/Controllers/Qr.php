<?php
namespace App\Controllers;

class Qr extends BaseController
{
    public function index()
    {
        // QR 페이지임을 나타내는 플래그 (CSS 적용용)
        $data = ['isQrPage' => true];

        return view('includes/header', $data)
             . view('qr')
             . view('includes/footer');
    }
}

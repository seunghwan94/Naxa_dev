<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $file = (ENVIRONMENT === 'development')
            ? ROOTPATH . 'public/static/programs.dev.json'
            : ROOTPATH . 'public/static/programs.prod.json';

        $programsJson = file_get_contents($file);
        $programs     = json_decode($programsJson, true);

        // 데이터와 함께 뷰 반환
        return view('includes/header')
             . view('index', ['programs' => $programs])
             . view('includes/footer');
    }
}

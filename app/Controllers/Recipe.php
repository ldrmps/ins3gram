<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Recipe extends BaseController
{
    public function index()
    {
        return $this->view('front/recipe/index', [], false);
    }

    public function show($any){
        return $this->view('front/recipe/show', ['any' => $any], false);
    }
}

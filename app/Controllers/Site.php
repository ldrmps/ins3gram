<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Site extends BaseController
{
    public function forbidden()
    {
        return $this->view('templates/forbidden', [], false);
    }

    public function show404()
    {
        return $this->view('templates/404', [], false);
    }

    public function testPagination() {
        $recipeModel = Model('RecipeModel');
        // Test basique

        var_dump($recipeModel->getAllRecipes()); // Génère les liens HTML
    }
}
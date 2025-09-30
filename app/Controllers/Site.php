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
        $recipes = $recipeModel->paginate(8);
        $pager = $recipeModel->pager;
        echo "<h3>Test pagination :</h3>";
        echo "Nombre de recettes récupérées : " . count($recipes) . "<br>";
        echo "Page actuelle : " . $pager->getCurrentPage() . "<br>";
        echo "Nombre total d'éléments : " . $pager->getTotal() . "<br>";
        echo "Nombre de pages : " . $pager->getPageCount() . "<br>";
        var_dump($pager->links()); // Génère les liens HTML
    }
}
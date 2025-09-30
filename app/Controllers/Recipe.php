<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RecipeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Recipe extends BaseController
{
    public function index()
    {
        helper(['form']);
        // Récupération des filtres
        $filters = [
            'alcool' => $this->request->getGet('alcool'),
            'search' => $this->request->getGet('search'),
            'sort' => $this->request->getGet('sort'),
            'ingredients' => $this->request->getGet('ingredients'),
            // TODO : autres filtres
        ];
        // Paramètres de tri et pagination
        $orderBy = $this->request->getGet('order_by') ?? 'name';
        $perPage = (int)($this->request->getGet('per_page') ?? 8);
        // Validation du nombre d'éléments par page
        $allowedPerPage = [8, 16, 24];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 8;
        }

        $currentPage = (int)($this->request->getGet('page') ?? 1);
        // Appel du Model
        $recipeModel = Model('RecipeModel');
        $result = $recipeModel->getAllRecipes($filters, $orderBy, 'ASC', $perPage, $currentPage);
        return $this->view('front/recipe/index', [
            'recipes' => $result['data'],
            'pager' => $result['pager'],
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'filters' => $filters
        ], false);
    }

    public function show($slug) {
        $rm = Model('RecipeModel');
        $recipe = $rm->getFullRecipe(null,$slug);
        if($recipe) {
            $this->title = "Recette : " . $recipe['name'];
            return $this->view('front/recipe/show', ['recipe' => $recipe], false);
        }
        return $this->view('templates/404.php', [],false);
    }
}

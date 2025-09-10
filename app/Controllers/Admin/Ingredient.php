<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Ingredient extends BaseController
{
    protected $breadcrumb = [['text'=>'Tableau de Bord', 'url' => "/admin/dashboard"],['text'=>"Ingrédients", 'url' => '']];
    public function index()
    {
        helper('form');
        return $this->view('/admin/ingredient/index');
    }

    public function edit($id_ingredient) {
        helper('form');
        $igm = Model('IngredientModel');
        $ingredient = $igm->find($id_ingredient);
        if (!$ingredient) {
            $this->error('Ingredient inexistant');
            return $this->redirect('/admin/ingredient');
        }
        $brand = Model('BrandModel')->findAll();
        $category = Model('CategIngModel')->findAll();
        return $this->view('/admin/ingredient/form', ['ingredient' => $ingredient, 'brand' => $brand, 'category' => $category]);
    }
    public function insert(){
        //
    }
    public function create() {
        helper('form');
        return $this->view('/admin/ingredient/form');
    }

    public function delete() {
        $igm = model('IngredientModel');
        $id = $this->request->getPost('id');
        if ($igm->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "L'ingrédient à été supprimé avec succès !",
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $igm->errors(),
            ]);
        }
    }

    public function update() {
        //
    }

    public function search()
    {
        $request = $this->request;

        // Vérification AJAX
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête non autorisée']);
        }

        $im = Model('IngredientModel');

        // Paramètres de recherche
        $search = $request->getGet('search') ?? '';
        $page = (int)($request->getGet('page') ?? 1);
        $limit = 20;

        // Utilisation de la méthode du Model (via le trait)
        $result = $im->quickSearchForSelect2($search, $page, $limit);

        // Réponse JSON
        return $this->response->setJSON($result);
    }

}
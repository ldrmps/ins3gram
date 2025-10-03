<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Ingredient extends BaseController
{
    public function index(){
        return $this->view('/admin/ingredient/index');
    }
    public function create(){
        helper(['form']);
        return $this->view('/admin/ingredient/form');
    }

    public function insert() {
        $image = $this->request->getFiles();
        $data = $this->request->getPost();
        echo "<pre>";
        $id_ingredient = Model('IngredientModel')->insert($data);
        foreach($image['image'] as $img) {
            print_r(upload_file($img,'ingredient',$data['name'],['entity_type' => 'ingredient',"entity_id" => $id_ingredient], true));
        }
        die();



        die();
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
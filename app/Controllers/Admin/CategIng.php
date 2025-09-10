<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CategIng extends BaseController
{
    public function index()
    {
        helper(['form']);
        $categ = Model('CategIngModel')->orderBy('name')->findAll();
        return $this->view('/admin/categ-ing', ['categ' => $categ]);
    }

    public function insert()
    {
        $cim = model('CategIngModel');
        $data = $this->request->getPost();
        if(empty($data['id_categ_parent'])) unset($data['id_categ_parent']);
        if ($cim->insert($data)) {
            $this->success('Catégorie d\'ingrédients bien créée');
        } else {
            foreach ($cim->errors() as $error) {
                $this->error($error);
            }
        }
        return $this->redirect('admin/category-ingredient');
    }

    public function update()
    {
        //
    }

    public function delete()
    {
        //
    }
    public function search()
    {
        $request = $this->request;

        // Vérification AJAX
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête non autorisée']);
        }

        $cim = model('CategIngModel');

        // Paramètres de recherche
        $search = $request->getGet('search') ?? '';
        $page = (int)($request->getGet('page') ?? 1);
        $limit = 20;

        // Utilisation de la méthode du Model (via le trait)
        $result = $cim->quickSearchForSelect2($search, $page, $limit);

        // Réponse JSON
        return $this->response->setJSON($result);
    }
}
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Unit extends BaseController
{
    public function index() {
        helper('form');
        return $this->view('admin/unit');
    }
    public function insert() {
        $um = Model('UnitModel');
        $data=$this->request->getPost();
        if($um->insert($data)) {
            $this->success ("Unité crée avec succès !");
        } else {
            foreach ($um->errors() as $key => $error) {
                $this->error($error);
            }
        }
        return $this->redirect('admin/unit');
    }
    public function update() {
        $um = Model('UnitModel');
        $data=$this->request->getPost();
        $id=$data['id'];
        unset($data['id']);
        if ($um->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unité modifiée avec succès !',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $um->errors()
            ]);
        }
    }
    public function delete() {
        $um = Model('UnitModel');
        $id = $this->request->getPost('id');
        if ($um->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unité supprimée !',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $um->errors()
            ]);
        }
    }
    public function search()
    {
        $request = $this->request;
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête non autorisée']);
        }

        $um = Model('UnitModel');
        $search = $request->getGet('search') ?? '';
        $page = (int)($request->getGet('page') ?? 1);
        $limit = 20;

        $result = $um->quickSearchForSelect2($search, $page, $limit);
        return $this->response->setJSON($result);
    }
}
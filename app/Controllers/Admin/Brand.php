<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Brand extends BaseController
{
    protected $breadcrumb = [['text'=>'Tableau de Bord', 'url' => "/admin/dashboard"],['text'=>"Marques", 'url' => '']];

    public function index()
    {
        helper('form');
        return $this->view('admin/brand');
    }

    public function insert()
    {
        $upm = model('BrandModel');
        $data = $this->request->getPost();
        if ($upm->insert($data)) {
            $this->success('Marque bien créée');
        } else {
            foreach ($upm->errors() as $error) {
                $this->error($error);
            }
        }
        return $this->redirect('admin/brand');
    }

    public function update() {
        $upm = model('BrandModel');
        $data = $this->request->getPost();
        $id = $data['id'];
        unset($data['id']);
        if ($upm->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "La marque à été modifiée avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $upm->errors(),
            ]);
        }
    }

    public function delete() {
        $upm = model('BrandModel');
        $id = $this->request->getPost('id');
        if ($upm->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "La marque à été supprimée avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $upm->errors(),
            ]);
        }
    }
}
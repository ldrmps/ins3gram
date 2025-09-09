<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Tag extends BaseController
{
    protected $breadcrumb = [['text'=>'Tableau de Bord', 'url' => "/admin/dashboard"],['text'=>"Mot Clés", 'url' => '']];

    public function index()
    {
        helper('form');
        return $this->view('admin/tag');
    }

    public function insert()
    {
        $upm = model('TagModel');
        $data = $this->request->getPost();
        if ($upm->insert($data)) {
            $this->success('Mot clé bien créé');
        } else {
            foreach ($upm->errors() as $error) {
                $this->error($error);
            }
        }
        return $this->redirect('admin/tag');
    }

    public function update() {
        $upm = model('TagModel');
        $data = $this->request->getPost();
        $id = $data['id'];
        unset($data['id']);
        if ($upm->update($id, $data)) {
            return $this->response->setJSON([
                    'success' => true,
                    'message' => "Le mot clé à été modifié avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                    'success' => false,
                    'message' => $upm->errors(),
            ]);
        }
    }

    public function delete() {
        $upm = model('TagModel');
        $id = $this->request->getPost('id');
        if ($upm->delete($id)) {
            return $this->response->setJSON([
                    'success' => true,
                    'message' => "Le mot clé à été supprimé avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                    'success' => false,
                    'message' => $upm->errors(),
            ]);
        }
    }
}
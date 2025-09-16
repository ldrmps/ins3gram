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
        $bm = model('BrandModel');
        $data = $this->request->getPost();
        $image = $this->request->getFile('image');
        if ($id_brand = $bm->insert($data)) {
            $this->success('Marque bien créée');
            if($image && $image->getError() !== UPLOAD_ERR_NO_FILE){
                $mediaData = [
                    'entity_type' => 'brand',
                    'entity_id' => $id_brand,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                // Utiliser la fonction upload_file() de l'utils_helper pour gérer l'upload et les données du média
                $uploadResult = upload_file($image, 'brand', $image->getName(), $mediaData,false);
                // Vérifier le résultat de l'upload
                if (is_array($uploadResult) && $uploadResult['status'] === 'error') {
                    // Afficher un message d'erreur détaillé
                    $this->error("Une erreur est survenue lors de l'upload de l'image : " . $uploadResult['message']);
                }
            }
        } else {
            foreach ($bm->errors() as $error) {
                $this->error($error);
            }
        }
        return $this->redirect('admin/brand');
    }

    public function update() {
        $bm = model('BrandModel');
        $data = $this->request->getPost();
        $id = $data['id'];
        unset($data['id']);
        if ($bm->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "La marque à été modifiée avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $bm->errors(),
            ]);
        }
    }

    public function delete() {
        $bm = model('BrandModel');
        $id = $this->request->getPost('id');
        if ($bm->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "La marque à été supprimée avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $bm->errors(),
            ]);
        }
    }
    public function search()
    {
        $request = $this->request;

        // Vérification AJAX
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête non autorisée']);
        }

        $upm = Model('BrandModel');

        // Paramètres de recherche
        $search = $request->getGet('search') ?? '';
        $page = (int)($request->getGet('page') ?? 1);
        $limit = 20;

        // Utilisation de la méthode du Model (via le trait)
        $result = $upm->quickSearchForSelect2($search, $page, $limit);

        // Réponse JSON
        return $this->response->setJSON($result);
    }
}
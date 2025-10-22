<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Media extends BaseController
{
    public function index()
    {

        return $this->view('admin/media');
    }

    public function load() {
        $page = $this->request->getGet('page') ?? 1;
        $entity_type = $this->request->getGet('entity_type') ?? null;
        $medias = Model('MediaModel')->getMedias($page, 12, $entity_type);
        return $this->response->setJson([$medias['data'], $medias['pager']->getPageCount()]);
    }

    public function delete() {
        $id = $this->request->getPost('id');
        $mm = Model('MediaModel');
        $media = $mm->find($id);
        if (!$media) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Media introuvable'
            ]);
        }
        if (!$media->delete()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ]);
        }
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Media supprimé'
        ]);
    }
}
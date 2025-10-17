<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Media extends BaseController
{
    public function index()
    {
        $medias = Model('MediaModel')->getMedias(1,6);
        return $this->view('admin/media', [
            'medias' => $medias['data'],
            'pager' => $medias['pager']
        ]);
    }

    public function loadMore() {
        $page = $this->request->getGet('page');
        $medias = Model('MediaModel')->getMedias($page, 6);
        return $this->response->setJson($medias['data']);
    }
}
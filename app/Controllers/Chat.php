<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ChatModel;
use CodeIgniter\HTTP\ResponseInterface;

class Chat extends BaseController
{
    public function index()
    {
        $this->title = "Chat";
        return $this->view('/front/chat', [], false);
    }

    public function send() {
        $data = esc($this->request->getPost());
        $cm = Model('ChatModel');
        if($cm->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Message envoyé',
                'data' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $cm->errors()
            ]);
        }
    }

    public function conversation() {
        $data = $this->request->getGet();
        $cm = Model('ChatModel');
        $conversation = $cm->getConversation($data['id_1'], $data['id_2']);
        return $this->response->setJSON($conversation);
    }
}
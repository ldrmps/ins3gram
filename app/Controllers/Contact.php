<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Contact extends BaseController
{
    public function index()
    {
        helper(['form']);
        return $this->view('front/contact', [], false);
    }

    public function send() {
        helper(['form']);
        $rules = [
            'subject' => [
                'label' => 'Objet',
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Veuillez saisir un objet.',
                    'min_length' => "L'objet doit faire au moins 3 caractères",
                ],
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[255]',
                'errors' => [
                    'required' => 'Veuillez saisir votre adresse email.',
                    'valid_email' => 'Veuillez saisir une adresse email valide.',
                    'max_length' => "L'email ne dois pas depasser 255 caractères"
                ]
            ]
        ];
        $data = $this->request->getPost();
        if(!$this->validate($rules)) {
            return $this->redirect('/contactez-nous',['errors' => $this->validator,'data'=>$data]);
        }
        $email = service('email');
        $email->setTo('form@localhost');
        $email->setFrom($data['email']);
        $email->setSubject($data['subject']);
        $email->setMessage(esc($data['message']));
        if ($email->send()) {
            echo "E-mail envoyé avec succès !";
        } else {
            echo $email->printDebugger(['headers']);
        }
    }
}
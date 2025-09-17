<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Recipe extends BaseController
{
    protected $breadcrumb = [['text' => 'Tableau de Bord', 'url' => '/admin/dashboard']];

    protected $title = 'Recettes';

    public function index()
    {
        $this->addBreadcrumb('Recettes', "");
        return $this->view('admin/recipe/index');
    }

    public function create()
    {
        helper('form');
        $this->addBreadcrumb('Recettes', "/admin/recipe");
        $this->addBreadcrumb('Création d\'une recette', "");
        $tags = Model('TagModel')->findAll();
        return $this->view('admin/recipe/form', ['tags' => $tags]);
    }

    public function edit($id_recipe)
    {
        helper('form');
        $this->addBreadcrumb('Recettes', "/admin/recipe");
        $this->addBreadcrumb('Modification d\'une recette', "");
        $recipe = Model('RecipeModel')->withDeleted()->find($id_recipe);
        if (!$recipe) {
            $this->error('Recette introuvable');
            return $this->redirect('/admin/recipe');
        }
        $user = Model('UserModel')->withDeleted()->find($recipe['id_user']);
        unset($recipe['id_user']);
        $recipe['user'] = $user;
        $ingredients = Model('QuantityModel')->getQuantityByRecipe($id_recipe);
        $tags = Model('TagModel')->findAll();
        $recipe['ingredients'] = $ingredients;
        $recipe_tags = Model('TagRecipeModel')->where('id_recipe', $id_recipe)->findAll();
        foreach ($recipe_tags as $recipe_tag) {
            $recipe['tags'][] = $recipe_tag['id_tag'];
        }
        $mediamodel = Model('MediaModel');
        $recipe['mea'] = $mediamodel->where('entity_id', $id_recipe)->where('entity_type', 'recipe_mea')->first();
        $recipe['images'] = $mediamodel->where('entity_id', $id_recipe)->where('entity_type', 'recipe')->findAll();
        $steps = Model('StepModel')->where('id_recipe', $id_recipe)->orderBy('order', 'ASC')->findAll();
        $recipe['steps'] = $steps;
        return $this->view('admin/recipe/form', ['tags' => $tags, 'recipe' => $recipe]);
    }

    public function insert()
    {
        $data = $this->request->getPost();
        $rm = Model('RecipeModel');

        //Ajout de ma recette + récupération de l'ID de ma recette ajouté
        if ($id_recipe = $rm->insert($data, true)) {
            $this->success('Recette créée avec succès !');
            //Gestion activation / désactivation
            if (!isset($data['active'])) {
                $rm->delete($id_recipe);
            }

            if (isset($data['ingredients'])) {
                $qm = Model('QuantityModel');
                //Ajout des ingrédients
                foreach ($data['ingredients'] as $ingredient) {
                    $ingredient['id_recipe'] = $id_recipe;
                    if ($qm->insert($ingredient)) {
                        $this->success('Ingrédient ajouté avec succès !');
                    } else {
                        foreach ($qm->errors() as $error) {
                            $this->error($error);
                        }
                    }
                }
            }

            if (isset($data['tags'])) {
                //Ajout des mots clés (avec création d'un tableau)
                $trm = Model('TagRecipeModel');
                $tag_recipe = array();
                $tag_recipe['id_recipe'] = $id_recipe;
                foreach ($data['tags'] as $id_tag) {
                    $tag_recipe['id_tag'] = $id_tag;
                    if ($trm->insert($tag_recipe)) {
                        $this->success('Mot clés ajouté avec succès à la recette !');
                    } else {
                        foreach ($trm->errors() as $error) {
                            $this->error($error);
                        }
                    }
                }
            }

            if (isset($data['steps'])) {
                $sm = Model('StepModel');
                foreach ($data['steps'] as $order => $step) {
                    $step['id_recipe'] = $id_recipe;
                    $step['order'] = $order;
                    if ($sm->insert($step)) {
                        $this->success('Étape ajoutée avec succès à la recette !');
                    } else {
                        foreach ($sm->errors() as $error) {
                            $this->error($error);
                        }
                    }
                }
            }

            //images mea
            $mea = $this->request->getFile('mea');
            if($mea && $mea->getError() !== UPLOAD_ERR_NO_FILE){
                $mediaData = [
                    'entity_type' => 'recipe_mea',
                    'entity_id' => $id_recipe,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                // Utiliser la fonction upload_file() de l'utils_helper pour gérer l'upload et les données du média
                $uploadResult = upload_file($mea, 'recipe/mea', $mea->getName(), $mediaData,false);
                // Vérifier le résultat de l'upload
                if (is_array($uploadResult) && $uploadResult['status'] === 'error') {
                    // Afficher un message d'erreur détaillé
                    $this->error("Une erreur est survenue lors de l'upload de l'image : " . $uploadResult['message']);
                }
            }
        } else {
            foreach ($rm->errors() as $error) {
                $this->error($error);
            }
        }
        return $this->redirect('/admin/recipe');
    }

    public function update()
    {
        $data = $this->request->getPost();
        $id_recipe = $data['id_recipe'];
        $rm = Model('RecipeModel');
        if ($rm->update($id_recipe, $data)) {
            $this->success('Recette modifiée avec succès !');
            //Gestion activation / désactivation
            if (isset($data['active'])) {
                $rm->reactive($id_recipe);
            } else {
                $rm->delete($id_recipe);
            }
            //Gestion des mots clés
            $trm = Model('TagRecipeModel');
            if ($trm->where('id_recipe', $id_recipe)->delete()) {
                if (isset($data['tags'])) {
                    //Ajout des mots clés (avec création d'un tableau)
                    $tag_recipe = array();
                    $tag_recipe['id_recipe'] = $id_recipe;
                    foreach ($data['tags'] as $id_tag) {
                        $tag_recipe['id_tag'] = $id_tag;
                        if ($trm->insert($tag_recipe)) {
                            //nothing
                        } else {
                            foreach ($trm->errors() as $error) {
                                $this->error($error);
                            }
                        }
                    }
                }
            } else {
                foreach ($rm->errors() as $error) {
                    $this->error($error);
                }
            }

            //Gestion des ingrédients
            $qm = Model('QuantityModel');
            if ($qm->where('id_recipe', $id_recipe)->delete()) {
                if (isset($data['ingredients'])) {
                    $qm = Model('QuantityModel');
                    //Ajout des ingrédients
                    foreach ($data['ingredients'] as $ingredient) {
                        $ingredient['id_recipe'] = $id_recipe;
                        if ($qm->insert($ingredient)) {
                            //$this->success('Ingrédient ajouté avec succès !');
                        } else {
                            foreach ($qm->errors() as $error) {
                                $this->error($error);
                            }
                        }
                    }
                }
            } else {
                foreach ($rm->errors() as $error) {
                    $this->error($error);
                }
            }

            if (isset($data['steps'])) {
                $sm = Model('StepModel');
                $existing_steps = $sm->where('id_recipe', $id_recipe)->findAll();
                $check_existing_steps = array();
                foreach ($existing_steps as $step) {
                    $check_existing_steps[] = $step['id'];
                }
                foreach ($data['steps'] as $order => $step) {
                    //Pas d'ID alors c'est un ajout
                    if (!isset($step['id'])) {
                        //insert
                        $step['id_recipe'] = $id_recipe;
                        $step['order'] = $order;
                        if ($sm->insert($step)) {
                            //$this->success('Étape ajoutée avec succès à la recette !');
                        } else {
                            foreach ($sm->errors() as $error) {
                                $this->error($error);
                            }
                        }
                    } else {
                        //Si j'ai un ID et qu'il est présent dans ma BDD c'est une modification
                        if (($key = array_search($step['id'], $check_existing_steps)) !== FALSE) {
                            //update + unset(id)
                            $step['order'] = $order;
                            if ($sm->update($step['id'], $step)) {
                                //$this->success('Étape modifiée avec succès !');
                            } else {
                                foreach ($sm->errors() as $error) {
                                    $this->error($error);
                                }
                            }
                            unset($check_existing_steps[$key]);
                        }
                    }
                }
                foreach ($check_existing_steps as $id) {
                    //delete
                    foreach ($check_existing_steps as $id_step) {
                        if ($sm->delete($id_step)) {
                            $this->success('Étape supprimée avec succès !');
                        } else {
                            foreach ($sm->errors() as $error) {
                                $this->error($error);
                            }
                        }
                    }
                }
            }

            //images mea
            $mea = $this->request->getFile('mea');
            if($mea && $mea->getError() !== UPLOAD_ERR_NO_FILE){
                $mediaData = [
                    'entity_type' => 'recipe_mea',
                    'entity_id' => $id_recipe,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                // Utiliser la fonction upload_file() de l'utils_helper pour gérer l'upload et les données du média
                $uploadResult = upload_file($mea, 'recipe/mea', $mea->getName(), $mediaData,false);
                // Vérifier le résultat de l'upload
                if (is_array($uploadResult) && $uploadResult['status'] === 'error') {
                    // Afficher un message d'erreur détaillé
                    $this->error("Une erreur est survenue lors de l'upload de l'image : " . $uploadResult['message']);
                }
            }

        } else {
            foreach ($rm->errors() as $error) {
                $this->error($error);
            }
        }

        return $this->redirect('/admin/recipe');
    }

    public function switchActive()
    {
        $id = $this->request->getPost('id_recipe');
        $recipeModel = model('RecipeModel');

        // Récupérer l'utilisateur (même s'il est soft deleted)
        $recipe = $recipeModel->withDeleted()->find($id);

        if (!$recipe) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Utilisateur introuvable'
            ]);
        }

        if (empty($recipe['deleted_at'])) {
            // Désactiver l'utilisateur (soft delete)
            $recipeModel->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Recette désactivée',
                'status' => 'inactive'
            ]);
        } else {
            // Réactiver avec la méthode restore
            if ($recipeModel->reactive($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Recette activée',
                    'status' => 'active'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de l\'activation'
                ]);
            }
        }
    }
}
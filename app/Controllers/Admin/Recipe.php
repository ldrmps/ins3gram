<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Recipe extends BaseController
{
    protected $breadcrumb = [['text' => 'Tableau de Bord','url' => '/admin/dashboard']];
    public function index()
    {
        $this->addBreadcrumb('Recettes', "");
        return $this->view('admin/recipe/index');
    }

    public function create() {
        helper('form');
        $this->addBreadcrumb('Recettes', "/admin/recipe");
        $this->addBreadcrumb('Création d\'une recette', "");
        $tags = Model('TagModel')->findAll();
        return $this->view('admin/recipe/form', ['tags' => $tags]);
    }
    public function edit($id_recipe) {
        helper('form');
        $this->addBreadcrumb('Recettes', "/admin/recipe");
        $this->addBreadcrumb('Modification d\'une recette', "");
        $recipe = Model('RecipeModel')->find($id_recipe);
        if(!$recipe) {
            $this->error('Recette introuvable');
            return $this->redirect('/admin/recipe');
        }
        $qm = Model('QuantityModel');
        $ingredients = $qm->getQuantityByRecipe($id_recipe);
        $tags = Model('TagModel')->findAll();
        $recipe['ingredients'] = $ingredients;
        $recipe_tags = Model('TagRecipeModel')->where('id_recipe',$id_recipe)->findAll();
        foreach($recipe_tags as $recipe_tag) {
            $recipe['tags'][] = $recipe_tag['id_tag'];
        }
        return $this->view('admin/recipe/form', ['tags' => $tags,'recipe' => $recipe]);
    }

    public function insert() {
        $data = $this->request->getPost();
        $rm = Model('RecipeModel');

        //Ajout de ma recette + récupération de l'ID de ma recette ajouté
        if($id_recipe = $rm->insert($data, true)){
            $this->success('Recette créée avec succès !');
            if(isset($data['ingredients'])) {
                $qm = Model('QuantityModel');
                //Ajout des ingrédients
                foreach($data['ingredients'] as $ingredient) {
                    $ingredient['id_recipe'] = $id_recipe;
                    if ($qm->insert($ingredient)) {
                        $this->success('Ingrédient ajouté avec succès !');
                    } else {
                        foreach($qm->errors() as $error){
                            $this->error($error);
                        }
                    }
                }
            }

            if(isset($data['tags'])) {
                //Ajout des mots clés (avec création d'un tableau)
                $trm = Model('TagRecipeModel');
                $tag_recipe = array();
                $tag_recipe['id_recipe'] = $id_recipe;
                foreach($data['tags'] as $id_tag) {
                    $tag_recipe['id_tag'] = $id_tag;
                    if ($trm->insert($tag_recipe)) {
                        $this->success('Mot clés ajouté avec succès à la recette !');
                    } else {
                        foreach($trm->errors() as $error){
                            $this->error($error);
                        }
                    }
                }
            }
        } else {
            foreach($rm->errors() as $error){
                $this->error($error);
            }
        }
        return $this->redirect('/admin/recipe');
    }

    public function update() {
        $data = $this->request->getPost();
        $id_recipe = $data['id_recipe'];
        $rm = Model('RecipeModel');
        if($rm->update($id_recipe,$data)){
            $this->success('Recette modifiée avec succès !');
            //Gestion des ingredients
            $qm = Model('QuantityModel');
            if($qm->where('id_recipe',$id_recipe)->delete()){
                if(isset($data['ingredients'])) {
                    foreach($data['ingredients'] as $ingredient) {
                        $ingredient['id_recipe'] = $id_recipe;
                        if ($qm->insert($ingredient)) {
                            $this->success('Ingrédient ajouté avec succès !');
                        } else {
                            foreach($qm->errors() as $error){
                                $this->error($error);
                            }
                        }
                    }
                }
            }
            //Gestion des mots clés
            $trm = Model('TagRecipeModel');
            if($trm->where('id_recipe',$id_recipe)->delete()){
                if(isset($data['tags'])) {
                    //Ajout des mots clés (avec création d'un tableau)
                    $tag_recipe = array();
                    $tag_recipe['id_recipe'] = $id_recipe;
                    foreach($data['tags'] as $id_tag) {
                        $tag_recipe['id_tag'] = $id_tag;
                        if ($trm->insert($tag_recipe)) {
                            //nothing
                        } else {
                            foreach($trm->errors() as $error){
                                $this->error($error);
                            }
                        }
                    }
                }
            } else {
                foreach($rm->errors() as $error){
                    $this->error($error);
                }
            }
        } else {
            foreach($rm->errors() as $error){
                $this->error($error);
            }
        }
        return $this->redirect('/admin/recipe');
    }
}
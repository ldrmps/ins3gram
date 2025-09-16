<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\DataTableTrait;
use App\Traits\Select2Searchable;

class UnitModel extends Model
{
    use DataTableTrait;
    use Select2Searchable;
    protected $table            = 'unit';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name'];

    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[unit.name,id,{id}]',
    ];
    protected $validationMessages = [
        'name' => [
            'required'   => 'Le nom de l’unité est obligatoire.',
            'max_length' => 'Le nom de l’unité ne peut pas dépasser 255 caractères.',
            'is_unique'  => 'Cette unité existe déjà.',
        ],
    ];
    protected $select2SearchFields = ['name'];
    protected $select2DisplayField = 'name';
    protected function getDataTableConfig(): array
    {
        return [
            'searchable_fields'=>[
                'name', // Recherche par nom
                'id' // recherche par ID
            ],
            'joins' => [],
            'select' => '*',
        ];
    }
}
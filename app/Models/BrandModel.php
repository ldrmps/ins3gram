<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\Select2Searchable;
use App\Traits\DataTableTrait;
class BrandModel extends Model
{
    use Select2Searchable;
    use DataTableTrait;
    protected $table            = 'brand';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name'];
    protected $useTimestamps = false;
    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[brand.name,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Le nom de la marque est obligatoire.',
            'max_length' => 'Le nom de la marque ne peut pas dépasser 255 caractères.',
            'is_unique'  => 'Cette marque existe déjà.',
        ],
    ];

    protected function getDataTableConfig(): array
    {
        return [
            'searchable_fields' => [
                'brand.name',
                'brand.id',
            ],
            'joins' => [
                [
                    'table' => 'media',
                    'condition' => 'brand.id = media.entity_id AND media.entity_type = \'brand\'',
                    'type' => 'left'
                ]
            ],
            'select' => 'brand.*, media.file_path as image_url',
        ];
    }
    protected $select2SearchFields = ['name'];
    protected $select2DisplayField = 'name';
}
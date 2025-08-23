<?php

namespace App\Packages\SoolarApiPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleBrand extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'soollar';
    protected $table = 'module_brands';
    protected $fillable = [
        'brand',
        'logo',
        'picture',
        'warranty',
        'linear_warranty',
        'active'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkCost extends Model
{
    use HasFactory;

    protected $table = 'work_costs';

    protected $fillable = [
        'classification',
        'costs',
        'change_history',
    ];

    protected $casts = [
        'costs' => 'array',
        'change_history' => 'array',
        'classification' => 'integer',
    ];
}

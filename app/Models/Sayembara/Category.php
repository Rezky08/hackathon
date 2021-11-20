<?php

namespace App\Models\Sayembara;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $table = 'sayembara_categories';
    public $hidden = [
        'is_active',
        'updated_at',
        'created_at',
    ];
}

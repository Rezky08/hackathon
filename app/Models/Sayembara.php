<?php

namespace App\Models;

use App\Casts\LimitCast;
use App\Models\Sayembara\Detail;
use App\Traits\ColumnListing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Detail $detail;
 */
class Sayembara extends Model
{
    use HasFactory,ColumnListing;
    public $fillable = [
        'user_id',
        'is_open',
        'start_date',
        'end_date',
    ];

    public function detail(){
        return $this->hasOne(Detail::class,'sayembara_id','id');
    }

}

<?php

namespace App\Models;

use App\Casts\LimitCast;
use App\Models\Sayembara\Detail;
use App\Traits\ColumnListing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

/**
 * @property Detail $detail;
 * @property boolean $is_open;
 * @property Date $start_date;
 * @property Date $end_date;
 *
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

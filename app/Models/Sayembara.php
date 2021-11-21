<?php

namespace App\Models;

use App\Casts\LimitCast;
use App\Models\Sayembara\Detail;
use App\Models\Sayembara\Participant;
use App\Models\Sayembara\Winner;
use App\Traits\ColumnListing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

/**
 * @property int $id
 * @property User $user
 * @property Detail $detail
 * @property boolean $is_open
 * @property Date $start_date
 * @property Date $end_date
 * @property Participant $participants
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
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function participants(){
        return $this->hasMany(Participant::class,'sayembara_id','id');
    }

    public function winners(){
        return $this->hasManyThrough(Winner::class,Participant::class,'sayembara_id','sayembara_participant_id','id','id');
    }

    public function attachments(){
        return $this->morphMany(Attachment::class,'attachable');
    }

}

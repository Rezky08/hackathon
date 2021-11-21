<?php

namespace App\Models\Sayembara;

use App\Models\Sayembara;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property User $user
 * @property Sayembara $sayembara
 * @property boolean $task_is_verified
 */
class Participant extends Model
{
    use HasFactory;

    public $table = 'sayembara_participants';

    public $fillable = [
        'user_id',
        'sayembara_id'
    ];
    public $hidden = [
        'user_id',
        'sayembara_id',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sayembara(){
        return $this->belongsTo(Sayembara::class,'sayembara_id','id');
    }
    public function winners(){
        return $this->hasMany(Winner::class,'sayembara_participant_id','id');
    }

}

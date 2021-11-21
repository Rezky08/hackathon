<?php

namespace App\Models\Sayembara;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Participant $participant
 * @property bool $present_is_received;
 */
class Winner extends Model
{
    use HasFactory;

    public $table = 'sayembara_winners';

    public function participant(){
        $this->belongsTo(Participant::class,'sayembara_participant_id','id');
    }
}

<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';
    use HasFactory;

    static public function getAvailableGender(){
        return [
            self::GENDER_MALE,
            self::GENDER_FEMALE
        ];
    }
}

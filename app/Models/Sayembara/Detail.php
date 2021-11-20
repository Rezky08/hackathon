<?php

namespace App\Models\Sayembara;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;
    const PRESENT_TYPE_MONEY = 'money';
    const PRESENT_TYPE_ITEM = 'item';
    const PRESENT_TYPE_VOUCHER = 'voucher';
    const PRESENT_TYPE_OTHER = 'other';

    static public function getAvaliablePresentType(){
        return [
          self::PRESENT_TYPE_ITEM,
          self::PRESENT_TYPE_MONEY,
          self::PRESENT_TYPE_VOUCHER,
          self::PRESENT_TYPE_OTHER,
        ];
    }
}

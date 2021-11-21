<?php

namespace App\Models\Sayembara;

use App\Casts\LimitCast;
use App\Models\Attachment;
use App\Models\Sayembara;
use App\Traits\ColumnListing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Sayembara $sayembara
 * @property string $title
 */
class Detail extends Model
{
    use HasFactory,ColumnListing;

    public $table = 'sayembara_details';

    const PRESENT_TYPE_MONEY = 'money';
    const PRESENT_TYPE_ITEM = 'item';
    const PRESENT_TYPE_VOUCHER = 'voucher';
    const PRESENT_TYPE_OTHER = 'other';

    const LIMIT_AGE = 'age';// count from birth
    const LIMIT_GEO = 'geo';// array
    const LIMIT_GENDER = 'gender';// string

    public $fillable = [
        'sayembara_id',
        'province_id',
        'city_id',
        'district_id',
        'sub_district_id',
        'thumbnail',
        'title',
        'present_type',
        'present_value',
        'category',
        'max_participant',
        'max_winner',
        'content',
        'limit'
    ];

    public $hidden = [
        'id',
        'sayembara_id',
        'created_at',
        'updated_at'
    ];

    public $casts = [
        'limit'=> LimitCast::class
    ];


    static public function getAvaliablePresentType(){
        return [
          self::PRESENT_TYPE_ITEM,
          self::PRESENT_TYPE_MONEY,
          self::PRESENT_TYPE_VOUCHER,
          self::PRESENT_TYPE_OTHER,
        ];
    }

    static public function getAvailableLimit(){
        return [
            self::LIMIT_AGE,
            self::LIMIT_GEO,
            self::LIMIT_GENDER
        ];
    }

    public function sayembara(){
        $this->belongsTo(Sayembara::class,'sayembara_id','id');
    }

    public function thumbnail(){
        return $this->morphOne(Attachment::class,'attachable');
    }
}

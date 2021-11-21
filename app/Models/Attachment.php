<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 */
class Attachment extends Model
{
    use HasFactory;

    public $fillable = [
        'type',
        'path',
        'size'
    ];

    static public function generateFileName(Attachment $attachment,UploadedFile $file){

        return Carbon::today()->format("Y-m-d")."-attachment-".$attachment->id.'.'.$file->getClientOriginalExtension();
    }

    /**
     * Get the parent attachable model (post or video).
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Jobs\Attachment;

use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

class CreateNewAttachment
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const DEFAULT_DISK = 'public';
    const SIZE_KB ="KB";
    const SIZE_MB ="MB";
    const SIZE_GB ="GB";

    /** @var User  */
    public User $user;

    public array $files;

    public array $attachments;

    public string $disk;

    public string $name;

    public Attachment $attachment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($files=null,$disk = self::DEFAULT_DISK,$name = null )
    {
        $files = Arr::wrap($files);
        /** @var User $user */
        $user = Auth::user();
        $this->user = $user;
        $rules = [
          '*' => ['file','max:1024']
        ];
        $this->files = Validator::make($files,$rules)->validate();
        $this->disk = $disk;
        $this->attachments = [];
    }

    function convert($size,$unit)
    {
        if($unit == self::SIZE_KB)
        {
            return $fileSize = round($size / 1024,4) ;
        }
        if($unit == self::SIZE_MB)
        {
            return $fileSize = round($size / 1024 / 1024,4) ;
        }
        if($unit == self::SIZE_GB)
        {
            return $fileSize = round($size / 1024 / 1024 / 1024,4) ;
        }
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var UploadedFile $file */
        foreach ($this->files as $file){
            DB::beginTransaction();
            try {

                $filePath = 'storage/';
                $dataInsert = [
                    'type' => $file->getClientMimeType(),
                    'size'=>$file->getSize(),
                    'path' => Storage::path($this->disk)
                ];
                /** @var Attachment $attachment */
                $attachment = $this->user->attachments()->create($dataInsert);
                $this->attachment = $attachment;

                $this->name = Attachment::generateFileName($this->attachment,$file);

                $file->storeAs($this->disk,$this->name);

                $this->attachment->path = $filePath.$this->name;
                $this->attachment->save();

                $this->attachments[]=($this->attachment);
                DB::commit();

            }catch (\Exception $e){
                DB::rollBack();
                throw Error::make(Response::CODE_ERROR_INVALID_FILE,['message'=>$e->getMessage()]);
            }
        }
    }
}

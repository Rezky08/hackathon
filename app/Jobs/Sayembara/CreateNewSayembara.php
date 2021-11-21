<?php

namespace App\Jobs\Sayembara;

use App\Events\Sayembara\NewSayembaraCreated;
use App\Exceptions\Error;
use App\Http\Response;
use App\Jobs\Attachment\CreateNewAttachment;
use App\Jobs\Sayembara\Detail\CreateNewDetailSayembara;
use App\Models\Attachment;
use App\Models\Sayembara;
use App\Models\Sayembara\Detail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateNewSayembara
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    public array $attributes;

    /**
     * @var array
     */
    public array $sayembaraDetailAttributes;

    /**
     * @var CreateNewDetailSayembara
     */
    public CreateNewDetailSayembara $sayembaraDetailJob;

    /** @var Sayembara  */
    public Sayembara $sayembara;

    /** @var Detail  */
    public Detail $sayembaraDetail;


    /** @var User|\Illuminate\Contracts\Auth\Authenticatable|null  */
    public Authenticatable $user;

    public array $attachments;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->user = Auth::user();
        $this->sayembara = new Sayembara();
        $rules = [
            'start_date'=>['required','date'],
            'end_date'=>['required','date'],
            'is_open'=>['nullable','bool'],
        ];
        $this->attributes = Validator::make($attributes,$rules)->validate();
        $this->sayembaraDetailJob = new CreateNewDetailSayembara($this->sayembara,$attributes);

        $this->attributes['user_id'] = $this->user->id;
        $this->attachments = Arr::wrap($attributes['attachments']??[]);

    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {

        $availableColumns = Sayembara::getTableColumns();
        $dataInsertSayembara = collect($this->attributes)->only($availableColumns);

        /** database multiple transaction */
        DB::beginTransaction();
        try {

            $this->sayembara->fill($dataInsertSayembara->toArray());
            $this->sayembara->save();

            /** upload attachment */
            $attachmentJob = new CreateNewAttachment($this->attachments);
            dispatch($attachmentJob);
            $this->attachments = $attachmentJob->attachments;
            $this->sayembara->attachments()->saveMany($this->attachments);
            /** end upload attachment */

            throw_if(!$this->sayembara->exists,Error::make(Response::CODE_ERROR_DATABASE_TRANSACTION));

            $this->sayembaraDetailJob->sayembara = $this->sayembara;

            dispatch($this->sayembaraDetailJob);

            $this->sayembaraDetail = $this->sayembaraDetailJob->sayembaraDetail;

            event(new NewSayembaraCreated($this->sayembara));

            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
            throw Error::make(Response::CODE_ERROR_DATABASE_TRANSACTION,['message'=>$e->getMessage()]);
        }

        return $this->sayembara->exists;
    }
}

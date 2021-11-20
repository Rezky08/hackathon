<?php

namespace App\Jobs\Sayembara;

use App\Events\Sayembara\NewSayembaraCreated;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use App\Models\Sayembara\Detail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    /** @var Sayembara  */
    public Sayembara $sayembara;

    /** @var Detail  */
    public Detail $sayembaraDetail;

    /** @var User|\Illuminate\Contracts\Auth\Authenticatable|null  */
    public Authenticatable $user;


    const ATTRIBUTE_MAP = [
        'province'=>'province_id',
        'city'=>'city_id',
        'district'=>'district_id',
        'sub_district'=>'sub_district_id',
        ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->user = Auth::user();
        $this->sayembara = new Sayembara();
        $this->sayembaraDetail = new Detail();
        $rules = [
            'start_date'=>['required','date'],
            'end_date'=>['required','date'],
            'province'=>['required','filled','exists:provinces,id'],
            'city'=>['required','filled','exists:cities,id'],
            'district'=>['nullable','exists:districts,id'],
            'sub_district'=>['nullable','exists:sub_districts,id'],
            'thumbnail'=>['nullable','exists:attachments,id'],
            'title'=>['required','filled'],
            'present_type'=>['required','filled',Rule::in(Detail::getAvaliablePresentType())],
            'present_value' =>['required','filled'/*Todo: add validation when present_type is money*/],
            'category'=>['required','filled','exists:sayembara_categories,name'],
            'max_participant' => ['required','filled','numeric'],
            'max_winner' => ['required','filled','numeric'],
            'content' => ['required','filled'],
            'limit' => ['nullable','array'],
            'limit.'.Detail::LIMIT_AGE  =>['filled','array'],
            'limit.'.Detail::LIMIT_AGE.'.min'  =>['filled','numeric'],
            'limit.'.Detail::LIMIT_AGE.'.max'  =>['filled','numeric'],
            'limit.'.Detail::LIMIT_GEO  =>['filled','array'],
            'limit.'.Detail::LIMIT_GEO.'.*'  =>['filled','array','min:1'],
            'limit'.Detail::LIMIT_GENDER    =>['filled','array','min:1']
        ];
        $this->attributes = Validator::make($attributes,$rules)->validate();

        $this->attributes['is_open'] = true;
        $this->attributes['user_id'] = $this->user->id;

    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {

        /** value mapping */
        foreach ($this->attributes as $key => $value){
            if (array_key_exists($key,self::ATTRIBUTE_MAP)){
                $this->attributes[self::ATTRIBUTE_MAP[$key]] = $value;
                unset($this->attributes[$key]);
            }
        }
        /** end value mapping */

        $availableColumns = Sayembara::getTableColumns();
        $dataInsertSayembara = collect($this->attributes)->only($availableColumns);

        /** database multiple transaction */
        DB::beginTransaction();
        try {
            $this->sayembara->fill($dataInsertSayembara->toArray());
            $this->sayembara->save();

            throw_if(!$this->sayembara->exists,new Error(Response::CODE_ERROR_DATABASE_TRANSACTION));

            $availableColumns = Detail::getTableColumns();
            $dataInsertSayembaraDetail = collect($this->attributes)->only($availableColumns);
            $this->sayembara->detail()->create($dataInsertSayembaraDetail->toArray());

            throw_if(!$this->sayembara->detail->exists,new Error(Response::CODE_ERROR_DATABASE_TRANSACTION));


            event(new NewSayembaraCreated($this->sayembara));

            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
            throw new Error(Response::CODE_ERROR_DATABASE_TRANSACTION,[],$e);
        }

        return $this->sayembara->exists;
    }
}

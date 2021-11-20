<?php

namespace App\Jobs\Sayembara\Detail;

use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use App\Models\Sayembara\Detail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateNewDetailSayembara
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const ATTRIBUTE_MAP = [
        'province'=>'province_id',
        'city'=>'city_id',
        'district'=>'district_id',
        'sub_district'=>'sub_district_id',
    ];

    public array $rules;

    public Sayembara $sayembara;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sayembara $sayembara,$attributes = [])
    {
        $this->sayembara = $sayembara;
        $this->rules = [
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
        $this->attributes = Validator::make($attributes,$this->rules)->validate();
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
            if (array_key_exists($key,CreateNewDetailSayembara::ATTRIBUTE_MAP)){
                $this->attributes[CreateNewDetailSayembara::ATTRIBUTE_MAP[$key]] = $value;
                unset($this->attributes[$key]);
            }
        }
        /** end value mapping */


        $availableColumns = Detail::getTableColumns();
        $dataInsertSayembaraDetail = collect($this->attributes)->only($availableColumns);
        $this->sayembara->detail()->create($dataInsertSayembaraDetail->toArray());

        throw_if(!$this->sayembara->detail->exists,Error::make(Response::CODE_ERROR_DATABASE_TRANSACTION));

        return $this->sayembara->detail->exists;

    }
}

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

class UpdateDetailExistingSayembara implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Sayembara $sayembara;

    protected Sayembara\Detail $sayembaraDetail;

    protected array $attributes;

    /**
     * Create a new job instance.
     *
     * @param Sayembara $sayembara
     * @param array $attributes
     */
    public function __construct(Sayembara $sayembara,array $attributes=[])
    {
        $this->sayembara = $sayembara;
        $this->sayembaraDetail = $sayembara->detail;

        $rules = [
            'province'=>['filled','exists:provinces,id'],
            'city'=>['filled','exists:cities,id'],
            'district'=>['nullable','exists:districts,id'],
            'sub_district'=>['nullable','exists:sub_districts,id'],
            'thumbnail'=>['nullable','exists:attachments,id'],
            'title'=>['filled'],
            'present_type'=>['filled',Rule::in(Detail::getAvaliablePresentType())],
            'present_value' =>['filled'/*Todo: add validation when present_type is money*/],
            'category'=>['filled','exists:sayembara_categories,name'],
            'max_participant' => ['filled','numeric'],
            'max_winner' => ['filled','numeric'],
            'content' => ['filled'],
            'limit' => ['nullable','array'],
            'limit.'.Detail::LIMIT_AGE  =>['filled','array'],
            'limit.'.Detail::LIMIT_AGE.'.min'  =>['filled','numeric'],
            'limit.'.Detail::LIMIT_AGE.'.max'  =>['filled','numeric'],
            'limit.'.Detail::LIMIT_GEO  =>['filled','array'],
            'limit.'.Detail::LIMIT_GEO.'.*'  =>['filled','array','min:1'],
            'limit'.Detail::LIMIT_GENDER    =>['filled','array','min:1']
        ];

        $this->attributes = Validator::make($attributes,$rules)->validate();
    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {
        foreach ($this->attributes as $key => $value){
            $this->sayembaraDetail->{$key}= $value;
        }

        $this->sayembaraDetail->save();

        throw_if(!$this->sayembaraDetail->exists,new Error(Response::CODE_ERROR_DATABASE_TRANSACTION));

        return $this->sayembaraDetail->exists;
    }
}

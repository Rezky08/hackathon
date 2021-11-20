<?php

namespace App\Jobs\Sayembara;

use App\Models\Sayembara;
use App\Models\Sayembara\Detail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateExistingSayembara extends CreateNewSayembara
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $attributes=[])
    {
        parent::__construct();
        $rules = [
            'start_date'=>['filled','date'],
            'end_date'=>['filled','date'],
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
     * @return void
     */
    public function handle()
    {
        dd($this->attributes);
    }
}

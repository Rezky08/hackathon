<?php

namespace App\Http\Resources;

use App\Models\Sayembara;
use Illuminate\Http\Resources\Json\JsonResource;

class SayembaraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Sayembara $data */
        $data = $this->resource;
        if (!$data->relationLoaded('detail')){
            $data->load('detail');
        }
        $data = $data->toArray();
        $data = array_merge($data,$data['detail']);
        unset($data['detail']);
        return $data;
    }
}

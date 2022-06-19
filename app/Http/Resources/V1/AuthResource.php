<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //  return parent::toArray($request);

        return [
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'cellphone' => $this->cellphone,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}

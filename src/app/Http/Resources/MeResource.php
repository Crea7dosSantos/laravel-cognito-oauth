<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

final class MeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'cognito_sub' => $this->resource->cognito_sub,
            'cognito_google_sub' => $this->resource->cognito_google_sub,
            'cognito_apple_sub' => $this->resource->cognito_apple_sub,
            'expired_at' => $this->resource->expired_at
        ];
    }
}

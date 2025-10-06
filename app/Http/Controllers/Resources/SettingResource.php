<?php

namespace App\Http\Controllers\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "fields" => $this->fields,
            "banner" => url('/storage/'.$this->banner),
            "favicon" => url('/storage/'.$this->favicon),
            "form_builder" => json_decode($this->form_builder, true) ?? [],

            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}

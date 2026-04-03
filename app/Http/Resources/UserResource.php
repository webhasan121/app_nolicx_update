<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'reference' => $this->reference,
            'wallet' => $this->coin,
            'profile_photo' => $this->profile_photo_path,
            'default_profile_photo' => $this->profile_photo_url,

            'country' => $this->country,
            'country_code' => $this->ocuntry_code,

            'city' => $this->city,
            'state' => $this->state,
            'number' => $this->phone,
            'zip' => $this->zip,
            'line1' => $this->line1,
            'line2' => $this->line2,

            'language' => $this->language,
            'site_language' => $this->site_language,

            'currency' => $this->currency,
            'currency_sign' => $this->currency_sign,

            'about' => $this->bio,

            'gender' => $this->gender,
            'my_ref' => $this->whenLoaded('myRef'),
            'my_order' => $this->whenLoaded('myOrderAsUser'),

        ];
    }
}

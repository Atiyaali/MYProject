<?php

namespace App\Http\Resources;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OnsiteParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'salutation' => $this->salutation,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'personal_email' => $this->personal_email,
            'designation' => $this->designation,
            'organisation' => $this->organisation,
            'dietary_preference' => $this->dietary_preference,
            'about_event' => $this->about_event,
            'about_event_other' => $this->about_event_other,
            'phone' => $this->phone,
            'terms_condition' => $this->terms_condition,
            'payment_status' => $this->payment_status,
            'stripe_url' => $this->stripe_url,
            'stripe_session' => $this->stripe_session,
            'txn_id' => $this->txn_id,
            'reg_key' => $this->reg_key,
            'auth_key' => $this->auth_key,
            'confirmed' => $this->confirmed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'meta' => $this->generateMeta(),
        ];
    }

    /**
     * Generate meta data with all keys from onsiteColumns.
     *
     * @return array<string, mixed>
     */
    private function generateMeta(): array
    {
        return collect(Participant::$onsiteColumns)
            ->mapWithKeys(fn($column) => [$column => $this->$column ?? null])
            ->toArray();
    }
}

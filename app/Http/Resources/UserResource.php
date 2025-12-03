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
    public function toArray($request)
    {
        // Kita return array kustom, bukan semua data
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'nim' => $this->nim, // Nullable
            'angkatan' => $this->angkatan,

            // Include Profile jika user adalah alumni & profilenya ada
            'alumni_profile' => $this->when($this->role === 'alumni' && $this->alumniProfile, function () {
                return [
                    'phone' => $this->alumniProfile->phone,
                    'linkedin_url' => $this->alumniProfile->linkedin_url,
                    'privacy' => $this->alumniProfile->privacy_settings,
                ];
            }),
        ];
    }
}

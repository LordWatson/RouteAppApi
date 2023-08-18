<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $returnArray = [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ];

        if(Auth::user()->hasRole('admin')){
            $returnArray['created_at'] = $this->created_at;
            $returnArray['updated_at'] = $this->updated_at;
        }

        return $returnArray;
    }
}

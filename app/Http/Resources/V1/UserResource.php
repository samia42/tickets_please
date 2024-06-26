<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request) : array
    {
        return [
            'type' => 'author',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'isManager' => $this->is_manager,
                $this->mergeWhen($request->routeIs('authors.*'), [
                    'emailVarifiedAt' => $this->email_verified_at,
                    'createdAt' => $this->created_at,
                    'updatedAt' => $this->updated_at,

                ]),
            ],
            'links' => [
                'self' => route('authors.show', ['author' => $this->id]),
            ],
            'includes' => TicketResource::collection($this->whenLoaded('tickets')),

        ];
    }
}

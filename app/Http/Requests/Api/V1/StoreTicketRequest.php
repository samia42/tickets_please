<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() : array
    {
        $user = $this->user();
        $authorIdAtr = $this->routeIs('tickets.store') ? 'data.relationships.author.data.id' : 'author';
        $authorRule = 'required|integer|exists:users,id';

        $rules = [
            "data.attributes.title" => "required|string",
            "data.attributes.description" => "required|string",
            "data.attributes.status" => "required|string|in:A,X,C,H",
            $authorIdAtr => $authorRule . "|size" . $user->id,
        ];

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$authorIdAtr] .= $authorRule;
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')
            ]);
        }
    }
}

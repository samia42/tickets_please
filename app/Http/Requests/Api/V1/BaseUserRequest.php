<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributeMap = array_merge([
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.isManager' => 'is_manager',
            'data.attributes.password' => 'password',
            'data.relationships.author.data.id' => 'user_id',
        ], $otherAttributes);
        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $value = $this->input($key);
                if ($attribute == 'password') {
                    $value = bcrypt($value);
                }
                $attributesToUpdate[$attribute] = $value;
            }
        }
        return $attributesToUpdate;
    }
}

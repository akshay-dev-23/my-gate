<?php

namespace App\Http\Requests\Api;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'content' => 'required',
            'admin_notice' => 'sometimes|boolean',
            'media'=>'sometimes|array',
            'admin_notice'=>'required|boolean'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
    }
}

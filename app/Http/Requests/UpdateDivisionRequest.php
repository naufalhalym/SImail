<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDivisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'division' => __('model.division.division'),
            'description' => __('model.division.description'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'division' => ['required', Rule::unique('divisions')->ignore($this->id)],
            'description'=> ['nullable'],
        ];
    }
}

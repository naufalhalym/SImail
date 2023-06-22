<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // return auth()->user()->role == Role::ADMIN->status() || $this->id == auth()->user()->id;
        return true;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => __('model.user.name'),
            'email' => __('model.user.email'),
            'phone' => __('model.user.phone'),
            'position' => __('model.user.position'),
            'division' => __('model.user.division'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', Rule::unique('users')->ignore($this->id)],
            'phone' => ['nullable'],
            'is_active' => ['nullable'],
            'position' => ['required'],
            'division' => ['required'],
        ];
    }
}

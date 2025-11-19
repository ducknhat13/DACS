<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ProfileUpdateRequest - Form Request validation cho profile update
 * 
 * Request này validate dữ liệu khi user cập nhật profile:
 * - name: Bắt buộc, string, max 255 chars
 * - email: Bắt buộc, email format, lowercase, unique (trừ current user)
 * 
 * Email validation:
 * - Rule::unique()->ignore(): Email phải unique nhưng ignore email hiện tại của user
 * - lowercase: Tự động convert về lowercase
 * 
 * Được dùng trong ProfileController@update
 * 
 * @author QuickPoll Team
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase', // Tự động convert về lowercase
                'email',
                'max:255',
                // Email phải unique nhưng ignore email hiện tại của user (cho phép giữ nguyên email)
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}

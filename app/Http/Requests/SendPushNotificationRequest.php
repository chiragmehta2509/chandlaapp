<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendPushNotificationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|url|max:2048',
            'action_type' => 'required|string|in:none,feature,offer,url,screen',
            'action_value' => 'nullable|string|max:1000',
            'send_to' => 'required|string|in:all,selected_users',
            'user_ids' => 'required_if:send_to,selected_users|array',
            'user_ids.*' => 'integer|exists:users,id',
            'schedule_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_ids.required_if' => 'The user_ids field is required when sending to selected users.',
            'user_ids.*.exists' => 'One or more of the selected users do not exist.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422));
    }
}

<?php

namespace App\Http\Requests\Api\RefreshToken;

use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Log;

final class InvokeRequest extends ApiRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        return [
            'client_id' => 'required|exists:oauth_clients,id',
            'access_token' => 'required',
            'refresh_token' => 'required',
        ];
    }

    /**
     * custom validation messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'client_id.required' => '指定されたパラメータに不正があります',
            'client_id.exists' => '指定されたパラメータに不正があります',
            'access_token.required' => '指定されたパラメータに不正があります',
            'refresh_token.required' => '指定されたパラメータに不正があります'
        ];
    }
}

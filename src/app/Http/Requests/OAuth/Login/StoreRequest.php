<?php

namespace App\Http\Requests\OAuth\Login;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|between:8,255',
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
            'email.required' => 'メールアドレスを指定してください',
            'email.email' => '入力されたメールアドレスのフォーマットが正しくありません',
            'email.exists' => '入力されたメールアドレスは存在しません',
            'password.required' => 'パスワードを指定してください',
            'password.string' => '入力されたパスワードが文字列ではありません',
            'password.between' => 'パスワードは8文字以上255文字以下で入力してください'
        ];
    }
}

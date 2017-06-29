<?php

namespace Terranet\Administrator\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        $config = app('scaffold.config');

        $identity = $config->get('auth.identity', 'username');
        $credential = $config->get('auth.credential', 'password');

        return [
            $identity => 'required',
            $credential => 'required',
        ];
    }

    protected function getRedirectUrl()
    {
        return route('scaffold.login');
    }
}

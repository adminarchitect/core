<?php

namespace Terranet\Administrator\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Terranet\Administrator\Contracts\Module\Validable;

class UpdateRequest extends FormRequest
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
        $rules = [];

        if (($module = app('scaffold.module')) && $module instanceof Validable) {
            $rules = $module->rules();

            if (\is_object($key = \Route::input('id')) && method_exists($key, 'getKey')) {
                $key = $key->getKey();
            }

            if (\is_callable($rules)) {
                return $rules($key);
            }

            $rules = array_map(function ($rule) use ($key) {
                if (\is_callable($rule)) {
                    $rule = $rule($key);
                }

                return $rule;
            }, $rules);
        }

        return $rules;
    }

    public function messages()
    {
        if (($module = app('scaffold.module'))
            && $module instanceof Validable
            && method_exists($module, 'messages')) {
            return $module->messages();
        }

        return parent::messages();
    }
}

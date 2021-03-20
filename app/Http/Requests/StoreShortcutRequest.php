<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreShortcutRequest extends FormRequest
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

    public function reservedKeywords()
    {
        return Rule::notIn(config('shortcuts.reserved_keywords'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url' => ['required', 'string', 'max:255', 'url'],
            'shortcut' => ['string', 'alpha_num', 'max:6', 'unique:shortcuts', $this->reservedKeywords()],
        ];
    }
}

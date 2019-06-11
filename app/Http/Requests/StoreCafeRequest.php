<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCafeRequest extends FormRequest
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
        return [
            'name'                          => 'required',
            'locations.*.address'           => 'required',
            'locations.*.city'              => 'required',
            'locations.*.state'             => 'required',
            'locations.*.zip'               => 'required|regex:/\b\d{5}\b/',
            'locations.*.methodsAvailable'  => 'sometimes|array',    //validation for brew methods
            'website'                       => 'required|url'    //VALID: http://www.example.com
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'                 => 'A name for the cafe is required.',
            'locations.*.address.required'  => 'An address is required to add this cafe.',
            'locations.*.city.required'     => 'A city is required to add this cafe.',
            'locations.*.state.required'    => 'A state is required to add this cafe.',
            'locations.*.zip.required'      => 'A zip code is required to add this cafe.',
            'locations.*.zip.regex'         => 'The zip code entered is invalid.'
        ];
    }
}

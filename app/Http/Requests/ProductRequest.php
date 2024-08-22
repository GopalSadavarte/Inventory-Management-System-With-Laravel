<?php

namespace App\Http\Requests;

use App\Rules\discountValidate;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'p_name' => 'required',
            'group' => 'required',
            'sub_group' => 'required',
            'weight' => 'required|alpha_num',
            'p_rate' => 'required|regex:/^[0-9\.]+$/',
            'p_mrp' => 'required|regex:/^[0-9\.]+$/',
            'discount' => ['regex:/^[0-9\.]*$/', new discountValidate()],
        ];
    }

    public function messages(): array
    {
        return [
            'p_name.required' => 'Product Name Field must be required..!',
            'group.required' => 'Please,Select specific group..!',
            'sub_group.required' => 'Please,Select specific sub group..!',
            'weight.required' => 'Product weight are required..!',
            'weight.alpha_num' => 'Product weight only allowed characters and numbers..!',
            'p_rate.required' => 'Product Sale rate are required..!',
            'p_mrp.required' => 'Product MRP are required..!',
            'p_rate.regex' => 'Product sale rate must includes only numbers..!',
            'p_mrp.regex' => 'Product MRP must includes only numbers..!',
            'discount.regex' => 'Discount only includes numbers..!',
        ];
    }
    //protected $stopOnFirstFailure = true;
}

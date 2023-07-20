<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreCnfusersRequest extends FormRequest{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [
            'first_name' => 'required',
            'aadhar_number' => 'required|unique:reseller_cnf|max:12',
            'pan_number' => 'required|unique:reseller_cnf|max:10',
            'mobile_number' => 'required|unique:reseller_cnf|max:10',
            //'email' => 'required|email:rfc,dns|unique:users,email',
        ];
    }
}
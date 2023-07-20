<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest{
    public function authorize(){
        return true;
    }

    public function rules(){
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'name' => 'required',
            'mobile_number' => 'required',
            'telephone_number' => 'nullable',
            'email' => 'required|email:rfc,dns|unique:super_cnf,email,',
            'username' => 'required|unique:super_cnf,username,',
            'app_id' => 'nullable',
            'gst_number' => 'nullable',
            'date_of_birth' => 'nullable',
            'gender' => 'nullable',
            'education' =>'nullable',
            'aadhar_number' => 'nullable',
            'pan_number' => 'nullable',
            'alternate_mobile_number' => 'nullable',
            'bank_name' => 'nullable',
            'account_type' => 'nullable',
            'account_holder_name' => 'nullable',
            'account_number' => 'nullable',
            'ifsc_code' => 'nullable',
            'logo' => 'nullable',
            'company_name' => 'nullable',
            'brand_name' => 'nullable',
            'display_name' => 'nullable',
            'url' => 'nullable',
            'mid_secret_key' => 'nullable',
            'mid' => 'nullable',
            'status' => 'required'
        ];
    }
}
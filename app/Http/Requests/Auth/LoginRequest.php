<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
   public function authorize(): bool
   {
       return true;
   }

   public function rules(): array
   {
       return [
           'login'    => ['required','string','max:255'],
           'password' => ['required','string','max:255'],
           'remember' => ['sometimes','boolean'],
       ];
   }

   public function messages(): array
   {
       return [
           'login.required'    => 'Please enter your email or username.',
           'password.required' => 'Please enter your password.',
       ];
   }
}

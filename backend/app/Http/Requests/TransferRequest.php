<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_email.required' => 'O email do destinatário é obrigatório.',
            'recipient_email.email' => 'O email do destinatário deve ser um endereço de email válido.',
            'recipient_email.exists' => 'O destinatário com este email não existe.',
            'amount.required' => 'Valor é obrigatório.',
            'amount.numeric' => 'Valor deve ser um número.',
            'amount.min' => 'O valor deve ser maior que zero.',
        ];
    }
}

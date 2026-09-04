<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isSecurityTransaction = in_array(
            $this->input('type'),
            [
                TransactionType::Buy->value,
                TransactionType::Sell->value,
            ],
            true
        );

        return [
            'type' => [
                'required',
                Rule::enum(TransactionType::class),
            ],

            'amount' => [
                Rule::requiredIf(!$isSecurityTransaction),
                Rule::prohibitedIf($isSecurityTransaction),
                'numeric',
                'gt:0',
            ],

            'ticker' => [
                Rule::requiredIf($isSecurityTransaction),
                Rule::prohibitedIf(!$isSecurityTransaction),
                'string',
                'max:20',
            ],

            'quantity' => [
                Rule::requiredIf($isSecurityTransaction),
                Rule::prohibitedIf(!$isSecurityTransaction),
                'integer',
                'min:1',
            ],

            'price' => [
                Rule::requiredIf($isSecurityTransaction),
                Rule::prohibitedIf(!$isSecurityTransaction),
                'numeric',
                'gt:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Transaction type is required.',
            'type.enum' => 'Transaction type must be deposit, withdrawal, buy, or sell.',

            'amount.required' => 'Amount is required for deposit and withdrawal transactions.',
            'amount.prohibited' => 'Amount must not be provided for buy or sell transactions.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.gt' => 'Amount must be greater than zero.',

            'ticker.required' => 'Ticker is required for buy and sell transactions.',
            'ticker.prohibited' => 'Ticker is only allowed for buy and sell transactions.',
            'ticker.string' => 'Ticker must be a valid string.',
            'ticker.max' => 'Ticker may not be longer than 20 characters.',

            'quantity.required' => 'Quantity is required for buy and sell transactions.',
            'quantity.prohibited' => 'Quantity is only allowed for buy and sell transactions.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',

            'price.required' => 'Price is required for buy and sell transactions.',
            'price.prohibited' => 'Price is only allowed for buy and sell transactions.',
            'price.numeric' => 'Price must be a valid number.',
            'price.gt' => 'Price must be greater than zero.',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Http\Request;

class KimariteGetStats extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'types' => ['required', 'array'],
            'types.*.name' => ['required', 'string'],
            'divisions' => ['required', 'array'],
            'divisions.*.name' => ['required', 'string'],
            'from' => ['required', 'string'],
            'to' => ['required', 'string'],
        ];
    }
}

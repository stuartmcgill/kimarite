<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class ShowKimariteRequest extends FormRequest
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
            'selected_types' => ['nullable', 'array'],
            'selected_types.*' => ['string'],
            'selected_divisions' => ['nullable', 'array'],
            'selected_divisions.*' => ['string'],
            'from' => ['nullable', 'string'],
            'to' => ['nullable', 'string'],
            'display_as_percent' => ['nullable', 'boolean'],
        ];
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'selected_types' => collect($this->input('selected_types', []))
                ->map(fn ($item) => ucfirst(strtolower($item)))
                ->toArray(),

            'selected_divisions' => collect($this->input('selected_divisions', []))
                ->map(fn ($item) => ucfirst(strtolower($item)))
                ->toArray(),
        ]);
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'status' => 'sometimes|in:draft,published',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Le statut doit être draft ou published.',
            'tags.*.exists' => 'Un ou plusieurs tags sont invalides.',
        ];
    }
}

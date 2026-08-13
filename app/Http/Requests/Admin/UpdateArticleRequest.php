<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $article = $this->route('article');

        return $article instanceof Article && ($this->user()?->can('update', $article) ?? false);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $article = $this->route('article');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash:ascii', Rule::unique(Article::class)->ignore($article)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_alt' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:50000'],
            'rating' => ['required', 'numeric', 'between:0,10', 'decimal:0,1'],
            'category' => ['required', 'string', 'max:100'],
            'tags' => ['nullable', 'string', 'max:500'],
            'affiliate_url' => ['nullable', 'url:http,https', 'max:2048'],
        ];
    }
}

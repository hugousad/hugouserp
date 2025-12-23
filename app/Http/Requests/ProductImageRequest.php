<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('products.image.upload') ?? false;
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'file',
                'max:5120', // 5 MB limit to prevent oversized uploads
                'mimes:jpg,jpeg,png,gif,webp',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ];
    }
}

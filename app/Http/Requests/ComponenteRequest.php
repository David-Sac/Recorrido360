<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComponenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'       => ['required','string','max:255'],
            'descripcion'  => ['nullable','string'],
            'imagen_path'  => [
                $this->routeIs('componentes.store') ? 'required' : 'nullable',
                'image','max:2048'
            ],
        ];
    }
}

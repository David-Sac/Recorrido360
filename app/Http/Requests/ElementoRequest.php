<?php
// app/Http/Requests/ElementoRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ElementoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'componente_id' => ['nullable','exists:componentes,id'],
            'nombre'        => ['required','string','max:255'],
            'tipo'          => ['required', Rule::in(['datos','imagen','video','audio','otro'])],
            'descripcion'   => ['nullable','string','max:2000'],

            // Para 'datos'
            'contenido'     => ['nullable','string','max:20000'],

            // Para media: el archivo como tal
            'media'         => ['nullable','file','max:51200'], // 50MB (ajusta a gusto)
        ];
    }
}

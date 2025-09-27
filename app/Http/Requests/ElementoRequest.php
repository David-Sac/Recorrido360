<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ElementoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // NOTA: ajusta 'componentes' si tu tabla se llama diferente
        return [
            'componente_id' => ['required','exists:componentes,id'],
            'nombre'        => ['required','string','max:255'],

            // Tipos soportados
            'tipo'          => ['required', Rule::in(['datos','imagen','video','audio','otro'])],

            // Campos extra
            'titulo'        => ['nullable','string','max:255'],
            'descripcion'   => ['nullable','string'],
            'contenido'     => ['nullable','string'],

            // Fuente remota o local (al menos una si tipo necesita media)
            'url'           => ['nullable','url'],
            'media'         => ['nullable','file','max:204800'], // 200MB

            // Extras
            'meta'          => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'componente_id.required' => 'Debes elegir un componente.',
            'componente_id.exists'   => 'El componente seleccionado no existe.',
            'tipo.in'                => 'Tipo inválido.',
            'url.url'                => 'La URL no es válida.',
            'media.max'              => 'El archivo es demasiado grande (máx 200MB).',
        ];
    }
}

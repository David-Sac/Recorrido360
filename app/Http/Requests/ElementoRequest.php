<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ElementoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ya controlamos roles en las rutas
    }

    public function rules(): array
    {
        return [
            'componente_id' => ['required','exists:componentes,id'],
            'nombre'        => ['required','string','max:255'],
            'tipo'          => ['required','in:datos,video,imagen,audio,otro'],
            'contenido'     => ['nullable','string'], 
            // si quieres archivos: validar aquí con 'file' y 'mimes'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PanoramaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ya protegemos por roles en las rutas
    }

    public function rules()
    {
        return [
            'nombre'         => ['required','string','max:255'],
            'componente_id'  => ['required','exists:componentes,id'],
            'imagen_path'    => [$this->isMethod('POST') ? 'required' : 'nullable', 'image', 'max:20000'],
        ];
    }


}

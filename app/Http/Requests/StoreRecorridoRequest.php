<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecorridoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'titulo' => ['required','string','max:255'],
            'slug'   => ['nullable','string','max:255','unique:recorridos,slug'],
            'descripcion' => ['nullable','string'],
            'publicado'   => ['nullable','boolean'],
        ];
    }
}

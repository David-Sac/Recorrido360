<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecorridoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'titulo' => ['required','string','max:255'],
            'descripcion' => ['nullable','string'],
            'publicado'   => ['nullable','boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DokumenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "file" => "required",
            "jenis_dokumen" => "required",
            "id_peserta" => "required|uuid",
        ];
    }

    public function message(): array
    {
        return[
            "file.required" => "Dokumen harus diisi",
            "jenis_dokumen.required" => "Jenis dokumen harus diisi",
            "id_peserta.required" => "ID peserta harus diisi",
            "id_peserta.uuid" => "ID peserta harus dalam format UUID yang valid",
        ];
    }
}

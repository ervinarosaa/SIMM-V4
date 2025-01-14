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
            "id_jenis" => "required|exists:jenis_dokumen,id",
            "id_peserta" => "required|uuid",
        ];
    }

    public function message(): array
    {
        return[
            "file.required" => "Dokumen harus diisi",
            "id_jenis.required" => "Jenis dokumen harus diisi",
            "id_jenis.exists" => "Jenis dokumen tidak valid",
            "id_peserta.required" => "ID peserta harus diisi",
            "id_peserta.uuid" => "ID peserta harus dalam format UUID yang valid",
        ];
    }
}

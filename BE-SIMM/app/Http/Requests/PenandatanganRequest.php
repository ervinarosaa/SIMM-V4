<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenandatanganRequest extends FormRequest
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
            "nama_penandatangan" => "required|string",
            "nip_penandatangan" => "required|string",
            "jabatan_penandatangan" => "required|string",
        ];
    }

    public function message(): array
    {
        return[
            "nama_penandatangan.required" => "Nama Penandatangan harus diisi",
            "nip_penandatangan.required" => "NIP Penandatangan harus diisi",
            "jabatan_penandatangan.required" => "Jabatan Penandatangan harus diisi",
        ];
    }
}

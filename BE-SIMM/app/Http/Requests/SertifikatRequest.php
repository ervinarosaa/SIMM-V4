<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SertifikatRequest extends FormRequest
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
            "nomor_sertifikat" => "required|string",
            "id_nilai" => "required|uuid|exists:nilai,id",
            "id_peserta" => "required|uuid|exists:peserta,id",
            "id_penandatangan" => "required|uuid|exists:penandatangan,id",
            "tanggal_sertifikat" => "required|string",
        ];
    }

    public function message(): array
    {
        return[
            "nomor_sertifikat.required" => "Nomor sertifikat harus diisi",
            "nomor_sertifikat.max" => "Nomor sertifikat tidak boleh lebih dari 255 karakter",
            "id_nilai.required" => "Nilai harus diisi",
            "id_nilai.exists" => "Nilai tidak valid",
            "id_peserta.required" => "ID peserta harus diisi",
            "id_peserta.exists" => "Peserta tidak valid",
            "id_penandatangan.uuid" => "ID penandatangan harus dalam format UUID yang valid",
            "id_penandatangan.exists" => "Penandatangan tidak valid",
            "tanggal_sertifikat.required" => "Tanggal Penandatangan harus diisi",
        ];
    }
}

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
            "id_nilai" => "required|exists:nilai,id",
            "id_peserta" => "required|uuid",
            "nama_penandatangan" => "required|string",
            "nip_penandatangan" => "required|string",
            "jabatan_penandatangan" => "required|string",
            "tanggal_penandatangan" => "required|string",
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
            "id_peserta.uuid" => "ID peserta harus dalam format UUID yang valid",
            "nama_penandatangan.required" => "Nama Penandatangan harus diisi",
            "nip_penandatangan.required" => "NIP Penandatangan harus diisi",
            "jabatan_penandatangan.required" => "Jabatan Penandatangan harus diisi",
            "tanggal_penandatangan.required" => "Tanggal Penandatangan harus diisi",
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresensiRequest extends FormRequest
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
            "tanggal_presensi" => "required|date",
            "latitude" => "required",
            "longitude" => "required",
            "id_keterangan" => "required|exists:keterangan_presensi,id",
            "id_peserta" => "required|uuid",
        ];
    }

    public function message(): array
    {
        return[
            "tanggal_presensi.required" => "Tanggal presensi harus diisi",
            "latitude.required" => "Judul presensi harus diisi",
            "longitude.required" => "Deskripsi presensi harus diisi",
            "id_keterangan.required" => "Keterangan presensi harus diisi",
            "id_keterangan.exists" => "Keterangan presensi tidak valid",
            "id_peserta.required" => "ID peserta harus diisi",
            "id_peserta.uuid" => "ID peserta harus dalam format UUID yang valid",
        ];
    }
}

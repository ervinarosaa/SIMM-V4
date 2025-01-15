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
            "keterangan_presensi" => "required",
            "latitude_presensi" => "required",
            "longitude_presensi" => "required",
            "id_peserta" => "required|uuid",
        ];
    }

    public function message(): array
    {
        return[
            "tanggal_presensi.required" => "Tanggal presensi harus diisi",
            "latitude_presensi.required" => "Latitude presensi harus diisi",
            "longitude_presensi.required" => "Longitude presensi harus diisi",
            "keterangan_presensi.required" => "Keterangan presensi harus diisi",
            "id_peserta.required" => "ID peserta harus diisi",
            "id_peserta.uuid" => "ID peserta harus dalam format UUID yang valid",
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LokasiRequest extends FormRequest
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
            'nama_lokasi' => 'required|max:255',
            'alamat_lokasi' => 'required',
            'maps_url' => 'required',
            'latitude_lokasi' => 'required',
            'longitude_lokasi' => 'required',
            'kuota_magang' => 'required|integer',
            'kuota_terisi' => 'required|integer',
        ];
    }

    public function message(): array
    {
        return[
            'nama_lokasi.required' => 'Nama lokasi harus diisi',
            'nama_lokasi.max' => 'Nama lokasi tidak boleh lebih dari 255 karakter',
            'alamat_lokasi.required' => 'Alamat lokasi harus diisi',
            'maps_url.required' => 'Maps URL lokasi harus diisi',
            'latitude_lokasi.required' => 'Latitude lokasi harus diisi',
            'longitude_lokasi.required' => 'Longitude lokasi harus diisi',
            'kuota_magang.required' => 'Kuota magang harus diisi',
            'kuota_magang.integer' => 'Kuota magang harus berupa angka',
            'kuota_terisi.required' => 'Kuota terisi harus diisi',
            'kuota_terisi.integer' => 'Kuota terisi harus berupa angka',
        ];
    }
}

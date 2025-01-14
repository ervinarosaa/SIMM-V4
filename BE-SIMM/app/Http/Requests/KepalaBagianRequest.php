<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KepalaBagianRequest extends FormRequest
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
            'nama_kabag' => 'required|max:255',
            'nip_kabag' => 'required',
            'id_user' => 'exists:users,id',
            'id_lokasi' => 'required|exists:lokasi,id',
        ];
    }

    public function message(): array
    {
        return[
            'nama_kabag.required' => 'Nama Kepala Bagian harus diisi',
            'nama_kabag.max' => 'Nama Kepala Bagian tidak boleh lebih dari 255 karakter',
            'nip_kabag.required' => 'NIP Kepala Bagian harus diisi',
            'id_user.exists' => 'Akun pengguna tidak valid',
            'id_lokasi.required' => 'Lokasi harus diisi',
            'id_lokasi.exists' => 'Lokasi tidak valid',
        ];
    }
}

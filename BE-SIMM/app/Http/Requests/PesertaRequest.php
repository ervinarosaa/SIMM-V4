<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PesertaRequest extends FormRequest
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
            'nama_peserta' => 'required|max:255',
            'nomor_induk' => 'required|max:255',
            'alamat_peserta' => 'required',
            'id_lokasi' => 'required|exists:lokasi,id',
            'id_status' => 'exists:status,id',
            'foto_profil' => 'nullable|mimes:jpeg,jpg,png',
            'nama_pembimbing' => 'required|max:255',
            'kontak_pembimbing' => 'required|max:255',
            'fakultas' => 'required|max:255',
            'jurusan' => 'required|max:255',
            'id_institusi' => 'required|exists:institusi,id',
            'id_nilai' => 'exists:nilai,id',
            'nomor_telepon' => 'required|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'id_user' => 'exists:user,id',
        ];
    }

    public function message(): array
    {
        return[
            'nama_peserta.required' => 'Nama harus diisi',
            'nama_peserta.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'nomor_induk.required' => 'NIM harus diisi',
            'nomor_induk.max' => 'NIM tidak boleh lebih dari 255 karakter',
            'alamat_peserta.required' => 'Alamat harus diisi',
            'id_lokasi.required' => 'Lokasi harus diisi',
            'id_lokasi.exists' => 'Lokasi tidak valid',
            'id_status.exists' => 'Status tidak valid',
            'foto_profil.mimes' => 'Gambar harus berformat jpeg, jpg, atau png',
            'nama_pembimbing.required' => 'Nama pembimbing harus diisi',
            'nama_pembimbing.max' => 'Nama pembimbing tidak boleh lebih dari 255 karakter',
            'kontak_pembimbing.required' => 'Kontak pembimbing harus diisi',
            'kontak_pembimbing.max' => 'Kontak pembimbing tidak boleh lebih dari 255 karakter',
            'fakultas.required' => 'Fakultas harus diisi',
            'fakultas.max' => 'Fakultas tidak boleh lebih dari 255 karakter',
            'jurusan.required' => 'Jurusan harus diisi',
            'jurusan.max' => 'Jurusan tidak boleh lebih dari 255 karakter',
            'id_institusi.required' => 'Institusi harus diisi',
            'id_institusi.exists' => 'Institusi tidak valid',
            'id_nilai.exists' => 'Nilai tidak valid',
            'nomor_telepon.required' => 'Nomor telepon harus diisi',
            'nomor_telepon.max' => 'Nomor telepon tidak boleh lebih dari 255 karakter',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogbookRequest extends FormRequest
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
            "title" => "required|max:255",
            "deskripsi" => "required",
            "dokumentasi" => "nullable|image|mimes:jpeg,jpg,png|max:2048",
            "tanggal_logbook" => "required|date",
            "id_peserta" => "required|uuid",
        ];
    }

    public function message(): array
    {
        return[
            "title.required" => "Judul logbook harus diisi",
            "title.max" => "Judul tidak boleh lebih dari 255 karakter",
            "deskripsi.required" => "Deskripsi logbook harus diisi",
            "dokumentasi.image" => "File yang diunggah harus berupa gambar",
            "dokumentasi.mimes" => "Gambar harus berformat jpeg, png, atau jpg",
            "dokumentasi.max" => "Ukuran gambar tidak boleh lebih dari 2MB",
            "tanggal_logbook.required" => "Tanggal logbook harus diisi",
            "id_peserta.required" => "ID peserta harus diisi",
            "id_peserta.uuid" => "ID peserta harus dalam format UUID yang valid",
        ];
    }
}

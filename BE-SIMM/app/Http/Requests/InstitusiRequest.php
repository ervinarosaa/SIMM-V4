<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstitusiRequest extends FormRequest
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
            'nama_institusi' => 'required|max:255',
            "id_tingkat" => "required|exists:tingkat_pendidikan,id",
        ];
    }

    public function message(): array
    {
        return[
            'nama_institusi.required' => 'Nama institusi harus diisi',
            'nama_institusi.max' => 'Nama institusi tidak boleh lebih dari 255 karakter',
            "id_tingkat.required" => "Tingkat pendidikan harus diisi",
            "id_tingkat.exists" => "Tingkat pendidikan tidak valid",
        ];
    }
}

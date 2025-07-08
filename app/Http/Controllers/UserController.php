<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    //show user profile
    public function show()
    {
        $user = auth()->user();

        if (!$user) {
            return new ApiResponseResource(false, 'Unauthorized', null);
        }

        return new ApiResponseResource(true, "Data user berhasil diambil", $user);
    }

    /**
     * Update photo profile in cloudinary.
     */
    public function updateProfilePhoto(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $validator = Validator::make($request->only('photo'), [
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'photo.required' => 'Foto profil tidak boleh kosong.',
            'photo.image' => 'Foto profil harus berupa gambar.',
            'photo.mimes' => 'Foto profil tidak sesuai format.',
            'photo.max' => 'Foto profil maksimal 2mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $photoData = $user->updateProfilePhoto($request->file('photo'), $user->id);

            return new ApiResponseResource(
                true,
                "Foto profil berhasil diubah",
                $photoData
            );
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $id = $user->id;
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $validator = Validator::make($request->all(), [

            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'name' => 'sometimes|required|max:50',
        ], [

            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.unique_user_email' => 'Email sudah terdaftar',
            'name.required' => 'Nama tidak boleh kosong',
            'name.max' => 'Nama maksimal 50 karakter',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        $data = $request->all();
        try {
            $updatedUser = User::updateUser($data, $user->id);

            return new ApiResponseResource(
                true,
                "Data berhasil diubah",
                $updatedUser
            );
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }



    
    /**
     * Change password.
     */

    public function changePassword(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'new_password' => 'required|min:8',
            'confirm_new_password' => 'required|min:8|same:new_password',
        ], [
            'password.required' => 'Kata sandi tidak boleh kosong',
            'password.min' => 'Kata sandi minimal 8 digit',
            'new_password.required' => 'Kata sandi baru tidak boleh kosong',
            'new_password.min' => 'Kata sandi baru minimal 8 digit',
            'confirm_new_password.required' => 'Konfirmasi kata sandi tidak boleh kosong',
            'confirm_new_password.min' => 'Konfirmasi kata sandi minimal 8 digit',
            'confirm_new_password.same' => 'Konfirmasi kata sandi tidak sama'
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }


        try {
            if (!Hash::check($request->password, $user->password)) {
                return new ApiResponseResource(
                    true,
                    'Password tidak sesuai',
                    null
                );
            }

            $user->updatePassword($request->new_password);

            return new ApiResponseResource(
                true,
                'Password berhasil diubah',
                null
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }

}

<?php

namespace App\Models;

use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false; // <- Wajib kalau pakai UUID
    protected $keyType = 'string'; // <- Supaya Laravel tahu id adalah string

    /**
     * Atribut yang bisa diisi secara mass-assignment.
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'image_url',
        'image_public_id'
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Tipe data casting atribut.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot method untuk auto-generate UUID saat membuat user baru.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Update the profile photo URL and public_id of the user.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @param string $userId 
     * @return array 
     */
    public function updateProfilePhoto($photo, string $userId)
    {
        $user = self::findOrFail($userId);
        $cloudinary = new Cloudinary();

        if ($user->image_public_id) {
            $cloudinary->uploadApi()->destroy($user->image_public_id);
        }

        $uploadResult = $cloudinary->uploadApi()->upload($photo->getRealPath(), [
            'folder' => 'users',
        ]);
        $user->update([
            'image_url' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
        ]);

        return [
            'image_url' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
        ];
    }

    public static function updateUser(array $dataUser, string $userId): self
    {
        $user = self::findOrFail($userId);
        $user->update([

            'email' => $dataUser['email'] ?? $user->email,
            'name' => $dataUser['name'] ?? $user->name,
        ]);

        return $user;
    }


     /**
     * Update password user.
     *
     * @param string $newPassword
     * @return User
     */
    public function updatePassword(string $newPassword)
    {
        $this->password = Hash::make($newPassword);
        return $this->save();
    }
}

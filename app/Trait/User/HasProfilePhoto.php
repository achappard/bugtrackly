<?php

namespace App\Trait\User;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{

    /**
     * Update the user's profile photo.
     *
     * @param UploadedFile $photo
     * @param false|string $storagePath
     * @return void
     */
    public function updateProfilePhoto(UploadedFile $photo, false|string $storagePath = false)
    {
        if(!$storagePath){
            $storagePath = config('bugtrackly.profile_photo_storage_path');
        }
        tap($this->profile_photo_path, function ($previous) use ($photo, $storagePath) {
            $this->forceFill([
                'profile_photo_path' => $photo->storePublicly(
                    $storagePath, ['disk' => $this->profilePhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {
        if (is_null($this->profile_photo_path)) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

        $this->forceFill([
            'profile_photo_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return Attribute
     */
    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            return $this->profile_photo_path
                ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
                : $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * @return string|null
     */
    private function defaultProfilePhotoUrl(): ?string
    {
        if (config('bugtrackly.use_gravatar_service') && $gravatar_url = $this->get_gravatar_url()) {
            return $gravatar_url;
        }
        return null;
    }

    /**
     * Retourne l'url gravatar de l'utilisateur ou null s'il n'existe pas.
     * @return string|null
     */
    private function get_gravatar_url(): ?string
    {
        // Créez l'URL du Gravatar
        $email = $this->getAttribute('email');
        $size = 120;
        $gravatarUrl = 'https://www.gravatar.com/avatar/' . hash("sha256", strtolower(trim($email))) . '?d=404' . "&s=" . $size;;
        $headers = @get_headers($gravatarUrl);
        if ($headers && strpos($headers[0], '200')) {
            return $gravatarUrl;
        } else {
            return null;
        }
    }

    protected function profilePhotoDisk()
    {
        return config('bugtrackly.profile_photo_disk', 'public');
    }
}

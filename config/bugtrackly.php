<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Baseline for app. Displayed in the footer
    |--------------------------------------------------------------------------
    |
    */
    'baseline'                   => env('BUGTRACKLY_BASELINE', 'Traquez les bugs, boostez la qualité'),


    /*
    |--------------------------------------------------------------------------
    | Support email. Will be displayed in the transactional emails
    |--------------------------------------------------------------------------
    |
    */

    'support_email'              => env('BUGTRACKLY_SUPPORT_EMAIL', 'support@bugtrackly.deefuse.fr'),
    /*
    |--------------------------------------------------------------------------
    | Storage path for the profile photo
    |--------------------------------------------------------------------------
    |
    */
    'profile_photo_storage_path' => env('BUGTRACKLY_USER_AVATAR_STORAGE_PATH', 'profile-photos'),

    /*
   |--------------------------------------------------------------------------
   | Storage path for the project photo
   |--------------------------------------------------------------------------
   |
   */
    'project_photo_storage_path' => env('BUGTRACKLY_PROJECT_PHOTO_STORAGE_PATH', 'profile-photos'),

    /*
    |--------------------------------------------------------------------------
    | Storage disk for the profile photo
    |--------------------------------------------------------------------------
    |
    */
    'profile_photo_disk'         => env('BUGTRACKLY_USER_AVATAR_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Storage disk for the project photo
    |--------------------------------------------------------------------------
    |
    */
    'profile_project_disk'       => env('BUGTRACKLY_PROJECT_PHOTO_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Should we use the gravatar service ?
    |--------------------------------------------------------------------------
    |
    */
    'use_gravatar_service'       => env('BUGTRACKLY_USER_AVATAR_USE_GRAVATAR', false),

    /*
    |--------------------------------------------------------------------------
    | Should we use the initiales for user ?
    |--------------------------------------------------------------------------
    |
    */
    'use_initiales_as_avatar'    => env('BUGTRACKLY_USER_AVATAR_INITIALS', false),

];

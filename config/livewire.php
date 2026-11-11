<?php

return [
    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK'),
        'rules' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        'directory' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DIRECTORY', 'livewire-tmp'),
        'middleware' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_MIDDLEWARE', 'throttle:60,1'),
        'preview_mimes' => ['jpg', 'jpeg', 'png', 'webp'],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],
];

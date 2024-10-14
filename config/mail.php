<?php

return [

    /*
    |--------------------------------------------------------------------------
    | If the email address validation is sended
    |--------------------------------------------------------------------------
    */

    'is_mail_checked' => env('MAIL_EMAIL_CHECKED', true),

    /*
    |--------------------------------------------------------------------------
    | If the password first creation is sended
    |--------------------------------------------------------------------------
    */

    'is_forcing_password_creation' => env('MAIL_FORCE_PASSWORD_CREATION', true),

    /*
    |--------------------------------------------------------------------------
    | If the password change sends an email
    |--------------------------------------------------------------------------
    */

    'is_confirming_password' => env('MAIL_CONFIRM_PASSWORD', true),

];

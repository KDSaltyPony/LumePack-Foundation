<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MFA is mendatory
    |--------------------------------------------------------------------------
    |
    | Should a MFA method be forced on the user (default false)
    |
    */

    'is_mendatory' => env('MFA_IS_MENDATORY', false),

    /*
    |--------------------------------------------------------------------------
    | MFA active methods
    |--------------------------------------------------------------------------
    |
    | An array of active MFA methods
    |
    */

    'methods' => array_filter(explode(',', env('MFA_METHODS', 'totp,email'))),

    /*
    |--------------------------------------------------------------------------
    | MFA password length
    |--------------------------------------------------------------------------
    |
    | The length of the temporary password (default 6)
    |
    */

    'digits' => env('MFA_DIGITS', 6),

    /*
    |--------------------------------------------------------------------------
    | MFA short password validity duration (in seconds)
    |--------------------------------------------------------------------------
    |
    | The period of time before the temporary password has to be renewed
    | For short MFA methods like TOTP
    | (default 30)
    |
    */

    'timeout_sec' => env('MFA_TIMEOUT_SEC', 30),

    /*
    |--------------------------------------------------------------------------
    | MFA short password validity duration (in minutes)
    |--------------------------------------------------------------------------
    |
    | The period of time before the temporary password has to be renewed
    | For long MFA methods like emails
    | (default 10)
    |
    */

    'timeout_min' => env('MFA_TIMEOUT_MIN', 10),

    /*
    |--------------------------------------------------------------------------
    | Email code max attempts
    |--------------------------------------------------------------------------
    |
    | The number of attempts for a code sent by email (default 5)
    |
    */

    'email_attempts' => env('MFA_EMAIL_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Temporary token TTL (in seconds)
    |--------------------------------------------------------------------------
    |
    | The period of validity of the temporary token in cache (default 300)
    |
    */

    'pending_ttl' => env('MFA_PENDING_TTL', 300),

    /*
    |--------------------------------------------------------------------------
    | Temporary token max attempts
    |--------------------------------------------------------------------------
    |
    | The number of attempts for the token (default 5)
    |
    */

    'pending_attempts' => env('MFA_PENDING_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | MFA exempted roles
    |--------------------------------------------------------------------------
    |
    | An array of MFA exempted roles
    |
    */

    'exempted_roles' => explode(',', env('MFA_EXEMPTED_ROLES', '')),

    /*
    |--------------------------------------------------------------------------
    | MFA exempted permissions
    |--------------------------------------------------------------------------
    |
    | An array of MFA exempted permissions
    |
    */

    'exempted_permissions' => explode(',', env('MFA_EXEMPTED_PERMISSIONS', '')),

];

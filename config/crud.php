<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Foreign Key
    |--------------------------------------------------------------------------
    |
    | This user foreign key used in relationships to secure the getters.
    |
    */
    'user_fk' => env('USER_FK', 'user_id'),
];
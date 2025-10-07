<?php

return [

    'default' => env('HASH_DRIVER', 'bcrypt'),

    'bcrypt' => [
        'rounds' => 12,
    ],
    
];

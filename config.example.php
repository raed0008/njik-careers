<?php

return [
    'database' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'database_name',
        'username' => 'database_user',
        'password' => 'change_me',
    ],
    'mail' => [
        'host' => 'mail.example.com',
        'port' => 465,
        'username' => 'sender@example.com',
        'password' => 'change_me',
        'encryption' => 'ssl',
        'from_address' => 'sender@example.com',
        'from_name' => 'نظام التوظيف',
        'to_address' => 'hr@example.com',
        'to_name' => 'الموارد البشرية',
        'debug' => false,
    ],
];

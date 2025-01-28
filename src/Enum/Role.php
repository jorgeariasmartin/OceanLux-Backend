<?php

namespace App\Enum;

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}
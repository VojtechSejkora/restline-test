<?php

namespace App\DB\Enums;

enum StatusEnum: string
{
	case NEW = 'NEW';
	case CLOSED = 'END';
	case ACTIVE = 'ACT';
}

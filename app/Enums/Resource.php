<?php

namespace App\Enums;

enum Resource: string
{
    use ArrayEnum;

    case THE_GUARDING = 'The Guardian';
    case NEWS_API = 'News API';
    case BBC = 'BBC';
}

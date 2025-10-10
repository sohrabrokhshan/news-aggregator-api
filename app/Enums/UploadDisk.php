<?php

namespace App\Enums;

enum UploadDisk: string
{
    use ArrayEnum;

    case LOCAL = 'local';
    case PUBLIC = 'public';
}

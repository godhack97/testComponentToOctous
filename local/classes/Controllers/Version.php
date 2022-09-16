<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Version extends BaseController
{
    public function getVersionAction(): string
    {
        return "1.0.0";
    }
}

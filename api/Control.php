<?php

namespace plugin\control\api;

use plugin\control\expose\api\Control as ApiControl;

class Control
{
    use ApiControl;
    public function __construct()
    {
        $this->path = __DIR__;
    }
}

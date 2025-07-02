<?php

namespace plugin\control\app\admin\controller;

use app\Basic;
use app\expose\build\builder\ComponentBuilder;
use app\expose\build\builder\FormBuilder;
use app\expose\enum\Filesystem;
use app\expose\enum\SubmitEvent;
use app\expose\trait\Config;
use support\Request;

class SettingsController extends Basic
{
    use Config;
    public function basic(Request $request)
    {
        return $this->builder();
    }
}
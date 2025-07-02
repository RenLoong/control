<?php

namespace plugin\control\api\admin;

use app\expose\build\config\Action;
use app\expose\enum\Action as EnumAction;
use app\expose\helper\Config;

class PublicController
{
    public function config($config)
    {
        $request=request();
        $toolbar = new Action();
        $control_config=new Config('basic','control');
        if ($control_config['state']) {
            $toolbar->add(EnumAction::LINK['value'], [
                'icon' => 'ElementPlus',
                'tips' => trans('toolbar Control', [], 'admin', $request->lang),
                'props' => [
                    'url' => '/control/',
                    'target' => '_blank'
                ]
            ]);
            $config->useToolbar($toolbar->toArray());
        }
    }
}

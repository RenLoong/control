<?php

return [
    '' => [
        app\middleware\Template::class,
        app\middleware\Access::class,
        app\middleware\Platform::class
    ],
    'control' => [
        plugin\control\app\middleware\Auth::class
    ],
    'admin' => [
        plugin\control\app\admin\middleware\Auth::class
    ]
];

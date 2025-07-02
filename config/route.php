<?php

use Webman\Route;

Route::get('/control', [plugin\control\app\control\controller\IndexController::class, 'index']);
Route::get('/control/', [plugin\control\app\control\controller\IndexController::class, 'index']);

$controllersClass = glob(base_path('plugin/control') . '/app/control/controller/*Controller.php');
$len = count($controllersClass);
$routes = [];
for ($i = 0; $i < $len; $i++) {
    $value = $controllersClass[$i];
    $controllerName = str_replace('Controller', '', basename($value, '.php'));
    $classStr = str_replace([base_path(), '.php', '/'], ['', '', '\\'], $value);
    $reflection = new \ReflectionClass($classStr);

    // 忽略抽象类、接口
    if ($reflection->isAbstract() || $reflection->isInterface()) {
        continue;
    }

    $actions = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $actionsLen = count($actions);
    for ($n = 0; $n < $actionsLen; $n++) {
        $name = $actions[$n]->name;
        if (!str_starts_with($name, '__')) {
            $routes["/{$controllerName}/{$name}"] = [$classStr, $name];
        }
    }
}
$controllersVersionClass = glob(base_path('plugin/control') . '/app/control/controller/**/*Controller.php');
if (!empty($controllersVersionClass)) {
    $len = count($controllersVersionClass);
    for ($i = 0; $i < $len; $i++) {
        $value = $controllersVersionClass[$i];
        $version = basename(dirname($value));
        $controllerName = str_replace('Controller', '', basename($value, '.php'));
        $classStr = str_replace([base_path(), '.php', '/'], ['', '', '\\'], $value);
        $reflection = new \ReflectionClass($classStr);

        // 忽略抽象类、接口
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            continue;
        }

        $actions = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $actionsLen = count($actions);
        for ($n = 0; $n < $actionsLen; $n++) {
            $name = $actions[$n]->name;

            if (!str_starts_with($name, '__')) {
                $routes["/{$version}/{$controllerName}/{$name}"] = [$classStr, $name];
            }
        }
    }
}
Route::group('/control', function () use ($routes) {
    foreach ($routes as $key => $value) {
        if (in_array($value[1], ['indexUpdateField', 'indexUpdateState'])) {
            Route::post($key, $value);
        } else if (strpos($value[1], 'GetTable')) {
            Route::get($key, $value);
        } else {
            Route::any($key, $value);
        }
    }
});

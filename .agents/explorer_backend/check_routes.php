<?php

$routesInViews = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($it as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $content = file_get_contents($file->getPathname());
        if (preg_match_all('/route\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            foreach ($matches[1] as $r) {
                $rel = str_replace('resources/views' . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $routesInViews[$r][] = $rel;
            }
        }
    }
}

// Load Laravel route names
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$definedRoutes = [];
foreach (Illuminate\Support\Facades\Route::getRoutes() as $route) {
    if ($route->getName()) {
        $definedRoutes[$route->getName()] = [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'action' => $route->getActionName(),
        ];
    }
}

echo "=== ROUTE NAMES IN VIEWS VS DEFINED ROUTES ===\n";
foreach ($routesInViews as $name => $views) {
    if (!isset($definedRoutes[$name])) {
        echo "MISSING ROUTE: '{$name}' used in:\n";
        foreach (array_unique($views) as $v) {
            echo "   - {$v}\n";
        }
    } else {
        echo "OK: '{$name}' -> {$definedRoutes[$name]['uri']} [".implode(',', $definedRoutes[$name]['methods'])."]\n";
    }
}

echo "\n=== DEFINED ROUTES NOT USED IN ANY BLADE ROUTE() CALL ===\n";
foreach ($definedRoutes as $name => $info) {
    if (!isset($routesInViews[$name])) {
        echo "UNUSED BY route(): '{$name}' ({$info['uri']})\n";
    }
}

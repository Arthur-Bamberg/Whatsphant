<?php
require_once __DIR__ . '/utils/globals.php';
require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';

    $namespaceMap = [
        'Model' => 'models/',
        'Service' => 'services/',
        'Util' => 'utils/'
    ];

    foreach ($namespaceMap as $namespace => $subdir) {
        if (strpos($class, $namespace . '\\') === 0) {
            $file = $baseDir . $subdir . substr($class, strlen($namespace) + 1);

            if($namespace == 'Model') {
                $file .= '.class';
            }

            $file .= '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

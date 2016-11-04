<?php

declare(strict_types = 1);
const ROOT = __DIR__ . '/../src/';

/**
 * Step 1: Simple auto file loader
 */
spl_autoload_register(function ($class) {
    $class_path = ROOT . str_replace(['\\', '_'], '/', $class) . '.php';
    $class_path = str_replace('AEngine/Orchid/Memory/', '', $class_path);

    if (file_exists($class_path)) {
        require_once($class_path);

        return;
    }
});

/**
 * Step 1.1 (only for demo): Load wrap
 */
require_once(ROOT . '/Mem.php');

/**
 * Step 2: Connect to the server
 * Note: by default connect to Memcache
 */
Mem::setup([
    [
        'host'    => 'localhost',
        'port'    => '11211',
        'timeout' => 10,
    ],
]);

// read data from storage
echo Mem::get('foo', function () {
    return 'baz';
}) . PHP_EOL;
# for first time return 'baz' (because no data in storage)

// write data to storage
Mem::set('foo', 'bar');

// read data from storage
echo Mem::get('foo') . PHP_EOL;
# return bar

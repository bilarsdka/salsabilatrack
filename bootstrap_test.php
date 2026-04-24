<?php

try {
    $app = require 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Http\Kernel');
    echo "Bootstrap OK\n";
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo 'File: '.$e->getFile().' Line: '.$e->getLine()."\n";
}

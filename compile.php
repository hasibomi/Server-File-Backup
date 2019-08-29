<?php

// Source: https://blog.programster.org/creating-phar-files#targetText=Steps,for%20your%20application's%20source%20code.&targetText=Copy%20all%20of%20your%20PHP,and%20modify%20the%20script%20below).

$directory = __DIR__;
$pharFile = 'backup.phar';

$phar = new Phar($pharFile);

$phar->startBuffering();

$defaultStub = $phar->createDefaultStub('index.php');


$phar->buildFromDirectory($directory);

$stub = "#!/usr/bin/php \n" . $defaultStub;

$phar->setStub($stub);

$phar->stopBuffering();

echo 'Done', "\n";

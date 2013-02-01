<?php
if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

require $autoloadFile;

//if (file_exists($file = __DIR__.'/autoload.php')) {
//    require_once $file;
//} elseif (file_exists($file = __DIR__.'/autoload.php.dist')) {
//    require_once $file;
//}

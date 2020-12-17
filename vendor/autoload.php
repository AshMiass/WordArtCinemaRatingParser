<?php
namespace Ashmiass;

class Autoload
{
    public static function autoload($className)
    {
        $className = ltrim($className, '\\');
        $fileName  = __DIR__ . DIRECTORY_SEPARATOR;
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        require $fileName;
    }

    public static function registerAutoload()
    {
        spl_autoload_register('Ashmiass\Autoload::autoload');
    }
}

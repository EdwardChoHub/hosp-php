<?php

/** 引入需要测试的文件 */
require_once '../hosp.php';

/** 引入测试基类 */
require_once './TestCase.php';

/**
 * @notes 类加载函数
 * @param $class
 */
function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');
<?php
/** 用户自定义代码 */
namespace hosp;

$entrance = stripos($_SERVER['REQUEST_URI'], 'admin') !== false ?'admin' :'home';

//开发开关
$pro = file_exists('pro');
if($pro){
    config('database', [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'hosp',
        'username' => 'hosp',
        'password' => 'ByXL25tSziRbEwPC',
        'table_prefix' => 'hp_',
    ]);
}else{
    config('database', [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'hosp',
        'username' => 'root',
        'password' => 'root',
        'table_prefix' => 'hp_',
    ]);
}
config('database', [
    'soft_delete' => [
        'timeline.dtime' => 0,
    ],
    'soft_delete_value' => function(){
        return time();
    }
]);

config('database.auto_event.timestamp', [
    'timeline' => [
        'create_time' => 'ctime',
    ]
]);

config('view',[
    'path' => APP_PATH . '/php/' . $entrance . '/view',
    'layout' => [
        'top' => 'layout/top',
        'bottom' => 'layout/bottom'
    ],
]);

require_once __DIR__ . DS . 'php' . DS . $entrance . DS . 'config.php';
require_once __DIR__ . DS . 'php' . DS . $entrance . DS . 'action.php';
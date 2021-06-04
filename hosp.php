<?php

namespace hosp;

use Exception;
use PDO;
use ReflectionFunction;

_init();

/** 内置用户自定义代码 */
function _custom()
{

}

/**
 * @notes 应用配置
 */
config('app', [
    /** 外部文件（无顺序要求）, 建议对代码进行分类存储，例如action,model存一起，app,database配置类存一起 */
    'extra_custom' => [
        'custom.php',
    ],
    //调试模式，会检测配置信息
    'debug' => true,
    //默认全局过滤器 逗号分隔
    'default_filter' => 'htmlspecialchars_decode,trim',
    // 默认返回类型
    'default_type' => 'json',
    /** 日志 */
    'log' => [
        /** 日志路径 */
        'path' => ROOT_PATH . DS . 'log',
        /** 写入日志等级最低要求（用于生产环境修改此值减少日志量） */
        'require' => LOG_NORMAL,
    ],
    /** 默认控制器 */
    'default_controller' => 'index',
    /** 默认控制器方法 */
    'default_method' => 'index',
]);


/**
 * @notes 数据库配置
 */
config('database', [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'tran_group',
    'username' => 'root',
    'password' => 'root',
    //表名前缀
    'table_prefix' => 'hosp_',
    //打开得编码形式
    'charset' => 'utf8mb4',
    /** 功能声明 */
    'declare' => [
        //全局配置
        '' => [
            /** 自动填充字段去除null */
            'no_null' => true,
            /** 自动补全插入(插入时参数补全系统自动插入默认值) */
            'complete_insert' => true,
            /** 自动时间戳 array|false 全局配置 */
            'auto_timestamp' => [
                'create_time' => 'ctime',
                'update_time' => 'mtime',
                'delete_time' => 'dtime'
            ],
            //自动关联查询，设定关联后会自动查询或者遵守格式(user_id为用户表的ID字段，user_ids则为用户表的ID集合值格式为(1,2,3,4))
            'relation' => [
                //源ID(主键) => [外键1 => 表.关系1, 外键2 => 关系2]
                'role_id' => 'role.id',
            ],
        ],
        'user' => [
            /** 自动时间戳（覆盖全局） array|bool */
            'auto_timestamp' => [
                'create_time' => 'ctime',
                'update_time' => 'mtime',
                'delete_time' => 'dtime'
            ],
            //自动关联查询，设定关联后会自动查询或者遵守格式(user_id为用户表的ID字段，user_ids则为用户表的ID集合值格式为(1,2,3,4))
            'relation' => [
                'user.role_id' => 'role.id',
            ],
        ]
    ],
]);

/**
 * @notes 配置路由
 */
config('router', [
]);


/**
 * @notes 控制器方法注册，不建议直接修改，请重写编写该action进行覆盖，方便后期升级
 *
 */
config('action', [
    /** 上传图片 */
    '/system/upload_image' => function () {
        $param = 'file';
        $path = APP_PATH;
        $dir = DS . 'upload' . DS;
        $exts = ['jpg', 'png', 'jpeg', 'ico', 'gif'];


        $maxSize = 4096;
        list($result, $error) = upload_file_valid($param, $exts, $maxSize);
        if (!$result) {
            return error($error);
        }

        list($name, $ext) = upload_file_name_ext($param);

        $filename = $dir . md5($name) . '.' . $ext;

        if (!upload_file_move($param, $path . $filename)) {
            return error('上传失败');
        }

        return true([
            'url' => $filename
        ]);
    },
    /** 验证码图片 */
    '/system/verify_code_image' => function () {
        /** 验证码长度 */
        $number = 4;
        $width = input('width', 200);
        $height = input('height', 100);
        $codes = implode(
            "",
            array_merge(
                range(0, 9),
                range("a", "z"),
                range("A", "Z")
            )
        );
        $code = substr(str_shuffle($codes), 0, $number);

        /** 保存验证码 */
        verify_code_save($code);

        /** 输出验证图片 */
        verify_code_image($code, $width, $height);
    },
    /** 注册 */
    '/system/register' => function () {
//        $username = input('username');
//        $password = input('password');
//        $encode = 'md5';
//
//        if (empty($username)) {
//            return error('账户名不能为空');
//        }
//
//        if (strval($username) < 8 and strval($username) > 16) {
//            return error('账户名有效长度为8-16位');
//        }
//
//        if (empty($password)) {
//            return error('密码不能为空');
//        }
//        if (strval($password) != 32) {
//            return error('密码长度异常');
//        }
        verify_code_check('');
//        $password = call_user_func($encode, $password);
//
//        /** 补充注册逻辑 */
//
//        return success();
    },
    /** 登录 */
    '/system/login' => function () {
//        $username = input('username');
//        $password = input('password');
//        if (empty($username)) {
//            return error('请输入用户名');
//        }
//        if (empty($password)) {
//            return error('请输入密码');
//        }
//
//        /** 缺失登录逻辑 */
//
//        /** 用户用户ID */
//        $id = 0;
//
//        /** 保存用户信息 */
        user_login(0, '');
//
//        return success();
    },
    /** 登出 */
    '/system/logout' => function () {
        user_logout();
        return success();
    },
    /** 检查登录 */
    '/system/check_login' => function () {
        return !empty(user_id()) ? success() : error();
    }
]);

/**
 * @notes 模型方法注册
 */
config('model', []);

/**
 * @notes 接口自动校验器，指定校验器自动检验得接口
 */
config('validate', [
    'edit_user' => [
        '/user/info',
        '/user/user',
    ]
]);

/**
 * @notes 事件钩子
 */
config('event', [
    'after_init' => function () {
    },
    'before_router' => function () {
    },
    'after_router' => function ($route) {
    },

    'before_action' => function ($action, $input) {
    },
    'after_action' => function ($action, $output) {
    },
    'before_model' => function ($model, $input) {
    },
    'after_model' => function ($model, $output) {
    },
    'before_hosp' => function ($express, $input) {
    },
    'after_hosp' => function ($express, $output) {
    },
    'before_sql' => function ($sql) {
    },
    'after_sql' => function ($sql, $result) {
    },
    'before_complete' => function ($response) {
    }
]);

/** @notes 监听者 */
config('listen', [
    'after_init' => [
        function () {

        },
    ]
]);


/**
 * @notes URL映射
 */
config('url', []);

/** 框架执行 */
_run();


/** Util */

/**
 * 综合打印
 * @param $data
 * @param bool $return
 * @return string|void
 */
function dump($data, $return = false)
{
    if (gettype($data) == 'object') {
        return var_export($data, $return);
    } else {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($return) {
            return $data;
        } else {
            echo $data;
        }
    }
}

/**
 * 获取或配置参数
 * @param $name
 * @param string $value
 * @return array|mixed
 */
function config($name = '', $value = '')
{
    $data = &$GLOBALS['hosp'];
    if (empty($name)) {
        return $data;
    }

    if ($data == null) {
        $data = [];
    }
    //使用索引去操作
    $indexList = explode('.', $name);
    if ($value !== '') {
        $config = &$data;
        $count = sizeof($indexList);
        for ($i = 0; $i < $count - 1; $i++) {
            if (empty($indexList[$i])) {
                continue;
            }
            $config = &$config[$indexList[$i]];
        }
        if (is_array($config[$indexList[$count - 1]]) && is_array($value)) {
            $config[$indexList[$count - 1]] = array_merge($config[$indexList[$count - 1]], $value);
        } else {
            $config[$indexList[$count - 1]] = $value;
        }
        return null;
    } else {
        $config = $data;
        foreach ($indexList as $index) {
            if (!isset($config[$index])) {
                return null;
            }
            $config = $config[$index];
        }
        return $config;
    }
}

/**
 * session函数
 * @param $name
 * @param string $value
 * @return array|mixed|string
 */
function session($name = null, $value = '')
{
    session_id() || session_start();

    if (empty($name)) {
        return $_SESSION['hosp'];
    }


    if (!isset($_SESSION['hosp'])) {
        $_SESSION['hosp'] = [];
    }
    //使用索引去操作
    $indexList = explode('.', $name);
    if ($value !== '') {
        $config = &$_SESSION['hosp'];
        $count = count($indexList);
        for ($i = 0; $i < $count - 1; $i++) {
            if (empty($indexList[$i])) {
                continue;
            }
            $config = &$config[$indexList[$i]];
        }
        $config[$indexList[$count - 1]] = $value;
        return null;
    } else {
        $config = $_SESSION['hosp'];
        foreach ($indexList as $index) {
            if (!isset($config[$index])) {
                return null;
            }
            $config = $config[$index];
        }
        return $config;
    }
}

/** 日志
 * @param $content mixed 日志内容
 * @param $filename string 文件名
 */
function log($content, $filename)
{
    if (!defined('LOG_ID')) {
        define('LOG_ID', ID);
    }
    if (!defined('LOG_PATH')) {
        $logPath = ROOT_PATH . DS . 'log' . DS;

        //加入时间目录结构
        $logPath .= sprintf(
            '%s' . DS . '%s' . DS . '%s' . DS,
            date('Y', TIME),
            date('m', TIME),
            date('d', TIME)
        );
        define('LOG_PATH', $logPath);
    }
    $logPath = LOG_PATH;

    //拆分文件名中的目录和实际文件名进行拼装
    $temp = explode('/', $filename);
    if (count($temp) > 0) {
        $filename = array_pop($temp);
        if (count($temp) > 0) {
            $dir = implode('/', $temp);
            $logPath .= $dir . '/';
        }
    }


    if (!is_dir($logPath)) {
        mkdir($logPath, 0777, true);
    }

    $logFile = $logPath . (($filename ? $filename : 'event') . '.log');

    $file = fopen($logFile, 'a');
    fwrite($file, date('Y-m-d H:i:s', time()) . "\n" . 'ID: ' . LOG_ID . "\n" . $content . "\n");
    fclose($file);
}

/**
 * @notes 函数调用
 * @param $name string 函数配置名
 * @param array $args
 * @return mixed|void
 */
function _callback(string $name, $args = [])
{
    try {
        $callback = config($name);
        if (is_callable($callback)) {
            $reflectFunction = new ReflectionFunction($callback);
            return $reflectFunction->invokeArgs($args);
        } else {
            throw new Exception();
        }
    } catch (Exception $e) {
        _error("调用{$name}函数异常");
    }
}

/** Request */

/**
 * @return bool
 * @author EdwardCho
 */
function is_post()
{
    return $_SERVER["REQUEST_METHOD"] == "POST";
}

/**
 * @return bool
 * @author EdwardCho
 */
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == "GET";
}

/**
 * 获取入参
 * @param $name
 * @param null $default
 * @return mixed|null
 */
function input($name = null, $default = null)
{
    //懒加载，使用到入参信息才会去获取
    if (!isset($GLOBALS['REQUEST_PARAMS'])) {
        $url = $_SERVER['REQUEST_URI'];

        if (!is_null($url)) {
            //拿到URL中的入参信息
            parse_str($url, $urlParams);
        } else {
            $urlParams = [];
        }


        //入参参数优先级 json > post > get > urlParams(url中的参数，处理加密url中没有被php解析到的参数)
        $json = json_decode(file_get_contents("php://input"), true);
        $json = empty($json) ? [] : $json;

        $params = array_merge($urlParams, $_GET, $_POST, $json);
        _input_filter($params);
        $GLOBALS['REQUEST_PARAMS'] = $params;
    }
    if (isset($GLOBALS['REQUEST_USER_PARAMS'])) {
        if (is_array($GLOBALS['REQUEST_USER_PARAMS'])) {
            $GLOBALS['REQUEST_PARAMS'] = array_merge($GLOBALS['REQUEST_PARAMS'], $GLOBALS['REQUEST_USER_PARAMS']);
        }
        unset($GLOBALS['REQUEST_USER_PARAMS']);
    }
    if ($name === null) {
        return $GLOBALS['REQUEST_PARAMS'];
    }
    return isset($GLOBALS['REQUEST_PARAMS'][$name]) ? $GLOBALS['REQUEST_PARAMS'][$name] : $default;
}

/**
 * @notes 参数过滤
 * @param $params array
 * @return void
 * @author EdwardCho
 */
function _input_filter(array &$params)
{
    $filters = config('app.default_filter');
    $filters = explode(',', $filters);
    foreach ($params as &$param) {
        foreach ($filters as $filter) {
            if (empty($filter)) {
                continue;
            }
            if (is_array($param)) {
                _input_filter($param);
            } else {
                $param = call_user_func($filter, $param);
            }
        }
    }
}


/**
 * url组装，优先使用注册的url
 * @param $url
 * @param array $params 拼接参数
 * @param bool $extendParams 继承先有的入参
 * @return string
 */
function url($url = null, $params = [], $extendParams = false)
{
    $urls = config('url');
    foreach ($urls as $name => $value) {
        if ($url == $name) {
            $url = $value;
            break;
        }
    }
    if (empty($url)) {
        $url = ACTION;
    }

    if (substr($url, 0, 1) != '/') {
        $url = '/' . $url;
    }

    $url = '/' . ENTRANCE_FILE . $url;

    if ($extendParams) {
        $params = array_merge(input(), $params);
    }

    if (count($params)) {
        $url .= '?' . http_build_query($params);
    }

    return $url;
}

/**
 * @notes 处理响应内容
 * @param $data
 * @param $type
 * @return array
 */
function _response($data, $type = 'json')
{
    switch ($type) {
        case 'json':
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            $type = 'html';
            break;
    }
    return [$data, $type];
}

/** Response */
/**
 * @notes action 出参包装
 * @param string $msg
 * @param int $code
 * @return array
 */
function error($msg = '', $code = 1)
{
    return _response([
        'msg' => $msg,
        'code' => $code
    ], 'json');
}

/**
 * @notes action 出参包装
 * @param array $data
 * @param int $code
 * @param string $msg
 * @return array
 */
function success($msg = '', $data = [], $code = 0)
{
    return _response([
        'data' => $data,
        'code' => $code,
        'msg' => $msg
    ], 'json');
}

/** Package */
/**
 * @notes 成功
 * @param array $data
 * @return array
 */
function true($data = [])
{
    return [true, $data];
}

/**
 * @notes 失败
 * @param string $msg
 * @return array
 */
function false($msg = '')
{
    return [false, $msg];
}

/**
 * 注册路由器
 * @param $before
 * @param $after
 * @author EdwardCho
 */
function router($before, $after)
{
    $router = config('router');
    $router = is_null($router) ? [] : $router;
    $router = array_merge($router, [$before => $after]);
    config('router', $router);
}

/**
 * @notes 上传文件保存
 * @param $name string 文件名
 * @param $destination string 目标文件路径
 * @return bool
 * @author EdwardCho
 */
function upload_file_move(string $name, string $destination)
{
    if (!isset($_FILES[$name])) {
        _error("{$name}文件不存在");
    }
    $temp = explode("/", $destination);
    array_pop($temp);
    $path = implode("/", $temp);

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    $res = move_uploaded_file($_FILES[$name]['tmp_name'], $destination);
    if (!$res) {
        _error('系统权限不足，无法上传文件');
    }
    return $res;
}

/**
 * 获取上传文件名与拓展名
 * @param $name
 * @return array
 * @author EdwardCho
 */
function upload_file_name_ext($name)
{
    $file = $_FILES[$name];

    if (empty($file)) {
        _error("{$name}文件不存在");
    }

    $data = explode('.', $file['name']);

    $ext = array_pop($data);

    $name = implode('.', $data);

    return [$name, $ext];
}

/**
 * @notes 上传文件有效性校验
 * @param $name string 文件名
 * @param array $exts
 * @param null $maxSize
 * @return array
 * @author EdwardCho
 */
function upload_file_valid(string $name, $exts = [], $maxSize = null)
{
    $file = $_FILES[$name];
    if (!isset($file)) {
        return false("{$name}文件不存在");
    }
    $temp = explode(".", $name);
    $ext = array_pop($temp);
    if (!in_array($ext, $exts)) {
        return false("上传文件的格式为{$ext}, 非合法格式：" . implode(",", $exts));
    }
    if (!is_null($maxSize) && $file['size'] > $maxSize) {
        return false("上传文件大小为{$file['size']}，已超过$maxSize");
    }
    return true();
}

/** Verify Code */

/**
 * @notes 图片验证码
 * @param $code
 * @param $width
 * @param $height
 */
function verify_code_image($code, $width, $height)
{
    //创建画布
    $image = imagecreatetruecolor($width, $height);
    //白色背景
    $white = imagecolorallocate($image, 255, 255, 255);
    //字体颜色
    $font_color = imagecolorallocate(
        $image,
        rand(0, 255),
        rand(0, 255),
        rand(0, imagefill($image, 0, 0, $white))
    );
    //字体类型和大小
    imagestring($image, 5, 10, 10, $code, $font_color);
//    imagettftext($image, 24, 0, 5, 20, $font_color, "/.ttf", $_SESSION['system']['imgCode']);
    //干扰线
    for ($i = 0; $i < 80; $i++) {
        $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imagesetpixel($image, rand(0, $width), rand(0, $height), $color);
    }
    for ($i = 0; $i < 5; $i++) {
        $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $color);
    }
    ob_clean();
    header("Content-type: image/png");
    imagepng($image);
    imagedestroy($image);
}

/**
 * 验证码保存
 * @param $code
 * @author EdwardCho
 */
function verify_code_save($code)
{
    session('verify_code', $code);
}

/**
 * @notes 校验验证码
 * @param $code
 * @return array
 */
function verify_code_check($code)
{
    $sessCode = session('verify_code');
    if (empty($sessCode)) {
        return false('请先获取验证码');
    }
    if (empty($code)) {
        return false('验证码不能为空');
    }
    if ($sessCode != $code) {
        return false('验证码不正确');
    } else {
        return true();
    }
}


/** Framework */

/** 框架初始化 */
function _init()
{

    define('MICRO_TIME', microtime(true));
    define('TIME', intval(MICRO_TIME));

    /** 当前文件名 */
    $temp = explode('\\', __FILE__);

    define('APP_PATH', __DIR__);
    define('FILE_NAME', str_ireplace('.php', '', array_pop($temp)));
    define('FILE', array_pop($temp));

    //当前入口文件名
    $temp = explode('/', $_SERVER['REQUEST_URI']);
    foreach ($temp as $item) {
        if (stripos($item, '.php')) {
            define('ENTRANCE_FILE', $item);
            define('ENTRANCE', str_ireplace('.php', '', $item));
            break;
        }
    }
    if (!defined('ENTRANCE_FILE')) {
        define('ENTRANCE_FILE', FILE_NAME);
        define('ENTRANCE', FILE);
    }

    /** Composer */
    if (is_file(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    define("DS", DIRECTORY_SEPARATOR);
    define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
    define("UPLOAD_PATH", ROOT_PATH . DS . "upload");

    //validate正则类型
    define('REQ', 'require');
    define('INT', 'int');
    define('FLOAT', 'float');
    define('MOBILE', 'mobile');
    define('EMAIL', 'email');
    define('ARR', 'array');

    //validate支持的所有正则
    define('VALIDATE_TYPE_ARRAY', [REQ, INT, FLOAT, MOBILE, EMAIL, ARR]);


    /** 日志类型(等级) */
    define('LOG_NORMAL', 1);
    define('LOG_WARN', 3);
    define('LOG_ERROR', 5);

    /** 响应code值 */
    /** 权限不足 */
    define('HTTP_CODE_FORBIDDEN', 403);
    /** 服务器错误 */
    define('HTTP_CODE_ERROR', 502);
    /** 失败 */
    define('HTTP_CODE_FAIL', 500);
    /** 成功 */
    define('HTTP_CODE_SUCCESS', 200);

    /** 生成ID */
    if (!defined('ID')) {
        $uid = uniqid("", true);
        if (!empty($uid) && !isset($_SERVER)) {
            $data = '';
            $data .= $_SERVER['REQUEST_TIME'];
            $data .= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $data .= isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '';
            $data .= isset($_SERVER['LOCAL_PORT']) ? $_SERVER['LOCAL_PORT'] : '';
            $data .= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $data .= isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '';
            $hash = strtoupper(hash('ripemd128', $uid . md5($data)));
            $id =
                substr($hash, 0, 8) .
                '-' .
                substr($hash, 8, 4) .
                '-' .
                substr($hash, 12, 4) .
                '-' .
                substr($hash, 16, 4) .
                '-' .
                substr($hash, 20, 12);
        } else {
            $id = md5(strval(microtime() . rand(0, 9999)));
        }
        define('ID', $id);
    }

}

/** 框架执行 */
function _run()
{
    error_reporting(E_ERROR);

    if (config('app.debug')) {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
    }

    if (function_exists('hosp\_custom')) {
        call_user_func('hosp\_custom');
    }
    $files = config('app.extra_custom');
    if (!is_array($files)) {
        $files = [$files];
    }
    foreach ($files as $file) {
        /** 引入外部用户自定义代码（建议使用，方便后期升级） */
        $filename = APP_PATH . DS . $file;
        if (file_exists($filename)) {
            @require_once $filename;
        }
    }


    /** 页面不进行直接渲染 */
    ob_start();

    /** 初始化钩子 */
    _callback('event.after_init');

    /** 事件钩子 */
    _callback('event.before_router');

    /** 获取路由信息 */
    $route = _route();

    /** 路由转换 */
    $route = _router($route);

    $url = '/' . implode('/', $route);

    define('ACTION', $url);

    /** 事件钩子 */
    _callback('event.after_router', ['route' => $route]);

    $params = input();

    /** 入参校验器 */
    list($success, $result) = validate($url, $params);
    if (!$success) {
        _end(error($result));
    }

    _callback('event.before_action', [
        'action' => $url,
        'input' => $params,
    ]);

    /** 调用相应方法 */
    $object = action($url, $params);
    if (!$object) {
        _end(error('请求接口不存在'));
    }

    _callback('event.after_action', [
        'action' => $url,
        'output' => $object
    ]);

    /** 响应内容 */
    _end($object);


}

/**
 * 结束执行
 * @param $response mixed
 * @author EdwardCho
 */
function _end($response)
{
    if (is_string($response)) {
        $response = [$response, 'html'];
    }
    _callback('event.before_complete', [
        'response' => $response
    ]);

    switch ($response[1]) {
        case 'html':
            echo $response[0];
            break;
    }

    /** 页面缓存输出 */
    ob_end_flush();


    die();
}

/** 路由解析 */
function _route()
{
    $url = parse_url($_SERVER['REQUEST_URI'])['path'];
    $index = stripos($url, ".php");
    $url = substr($url, $index ? $index + 4 : 0);
    $url = (substr($url, 0, 1) == '/') ? substr($url, 1) : $url;
    //伪静态
    $url = str_ireplace(['.html', '.htm'], ['', ''], $url);

    $data = explode('/', $url);

    $controller = array_shift($data);
    $method = array_shift($data);

    if (empty($controller)) {
        $controller = config('app.default_controller');
    }
    if (empty($method)) {
        $method = config('app.default_method');
    }

    //URL格式检验
    if (empty($controller) || empty($method)) {
        _error("无法正常解析URL，检查URL格式是否正确");
    }

    return [$controller, $method];
}

/**
 * @notes 路由器转换
 * @param $route
 * @return array
 */
function _router($route)
{
    list($currentController, $currentMethod) = $route;
    if ($currentMethod === '') {
        $currentMethod = '*';
    }


    $route = config('router');

    foreach ($route as $before => $after) {

        list($temp, $beforeController, $beforeMethod) = explode('/', $before);
        unset($temp);

        if (empty($beforeController)) {
            $beforeController = '*';
        }
        if (empty($beforeMethod)) {
            $beforeMethod = '*';
        }

        //第一种完全一样
        $flag1 = $currentController == $beforeController && $currentMethod == $beforeMethod;
        //第二种后模糊
        $flag2 = $currentController == $beforeController && $beforeMethod == '*';
        //第三种前模糊
        $flag3 = $beforeController == '*' && $currentMethod == $beforeMethod;
        //第四种全模糊
        $flag4 = $beforeController == '*' && $beforeMethod == '*';

        if ($flag1 || $flag2 || $flag3 || $flag4) {
            if (is_callable($after)) {
                $after();
                die();
            }
            //路由转发支持转发到其他http网站
            if (stripos($after, 'http') > -1) {
                //将入参填充到url中
                header('Location:' . $after . '?' . http_build_query(input()));
                die();
            }

            //去除开头的/
            list($temp, $controller, $method) = explode('/', $after);
            unset($temp);

            if (empty($controller) || $controller === '*') {
                $controller = $currentController;
            }
            if (empty($method) || $method === '*') {
                $method = $currentMethod;
            }

            return [$controller, $method];
        }
    }
    return [$currentController, $currentMethod];
}

/**
 * @notes 错误处理
 * @param $content
 */
function _error($content)
{
    log($content, 'error');
    if (!config('app.debug')) {
        $content = '';
    }
    _end([$content, 'html']);
}

/** API操作 */

/**
 * 校验器
 * @param $url
 * @param $params
 * @return array
 */
function validate($url, $params)
{
    $data = config("validate.$url");

    if (!empty($data)) {
        foreach ($data as $name => $rules) {
            //获取设定的和法值
            $values = array_diff_key($rules, VALIDATE_TYPE_ARRAY);

            if (!isset($params[$name]) && in_array(REQ, $rules)) {
                return false($name . '参数是必填的');
            } else {
                if (count($values) > 0 && !in_array($params[$name], $values)) {
                    return false($name . '参数值非法');
                } elseif (isset($params[$name])) {
                    $exp = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

                    if (intval($params[$name]) != $params[$name] && in_array('int', $rules)) {
                        $result = '整数';
                    } elseif (!is_numeric($params[$name]) && in_array('float', $rules)) {
                        $result = '浮点数';
                    } elseif (preg_match('/^1\d{10}$/', $params[$name]) && in_array('mobile', $rules)) {
                        $result = '手机号';
                    } elseif (preg_match($exp, $params[$name]) && in_array('email', $rules)) {
                        $result = '邮箱';
                    } else {
                        $result = true;
                    }
                    if ($result !== true) {
                        return false($name . '参数必须是' . $result);
                    }
                }
            }
        }
    }

    return true($params);
}

/**
 * @notes 触发事件
 * @param $name
 * @param array $args
 * @version
 * @author EdwardCho
 * @date 2021/6/4 21:00
 */
function event($name, $args = [])
{
    try {
        $functions = config("listen.{$name}");
        if (!is_array($functions)) {
            $functions = [$functions];
        }
        foreach ($functions as $function) {
            $rf = new ReflectionFunction($function);
            $rf->invokeArgs($args);
        }
    } catch (Exception $e) {
        _error("调用{$name}钩子异常，错误内容：" . $e->getMessage());
    }
}


/**
 * @notes 控制器方法（注册|调用）
 * @param $express
 * @param array $params
 * @return void|mixed
 */
function action($express, $params = [])
{
    if (is_callable($params)) {
        config("action.{$express}", $params);
    } else {
        try {
            $GLOBALS['REQUEST_USER_PARAMS'] = $params;
            $action = config("action.$express");
            $result = null;
            if (is_callable($action)) {
                $args = [];
                $reflectFunction = new ReflectionFunction($action);
                foreach ($reflectFunction->getParameters() as $param) {
                    $name = $param->getName();
                    $defaultValue = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                    $args[$name] = isset($params[$name]) ? $params[$name] : $defaultValue;
                }

                $result = call_user_func_array($action, $args);
            } else {
                return error('请求异常，找不到Action：' . $express);
            }

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 * 模型方法(调用|注册)
 * @param $express
 * @param array|callable $params
 * @return mixed|void
 */
function model($express, $params = [])
{

}

/** Database */

/**
 * @notes 初始化历史记录
 * @author EdwardCho
 */
function _init_mysql_history()
{
    if (!isset($GLOBALS['mysql_history'])) {
        $GLOBALS['mysql_history'] = [];
    }
}

/**
 * @notes 执行记录
 * @param string $sql
 * @return array|mixed
 * @author EdwardCho
 */
function mysql_history($sql = '')
{
    _init_mysql_history();
    if ($sql === '') {
        return $GLOBALS['mysql_history'];
    } else {
        array_unshift($GLOBALS['mysql_history'], $sql);
        return null;
    }
}

/**
 * 返回最近一条的sql
 * @author EdwardCho
 */
function get_last_sql()
{
    _init_mysql_history();
    return isset($GLOBALS['mysql_history'][0]) ? $GLOBALS['mysql_history'][0] : null;
}

/**
 * @notes 获取PDO实例
 * @return PDO
 * @author EdwardCho
 */
function _mysql()
{
    if (!isset($GLOBALS['PDO_MYSQL'])) {
        $config = config('database');
        //mysql连接
        $dsn = sprintf("%s:host=%s;port=%s;dbname=%s",
            $config['type'], $config['host'],
            $config['port'], $config['dbname']
        );
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        //编码
        $pdo->exec(sprintf("set names %s", $config['charset']));
        //持久化连接
        $pdo::ATTR_PERSISTENT;
        $GLOBALS['PDO_MYSQL'] = $pdo;
    }
    return $GLOBALS['PDO_MYSQL'];
}

/**
 * @notes 执行SQL语句
 * @param $sql
 * @return array|int
 */
function _mysql_exec($sql)
{
    try {
        $result = _mysql()->prepare($sql);

        $result->execute();

        $errorInfo = $result->errorInfo();

        if ($errorInfo[2] != null) {
            throw new Exception($errorInfo[2]);
        }
        $result = stripos($sql, 'select') !== false
            ? $result->fetchAll(PDO::FETCH_ASSOC)
            : $result->rowCount();

    } catch (Exception $e) {
        db_error($e->getMessage());
        return false;
    }

    return $result;
}

/**
 * @param $table
 * @param $data
 * @return array|false|int
 * @author EdwardCho
 */
function _mysql_insert($table, $data)
{
    $sql = "INSERT `{$table}`(%s) VALUES(%s)";
    $fields = "";
    $values = "";
    foreach ($data as $k => $v) {
        $fields .= "`$k`,";
        $values .= "'$v',";
    }
    $fields = substr($fields, 0, strlen($fields) - 1);
    $values = substr($values, 0, strlen($values) - 1);
    return db_exec(sprintf($sql, $fields, $values));
}

/**
 * @param $table
 * @param $where
 * @return array|false|int
 * @author EdwardCho
 */
function _mysql_delete($table, $where)
{
    $sql = "DELETE FROM `{$table}` ";
    if (!empty(trim($where))) {
        $sql .= 'WHERE ' . $where;
    }
    return db_exec($sql);
}


/**
 * @param $table
 * @param $data
 * @param $where
 * @return array|false|int
 * @author EdwardCho
 */
function _mysql_update($table, $data, $where)
{
    $sql = "UPDATE `{$table}` SET ";

    foreach ($data as $name => $value) {
        if (is_array($value)) {
            if ($value[0] == 'exp') {
                $sql .= " `{$name}` = {$value[1]},";
            }
        } elseif (is_string($value)) {
            $sql .= " `{$name}` = '{$value}',";
        }
    }
    if (substr($sql, -1, 1) == ',') {
        $sql = substr($sql, 0, strlen($sql) - 1);
    }
    return db_exec($sql . " WHERE 1=1 AND " . $where);
}

/**
 * @param $table
 * @param string $field
 * @param string $where
 * @param string $order
 * @param string $group
 * @param string $limit
 * @param string $having
 * @return array|false|int
 * @author EdwardCho
 */
function _mysql_select($table, $field = '', $where = '', $order = '', $group = '', $limit = '', $having = '')
{
    $sql = "SELECT ";

    $field = empty(trim($field)) ? '*' : $field;
    $sql .= $field;

    $sql .= ' FROM ' . $table;

    if (!empty(trim($where))) {
        $sql .= ' WHERE ' . $where;
    }
    if (!empty(trim($having))) {
        $sql .= ' HAVING ' . $having;
    }
    if (!empty(trim($order))) {
        $sql .= ' ORDER BY ' . $order;
    }
    if (!empty(trim($group))) {
        $sql .= ' GROUP BY ' . $group;
    }

    if (!empty(trim($limit))) {
        $sql .= ' LIMIT ' . $limit;
    }

    return db_exec($sql);
}

function db_error($error = '')
{
    if ($error !== '') {
        $GLOBALS['mysql_error'] = $error;
        return null;
    } else {
        return $GLOBALS['mysql_error'];
    }
}

/**
 * @param $table
 * @param null $prefix
 * @return string
 * @author EdwardCho
 */
function db_table($table, $prefix = null)
{
    if (is_null($prefix)) {
        $prefix = config('database.table_prefix');
    }
    return $prefix . $table;
}

/**
 * @notes 下一次操作为真删
 * @param $table
 * @param null $bool
 * @return mixed|void
 */
function db_true_delete($table, $bool = '')
{
    if (!isset($GLOBALS['MYSQL_TRUE_DELETE'])) {
        $GLOBALS['MYSQL_TRUE_DELETE'] = [];
    }
    if ($bool === '') {
        if (isset($GLOBALS['MYSQL_TRUE_DELETE'][$table])) {
            return $GLOBALS['MYSQL_TRUE_DELETE'][$table] ?: false;
        }
        return !boolval(_db_soft_delete($table));
    } else {
        $GLOBALS['MYSQL_TRUE_DELETE'][$table] = $bool;
    }
}

/**
 * @notes 获取假删除值
 * @param $table
 * @return int
 * @author EdwardCho
 */
function _db_soft_delete_value($table)
{
    $result = null;
    $data = config('database.soft_delete_value');
    if (is_null($data)) {
        _error('软删配置有误');
    }
    if (is_callable($data)) {
        $result = $data();
        if (is_null($result)) {
            _error('软删配置有误');
        }
    }
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (strtolower($key) == strtolower($table)) {
                $result = is_callable($value) ? $value() : $value;
            }
        }
    }
    if (is_null($result)) {
        _error('软删配置有误');
    }
    return $result;
}

/**
 * @notes 软删除
 * @param $table
 * @return array|false
 * @author EdwardCho
 */
function _db_soft_delete($table)
{
    $list = config('database.soft_delete');
    foreach ($list as $k => $v) {
        list($table1, $field) = explode('.', $k);
        if ($table1 == $table) {
            return [$field, $v];
        }
    }
    return false;
}

/**
 * @notes 自动写入时间戳字段值
 * @param $table
 * @param false $insert
 * @return mixed
 * @author EdwardCho
 */
function _db_auto_timestamp($table, $insert = false)
{
    $timestamps = config('database.declare.');
    if (!isset($timestamps[$table])) {
        $data = [];
    } else {
        $data = [];
        if (isset($timestamps[$table]['update_time'])) {
            $timestamps[$table]['update_time'] = time();
        }

        if ($insert) {
            $data[$timestamps[$table]['create_time']] = time();
        }
    }

    return $data;
}

/**
 * @notes 下一次操作自动去除非表内字段，需要查表字段
 * @param $table
 * @param null $bool
 * @return mixed|void
 * @author EdwardCho
 */
function db_allow_fields($table, $bool = null)
{
    if (!isset($GLOBALS['mysql_allow_fields'])) {
        $GLOBALS['mysql_allow_fields'] = [];
    }
    if ($bool === null) {
        return $GLOBALS['mysql_allow_fields'][$table] ?: false;
    } else {
        $GLOBALS['mysql_allow_fields'][$table] = $bool;
    }
}

/**
 * 查询表字段默认值
 * @param $table
 * @param bool $hasAutoincrement 包含自增字段
 * @return false|array
 * @author EdwardCho
 */
function _db_table_default_values($table, $hasAutoincrement = false)
{
    $sql = sprintf('
            SELECT COLUMN_NAME,COLUMN_DEFAULT,DATA_TYPE,IS_NULLABLE,EXTRA,COLUMN_KEY
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = "%s" AND TABLE_SCHEMA = "%s"',
        $table,
        config('database.dbname')
    );
    $fields = db_exec($sql);
    if (is_bool($fields)) {
        return false;
    }
    $fieldValues = [];
    foreach ($fields as $field) {
        $fieldName = $field['COLUMN_NAME'];

        if (!$hasAutoincrement) {
            if (stristr($field['EXTRA'], 'auto_increment')) {
                //属性携带自增长属性，则跳过
                continue;
            }
        }


        if (!config('database.autoCompleteNoNull')) {
            if ($field['IS_NULLABLE'] == 'YES') {
                //允许为空则进行一个初始化复制
                $fieldValues[$fieldName] = null;
                continue;
            }
        }
        if (in_array($field['DATA_TYPE'], ['varchar', 'char', 'text', 'longtext'])) {
            $fieldValues[$fieldName] = '';
        } else {
            if (in_array($field['DATA_TYPE'], ['int', 'tinyint'])) {
                $fieldValues[$fieldName] = 0;
            }
        }
    }
    return $fieldValues;
}

/**
 * @notes 补全插入值
 * @param $table
 * @param $data
 * @return array
 * @author EdwardCho
 */
function _db_complete_insert($table, $data)
{
    if (!config('database.declare.complete_insert')) {
        return $data;
    }
    //非空值
    return array_merge(_db_table_default_values($table), $data);
}

/**
 * @notes 获取表字段
 * @param $table
 * @return false|array
 */
function _db_table_fields($table)
{
    $sql = sprintf('
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = "%s" AND TABLE_SCHEMA = "%s"',
        $table,
        config('database.dbname')
    );
    $result = _mysql_exec($sql);
    if (is_bool($result)) {
        return $result;
    }
    return array_column($result, 'COLUMN_NAME');
}

/**
 * 执行器包装函数
 * @param $sql
 * @return array|false|int|mixed
 * @author EdwardCho
 */
function db_exec($sql)
{
    /** 记录执行的SQL */
    mysql_history($sql);

    _callback('event.before_sql', ['sql' => $sql]);

    $result = _mysql_exec($sql);

    _callback('event.after_sql', ['sql' => $sql, 'result' => $result]);

    if ($result === false) {
        log('run false :' . $sql, 'sql');
    } else {
        //执行日志
        log('run true :' . $sql, 'sql');
    }

    return $result;
}

/**
 * @notes 删除
 * @param $where
 * @param $table
 * @return string
 */
function db_delete($table, $where)
{
    $trueTable = db_table($table);
    return db_true_delete($table) ? _mysql_delete($trueTable, $where) : db_update($table, [_db_soft_delete($table)[0] => _db_soft_delete_value($table)], $where);
}

/**
 * @notes 更新
 * @param $table
 * @param $data
 * @param $where
 * @return string
 * @author EdwardCho
 */
function db_update($table, $data, $where)
{
    $data = array_merge($data, _db_auto_timestamp($table));
    return _mysql_update(db_table($table), $data, $where);
}

/**
 * @notes 插单
 * @param $table
 * @param $data
 * @return string
 */
function db_insert($table, $data)
{

    //自动插入时间戳
    $data = array_merge($data, _db_auto_timestamp($table, true));
    //补全插入
    $data = _db_complete_insert(db_table($table), $data);
    return _mysql_insert(db_table($table), $data);
}

/**
 * @notes 插多条
 * @param $table
 * @param $array
 * @return array|false|int
 */
function db_insert_all($table, $array)
{
    $result = 0;
    foreach ($array as $data) {
        $result += db_insert($table, $data);
    }

    return $result;

}

/**
 * @notes 查
 * @param $table
 * @param $where
 * @param string $field
 * @param string $order
 * @param string $group
 * @param string $limit
 * @param string $having
 * @return array
 * @author EdwardCho
 */
function db_select($table, $where = '', $field = '', $order = '', $group = '', $limit = '', $having = '')
{
    $result = _db_soft_delete($table);
    if (empty(trim($where))) {
        $where = '1=1';
    }
    if ($result) {
        $where .= " AND {$result[0]} = {$result[1]} ";
    }

    $result = _mysql_select(db_table($table), $field, $where, $order, $group, $limit, $having);

    return _db_relation($table, $result);
}

/**
 * @notes 查总
 * @param $table
 * @param $where
 * @param string $count
 * @return string
 * @author EdwardCho
 */
function db_count($table, $where, $count = '*')
{
    $result = db_select($table, $where, "COUNT({$count}) as `hosp_count`", '', '', 1);
    return intval(isset($result[0]['hosp_count']) ? $result[0]['hosp_count'] : 0);
}

/**
 * @notes 查列
 * @param $table
 * @param $where
 * @param $field
 * @return array
 */
function db_column($table, $where, $field)
{
    return array_column(db_select($table, $where, "{$field} as 'hosp_column'") ?: [], 'hosp_column');
}

/**
 * @notes 查单记录字段值
 * @param $table
 * @param $where
 * @param $field
 * @return string
 */
function db_field($table, $where, $field)
{
    $result = db_select($table, $where, "{$field} as `hosp_field`", '', '', 1);
    if (!$result || !is_array($result) || !count($result)) {
        return null;
    }
    return $result[0]['hosp_field'];
}

/**
 * @notes 查单
 * @param $table
 * @param $id
 * @param string $field
 * @param string $pk
 * @return array|false|int
 * @author EdwardCho
 */
function db_get($table, $id, $field = '', $pk = 'id')
{
    $result = db_select($table, "{$pk} = {$id}", $field, '', '', 1);
    if (is_bool($result)) {
        return $result;
    }
    if (is_array($result) && count($result) == 0) {
        return null;
    }

    return $result[0];
}

/**
 * @notes 自动关联查询
 * @param $table
 * @param $data
 * @return mixed
 * @author EdwardCho
 */
function _db_relation($table, $data)
{
    $queryList = config('database.declare..relation');
    if (is_array($queryList) && count($queryList) == 0) {
        return $data;
    }

    $relations = [];
    foreach ($queryList as $name => $value) {
        list($tempTable, $fk) = explode('.', $name);
        if ($table != $tempTable) {
            continue;
        }
        $relations[$fk] = $value;
    }

    if (count($relations) == 0) {
        return $data;
    }
    foreach ($data as &$record) {
        foreach ($relations as $fk => $relation) {
            if (!isset($record[$fk])) {
                continue;
            }
            list($relationTable, $foreignKey) = explode('.', $relation);
            $result = db_select(
                $relationTable,
                "$foreignKey = {$record[$fk]}"
            );
            $record['relation'][$relationTable] = $result;
        }
    }

    return $data;
}

/**
 * @notes Hosp代理函数
 * @param $express
 * @param array $params
 * @return mixed
 */
function hosp($express, $params = [])
{

    _callback('event.before_hosp', [
        'express' => $express,
        'input' => $params
    ]);

    list($sql, $options) = _hosp_resolve($express, $params);
    $result = _hosp_exec($sql, $options);

    _callback('event.after_hosp', [
        'express' => $express,
        'output' => $result
    ]);

    return $result;
}

/**
 * Hosp表达式执行
 * @param string $sql string SQL语句
 * @param array $options 操作指令
 * @return string
 * @author EdwardCho
 */
function _hosp_exec($sql, $options = [])
{
    $result = db_exec($sql);
    if (is_bool($result)) {
        $result = false;
    }
    if (is_numeric($result)) {
        return $result;
    }

    foreach ($options as $option) {
        if ($option == 'count') {
            $result = $result[0]['count'];
            break;
        } elseif ($result == 'one') {
            $result = $result[0];
            break;
        }
    }

    return $result;

}

/**
 * @notes 解析器入口
 * @param string $express
 * @param array $params
 * @return array
 * @example 'select { only[user_id,id,name] none[nick]by[user_id,noned_id,ip] }'
 */
function _hosp_resolve(string $express, array $params = [])
{
    list($temp, $table, $express) = explode('/', $express);
    //拆分get参数
    $index = stripos($express, '?');
    if (is_numeric($index)) {
        parse_str($express, $args);
        $params = array_merge($args, $params);
        $express = substr($express, 0, $index);
    }

    if (!strpos($express, '{')) {
        //非高级表达式使用简易解析器
        $data = _hosp_simple_resolve($express);
    } else {
        $data = _hosp_standard_resolve($express);
    }
    if (!isset($data['option'])) {
        $data['option'] = [];
    }
    $sql = '';
    switch ($data['type']) {
        case 'insert':
            $fields = [];
            $values = [];

            //自动填充缺省字段值
            $defaultValues = _db_table_default_values(db_table($table));
            if (is_array($defaultValues) && count($defaultValues) > 0) {
                foreach ($params as $name => $value) {
                    if (!in_array($name, array_keys($defaultValues))) {
                        unset($params[$name]);
                    }
                }
                $params = array_merge($defaultValues, $params);
            }

            foreach ($params as $name => $value) {
                $fields[] = "`$name`";
                $values[] = "'{$value}'";
            }
            $sql = sprintf(
                'INSERT INTO `%s`(%s) values(%s)',
                db_table($table),
                implode(',', $fields),
                implode(',', $values)
            );
            break;
        case 'delete':
            $softDelete = _db_soft_delete($table);
            if (!$softDelete) {
                $sql = sprintf(
                    'DELETE FROM `%s`',
                    db_table($table)
                );
            } else {
                $sql = sprintf(
                    'UPDATE `%s` SET `%s` = "%s"',
                    db_table($table),
                    $softDelete[0],
                    time()
                );
            }
            if ($data['by']) {
                $sql .= ' WHERE ' . _hosp_replace($data['by'], $params);
            }
            break;
        case 'update':
            $sql = sprintf(
                "UPDATE `%s` SET %s",
                db_table($table),
                _hosp_replace($data['set'], $params)
            );

            if (!empty($data['by'])) {
                $sql .= ' WHERE' . _hosp_replace($data['by'], $params);
            }
            break;
        case 'select':

            $sql = "SELECT %s FROM `%s`";


            //是否设定指定得字段
            $fields = '*';
            if (isset($data['field'])) {
                $fields = $data['field'];
            }

            //标记字段
            if (isset($data['option']) && array_search('COUNT', $data['option'])) {
                $fields = 'COUNT(*) as `count`';
            }
            $sql = sprintf($sql, $fields, db_table($table));


            if (!empty($data['by'])) {
                $by = $data['by'];
                $searches = [];
                $values = [];
                foreach ($params as $name => $value) {
                    $searches[] = '#{' . $name . '}';
                    $values[] = $value;
                }
                $by = str_replace($searches, $values, $by);
                $sql .= ' WHERE' . $by;
            }

            if (!empty($data['order'])) {
                $sql .= ' ORDER BY ' . $data['order'];
            }

            if (!empty($data['group'])) {
                $sql .= ' GROUP BY ' . $data['group'];
            }

            if (isset($data['option']['page'])) {
                list($page, $size) = explode(',', $params[$data['option']['page']]);
                if (is_null($page) || is_null($size)) {
                    _error('分页入参异常');
                }
                $start = $page * $size;
                $sql .= " LIMIT {$start}, $size";
            } elseif (isset($data['option']['limit'])) {
                if (is_null($data['option']['limit'])) {
                    _error('指定记录数入参异常');
                }
                $sql .= " LIMIT {$data['option']['limit']}";
            } elseif (isset($data['option']['one'])) {
                $sql .= " LIMIT 1";
            }

            $sql = _hosp_replace($sql, $params);

            break;
        default :
            _error("hosp表达式无法解析操作类型，表达式：{$express}");
    }

    return [$sql, $data['option'] ?: []];
}

/**
 * 替换变量
 * @param $express
 * @param $data
 * @return string|string[]
 * @author EdwardCho
 */
function _hosp_replace($express, $data)
{
    $search = [];
    $replace = [];
    foreach ($data as $name => $value) {
        $search[] = "#[$name]";
        $replace[] = $value;
    }
    return str_ireplace($search, $replace, $express);
}

/**
 * @notes 简易表达式解析
 * @param string $express
 * @return mixed
 * @example selectById|selectListByUser_idUsername|insert|delete|updateByUserSetName
 */
function _hosp_simple_resolve(string $express)
{
    $data = [];

    //去掉空格
    $expression = str_replace(' ', '', $express);
    //区分每个单词（在大写字母前加一个空格，并转成小写）
    $matches = preg_replace_callback('/[A-Z!]/', function ($matches) {
        return ' ' . strtolower($matches[0]);
    }, $expression);
    $array = explode(' ', $matches);
    if (count($array) == 0) {
        _error("hosp表达式解析异常，表达式：{$express}");
    }

    //类型
    $data['type'] = array_shift($array);

    $mods = ['by', 'only', 'none', 'order', 'group', 'set'];

    if (isset($array[0]) && !in_array(strtolower($array[0]), $mods)) {
        //模式
        $data['option'][strtoupper(array_shift($array))] = true;
    }


    $key = '';
    foreach ($array as $i => $value) {
        $lowerValue = strtolower($value);
        if (in_array($lowerValue, ['page', 'limit'])) {
            $key = $value;
            $data[$key] = $lowerValue;
            continue;
        }
        if (in_array($lowerValue, ['by', 'only', 'none', 'order', 'group', 'set'])) {
            $key = $value;
            $data[$key] = '';
            continue;
        }

        switch ($key) {
            case 'by':
                if (!empty($data[$key])) {
                    $data[$key] .= ' AND';
                }
                $data[$key] .= " `{$value}` = '#[$value]'";
                break;
            case 'only':
                if (!empty($data[$key])) {
                    $data[$key] .= ',';
                }
                $data[$key] .= $value;
                break;
            case 'order':
                if ($value == '!') {
                    continue;
                }
                if ($array[$i - 1] == '!') {
                    $data[$key] .= " {$value} DESC";
                } else {
                    $data[$key] .= " {$value} ASC";
                }
                break;
            case 'group':
                if (!empty($data[$key])) {
                    $data[$key] .= ',';
                }
                $data[$key] .= " {$value}";
                break;
            case 'set':
                if (!empty($data[$key])) {
                    $data[$key] .= ',';
                }
                $data[$key] .= " `{$value}` = '#[$value]'";
                break;
            case 'none':
                $data['option'][$key][] = $value;
                break;
            default :
                $data[$key] = $value;
                break;
        }
    }

    return $data;
}

/**
 * @notes 标准表达式解析
 * @param string $express
 * @return false|array
 */
function _hosp_standard_resolve(string $express)
{

    if (!stripos($express, '{')) {
        return false;
    }

    $data = [];
    //去除空格
    $expression = str_ireplace([' ', "\n", "\r"], '', $express);
    //取出事件名和子表达式
    preg_match('/(.*){(.*?)}/', $expression, $typeAndSubs);
    if (count($typeAndSubs) > 0) {
        array_shift($typeAndSubs);

        if (!stripos($typeAndSubs[0], '[')) {
            $typeAndOption = preg_replace_callback('/[A-Z!]/', function ($matches) {
                return ' ' . strtolower($matches[0]);
            }, array_shift($typeAndSubs));
            list($type, $option) = explode(' ', $typeAndOption);
            $data['type'] = $type;
            if (!empty($option)) {
                $data['option'][$option] = true;
            }
        }
        //子表达式进行分组
        preg_match_all('/(\w.*?)\[(.*?)]/', $typeAndSubs[0], $matches);
        if (count($matches) > 0) {
            //遍历拿出子表达式名和值并进行一一组合
            foreach ($matches[0] as $key => $value) {
                $data[$matches[1][$key]] = $matches[2][$key];
            }
        }
    }

    if (isset($data['by'])) {
        //符号对应的实际字符串
        $symbolHandlers = [
            '(' => '(',
            ')' => ')',
            '' => function ($name, $param) {
                return "`{$name}` = '#[{$param}]'";
            },
            '|' => function ($name, $param) {
                return " OR `{$name}` = '#[{$param}]'";
            },
            '&' => function ($name, $param) {
                return " AND `{$name}` = '#[{$param}]'";
            },
            '!' => function ($name, $param) {
                return " `{$name}` != '#[{$param}]' ";

            },
            '~' => function ($name, $param) {
                return " `{$name}` LIKE '%#[$param]%' ";
            },
            '%~' => function ($name, $param) {
                return " `{$name}` LIKE '%#[$param]' ";
            },
            '~%' => function ($name, $param) {
                return " `{$name}` LIKE '#[$param]%' ";
            },
            '!~' => function ($name, $param) {
                return " `{$name}` NOT LIKE '%#[$param]%' ";
            },
            '!%~' => function ($name, $param) {
                return " `{$name}` NOT LIKE '%#[$param]' ";
            },
            '!~%' => function ($name, $param) {
                return " `{$name}` NOT LIKE '#[$param]%' ";
            },
            '#' => function ($name, $param) {
                return " `{$name}` IN ({$param})";
            },
            '!#' => function ($name, $param) {
                return " `{$name}` NOT IN ({$param})";
            }
        ];
        $symbols = array_keys($symbolHandlers);
        $regex = '/[' . implode('', $symbols) . ']/';
        if (preg_match($regex, $data['by'])) {
            //区分每个单词（在大写字母前加一个空格，并转成小写）
            $byString = preg_replace_callback($regex, function ($content) {
                return ' ' . $content[0] . ' ';
            }, $data['by']);
            $bys = explode(' ', $byString);
            $where = '';
            for ($i = 0; $i < count($bys); $i++) {
                $by = $bys[$i];
                if ($by === '') {
                    continue;
                }

                if (!is_array($by)) {
                    if (in_array($by, $symbols)) {

                        $handler = $symbolHandlers[$by];
                        if (is_string($handler)) {
                            $where .= $handler;
                            continue;
                        }

                        $bys[$i + 1] = [$by, $bys[$i + 1]];
                        continue;
                    } else {
                        $by = ['', $by];
                    }
                }

                $handler = $symbolHandlers[$by[0]];

                list($field, $param) = explode(':', $by[1]);
                if (empty($param)) {
                    $param = $field;
                }

                $where .= $handler($field, $param);
            }
            $data['by'] = $where;
        } else {
            $bys = explode(',', $data['by']);
            //简易模式
            $where = ' 1=1';
            foreach ($bys as $i => $field) {
                list($field, $param) = explode(':', $field);
                if (empty($param)) {
                    $param = $field;
                }
                if ($field == '!') {
                    $bys[$i + 1] = ['!=', $bys[$i + 1]];
                    continue;
                } else {
                    $bys[$i + 1] = ['=', $bys[$i + 1]];
                    if (!is_array($field)) {
                        $field = ['=', $field];
                    }
                }
                $symbol = $field[0];
                $field = $field[1];

                $where .= " AND {$field} {$symbol} '#[{$param}]'";
            }
            $data['by'] = $where;
        }
    }

    if (isset($data['set'])) {
        $setString = '';
        $sets = explode(',', $data['set']);
        foreach ($sets as $set) {
            list($field, $param) = explode(':', $set);
            if (empty($param)) {
                $param = $field;
            }
            $setString .= ",`{$field}` = '#[{$param}]'";
        }
        if (!empty($setString)) {
            $setString = substr($setString, 1);
        }
        $data['set'] = $setString;
    }

    if (isset($data['none'])) {
        $data['none'] = explode(',', $data['none']);
    }

    if (isset($data['order'])) {
        $orders = explode(',', $data['order']);
        $orderString = '';
        foreach ($orders as $order) {
            if (substr($order, 0, 1) == '!') {
                $order = substr($order, 1);
                $orderString .= " `{$order}` DESC";
            } else {
                $orderString .= " `{$order}` ASC";
            }
        }
        $data['order'] = $orderString;
    }

    if (isset($data['only'])) {
        $data['only'] = explode(',', $data['only']);
    }

    if (isset($data['having'])) {
        $havings = $data['having'];
        //符号对应的实际字符串
        $symbolHandlers = [
            '(' => '(',
            ')' => ')',
            '' => function ($name, $param) {
                return "`{$name}` = '#[{$param}]'";
            },
            '|' => function ($name, $param) {
                return " OR `{$name}` = '#[{$param}]'";
            },
            '&' => function ($name, $param) {
                return " AND `{$name}` = '#[{$param}]'";
            },
            '!' => function ($name, $param) {
                return " `{$name}` != '#[{$param}]' ";

            },
            '~' => function ($name, $param) {
                return " `{$name}` LIKE '%#[$param]%' ";
            },
            '%~' => function ($name, $param) {
                return " `{$name}` LIKE '%#[$param]' ";
            },
            '~%' => function ($name, $param) {
                return " `{$name}` LIKE '#[$param]%' ";
            },
            '!~' => function ($name, $param) {
                return " `{$name}` NOT LIKE '%#[$param]%' ";
            },
            '!%~' => function ($name, $param) {
                return " `{$name}` NOT LIKE '%#[$param]' ";
            },
            '!~%' => function ($name, $param) {
                return " `{$name}` NOT LIKE '#[$param]%' ";
            },
            '#' => function ($name, $param) {
                return " `{$name}` IN ({$param})";
            },
            '!#' => function ($name, $param) {
                return " `{$name}` NOT IN ({$param})";
            }
        ];
        $symbols = array_keys($symbolHandlers);
        $regex = '/[' . implode('', $symbols) . ']/';
        if (preg_match($regex, $havings)) {
            //区分每个单词（在大写字母前加一个空格，并转成小写）
            $havingString = preg_replace_callback($regex, function ($content) {
                return ' ' . $content[0] . ' ';
            }, $havings);
            $havings = explode(' ', $havingString);
            $where = '';
            for ($i = 0; $i < count($havings); $i++) {
                $having = $havings[$i];
                if ($having === '') {
                    continue;
                }

                if (!is_array($having)) {
                    if (in_array($having, $symbols)) {

                        $handler = $symbolHandlers[$having];
                        if (is_string($handler)) {
                            $where .= $handler;
                            continue;
                        }

                        $havings[$i + 1] = [$having, $havings[$i + 1]];
                        continue;
                    } else {
                        $having = ['', $having];
                    }
                }

                $handler = $symbolHandlers[$having[0]];

                list($field, $param) = explode(':', $having[1]);
                if (empty($param)) {
                    $param = $field;
                }

                $where .= $handler($field, $param);
            }
            $data['having'] = $where;
        } else {
            //区分每个单词（在大写字母前加一个空格，并转成小写）
            $havingString = preg_replace_callback('/[A-Z!]/', function ($matches) {
                return ' ' . strtolower($matches[0]);
            }, $data['having']);
            $havings = explode(' ', $havingString);

            //简易模式
            $where = ' 1=1';
            for ($i = 0; $i < count($havings); $i++) {
                $field = $havings[$i];
                if ($field == '!') {
                    $havings[$i + 1] = ['!=', $havings[$i + 1]];
                    continue;
                } else {
                    $havings[$i + 1] = ['=', $havings[$i + 1]];
                    if (!is_array($field)) {
                        $field = ['=', $field];
                    }
                }
                $symbol = $field[0];
                $field = $field[1];

                $where .= " AND {$field} {$symbol} '#[{$field}]'";
            }
            $data['having'] = $where;
        }
    }

    if (isset($data['limit'])) {
        $content = '';
        $limits = explode(',', $data['limit']);
        foreach ($limits as $limit) {
            if (empty($limit)) {
                continue;
            }
            $content .= ",#[$limit]";
        }
        if (!empty($content)) {
            $content = substr($content, 1);
        }
        $data['limit'] = $content;
    }

    return $data;
}

/**
 * 分页工具
 * @param null $total
 * @param null $page
 * @param null $size
 * @param int $pageNumber 显示最大页数
 * @return mixed
 * @author EdwardCho
 */
function paging($total = null, $page = null, $size = null, $pageNumber = 5)
{
    if (is_null($total)) {
        if (!isset($GLOBALS['paging'])) {
            _error('请先配置分页信息');
        }
        return $GLOBALS['paging'];
    }
    $totalPage = ceil($total / $size);

    $median = floor($pageNumber / 2);

    if ($totalPage <= $median) {
        $start = 1;
        $end = $totalPage;
    } elseif ($page >= $totalPage - $median) {
        $start = max($totalPage - $pageNumber, 1);
        $end = $totalPage;
    } else {
        $start = $page - $median;
        $end = $page + $median;
    }
    $GLOBALS['paging'] = [
        'total' => $total,
        'total_page' => $totalPage,
        'page' => $page,
        'size' => $size,
        'first' => $page > 1,
        'final' => $page < $totalPage,
        'prev' => $page > 1,
        'next' => $totalPage > $page,
        'list' => range($start, $end),
    ];
}
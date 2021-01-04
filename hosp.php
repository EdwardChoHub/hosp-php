<?php

namespace hosp;

use Exception;
use PDO;
use ReflectionFunction;

_init();

/**
 * @notes 应用配置
 */
config('app', [
    'extra_custom' => '/custom.php',
    //调试模式，会检测配置信息
    'debug' => true,
    //完全自定义action 不使用预设action 关闭后预设api无法使用
    'custom_action' => false,
    /** 请求Token验证 */
    'token' => [
        /** 开关 */
        'switch' => true,
        /** 生成器 */
        'generator' => function () {
            return md5(rand(0, 123));
        }
    ],
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
    'table_prefix' => 'hisi_',
    //打开得编码形式
    'charset' => 'utf-8',
    //软删除状态(配置后将不会拿出假删除数据)，使用删除时如果存在假删除状态默认使用假删除
    'soft_delete' => [
        'table_name.field' => 'soft_delete_value'
    ],
    /** 自动事件 */
    'auto_event' => [
        /** 自动填充字段去除null */
        'no_null' => true,
        /** 自动补全插入(插入时参数补全系统自动插入默认值) */
        'complete_insert' => true,
        /** 自动插入时间戳 */
        'timestamp' => [
            'user' => [
                'create_time' => 'ctime',
                'update_time' => 'mtime',
            ],
        ],
        //自动关联查询，设定关联后会自动查询或者遵守格式(user_id为用户表的ID字段，user_ids则为用户表的ID集合，值格式为(1,2,3,4))
        'query' => [
            //源ID(主键) => [外键1 => 关系1, 外键2 => 关系2]
            'user.role_id' => 'role.id',
        ],
    ],
]);

/**
 * @notes 配置路由
 */
config('router', [
    '/index/index' => function () {
        echo '欢迎使用Hosp';
        die();
    },
]);

/**
 * @notes 角色权限声明，1则为全部权限，0或[]组则无任何权限
 */
config('authority', [
    /**
     * @notes 角色权限控制(支持静态权限和动态权限)
     * @return array
     */
    'access' => function(){
        //用户自定义权限
    },
    /**
     * @notes 不拦截列表
     * @return array
     */
    'except' => function(){

    },
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
    'before_authority' => function ($action) {
    },
    'after_authority' => function ($action, $result) {
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

/**
 * @notes 自定义钩子
 */
config('hook', []);

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
        $config[$indexList[$count - 1]] = $value;
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
    session_start();
    if (empty($name)) {
        return $_SESSION['apino'];
    }

    if ($_SESSION['apino'] == null) {
        $_SESSION['apino'] = [];
    }
    //使用索引去操作
    $indexList = explode('.', $name);
    if ($value !== '') {
        $config = &$_SESSION['apino'];
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
        $config = $_SESSION['apino'];
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

        //拿到URL中的入参信息
        $index = stripos($url, "?");
        $index = $index > 0 ? $index + 1 : strlen($url);
        $string = substr($url, $index);
        $urlParams = [];
        foreach (explode("&", $string) as $item) {
            list($name, $value) = explode("=", $item);
            if (isset($name) && isset($value)) {
                $urlParams[$name] = $value;
            }
        }

        //入参参数优先级 json > post > get > urlParams(url中的参数，处理加密url中没有被php解析到的参数)
        $json = json_decode(file_get_contents("php://input"), true);
        $json = empty($json) ? [] : $json;

        $GLOBALS['REQUEST_PARAMS'] = array_merge($urlParams, $_GET, $_POST, $json);
    }

    if ($name == null) {
        return $this->params;
    }
    return isset($GLOBALS['REQUEST_PARAMS'][$name]) ? $GLOBALS['REQUEST_PARAMS'][$name] : $default;
}

/**
 * @notes 上传文件保存
 * @param $name string 文件名
 * @param $destination string 目标文件路径
 * @return bool
 * @author EdwardCho
 */
function upload_file_move($name, $destination)
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
function upload_file_valid($name, $exts = [], $maxSize = null)
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
        return false("上传文件大小为{$this->size}，已超过$maxSize");
    }
    return true();
}


/**
 * url组装，优先使用注册的url
 * @param $url
 * @param array $params 拼接参数
 * @return string
 */
function url($url, $params = [])
{
    $urls = config('url');
    foreach ($urls as $name => $value) {
        if ($url == $name) {
            $url = $value;
            break;
        }
    }

    if (substr($url, 0, 1) != '/') {
        $url = '/' . $url;
    }

    $url = '/' . FILE_NAME . '.php' . $url;

    if (count($params)) {
        $url .= array_to_get_string($params);
    }

    return urlencode($url);
}

/**
 * 数组转入参字符串
 * @param $params array
 * @return string
 */
function array_to_get_string($params){
    $paramsStr = '';
    foreach ($params as $name => $value) {
        $paramsStr .= "&{$name}={$value}";
    }
    $paramsStr = '?' . substr($paramsStr, 1);
    return $paramsStr;
}

/** Response */
/**
 * @notes action 出参包装
 * @param string $msg
 * @param int $code
 * @return false|string|null
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
 * @return false|string|null
 */
function success($data = [], $code = 0, $msg = '')
{
    return _response([
        'data' => $data,
        'code' => $code,
        'msg' => $msg
    ], 'json');
}

/**
 * @notes 输出JSON对象
 * @param $code
 * @param $data
 * @param $msg
 * @return void
 */
function json($code = 0, $data = [], $msg = '')
{
    $object = [
        'code' => $code,
        'data' => $data,
        'msg' => $msg
    ];
    //日志钩子
    hook('complete', [
        'response' => $object
    ]);

    exit(json_encode($object, JSON_UNESCAPED_UNICODE));
}

/**
 * @notes 处理响应内容
 * @param $data
 * @param $type
 * @return string
 */
function _response($data, $type = 'json')
{
    $response = null;
    switch ($type) {
        case 'json':
            $response = json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
        case 'html':
            $response = $data;
            break;
    }
    if (is_null($response)) {
        _error('无响应内容');
    }
    return $response;
}


/** User */
/**
 * @notes ID
 * @param string $id
 * @return int|string|void
 */
function user_id($id = '')
{
    return session('user_id', $id);
}

/**
 * @notes 登录
 * @param $id
 * @param mixed $role
 */
function user_login($id, $role = '')
{
    user_id($id);
    user_role($role);
}

/**
 * @notes 退出登录
 */
function user_logout()
{
    user_id(null);
    user_role(null);
    _user_authority(false);
    session_destroy();
}

/**
 * @notes 角色
 * @param string|int $role
 * @return string|void
 */
function user_role($role = '')
{
    return $role === '' ?session('user_role') : session('user_role', $role);
}

/**
 * @notes 权限
 * @param array|bool|mixed $authority
 * @return array|bool|void
 */
function _user_authority($authority = [])
{
    return session('user_authority', $authority);
}

/**
 * @notes 全部权限(true)、无任何权限(false)、指定权限列表(array)
 * @param $url
 * @return bool
 */
function _user_access($url)
{
    return is_bool(_user_authority()) ? _user_authority() : in_array($url, _user_authority());
}

/**
 * @notes 用户信息同步
 */
function _user_sync()
{
    $access = config('authority.access');
    $except = config('authority.except');

    session('user_authority', array_merge(
        is_callable($access) ? $access() : [],
        is_callable($except) ? $except() : []
    ));
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

    /** 是否事引用（判断依据非第一个文件） */
    define('IS_REQUIRE', get_required_files()[0] != __FILE__);

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

    //表关联常量
    define('ONE_TO_ONE', 11); //一对一
    define('ONE_TO_MANY', 13); //一对多

    /** 日志类型(等级) */
    define('LOG_NORMAL', 1);
    define('LOG_WARN', 3);
    define('LOG_ERROR', 5);

    /** 响应code值 */
    /** 权限不足 */
    define('HTTP_CODE_FORBIDDEN', 403);
    /** 服务器错误 */
    define('HTTP_CODE_ERROR', 500);
    /** 失败 */
    define('HTTP_CODE_FAIL', 1);
    /** 成功 */
    define('HTTP_CODE_SUCCESS', 0);


    if (config('app.debug')) {
        ini_set("display__errors", "On");
        error_reporting(E_ALL);
        //配置检查员(app.debug开启后才会执行)
        _check_config();
    }

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
    /** 引入外部用户自定义代码（建议使用，方便后期升级） */
    $extraCustomFile = APP_PATH . DS . config('app.extra_custom');
    if (file_exists($extraCustomFile)) {
        require_once $extraCustomFile;
    }

    if (!IS_REQUIRE) {
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

        /** 事件钩子 */
        _callback('event.after_router');

        /** 事件钩子 */
        _callback('event.before_authority');

        /** 同步当前用户角色权限信息 */
        _user_sync();

        /** 权限判断 */
        $result = _user_access($route);

        /** 事件钩子 */
        _callback('event.after_authority');

        if (!$result) {
            _end(error('当前用户没有该权限'));
        }

        $params = input();

        /** 入参校验器 */
        list($success, $result) = validate($route, $params);
        if (!$success) {
            _end(error($result));
        }

        _callback('event.before_action');

        /** 调用相应方法 */
        $object = action($url, $params);

        _callback('event.after_action');

        /** 响应内容 */
        _end($object);

    }

}

/**
 * 结束执行
 * @param $content
 * @author EdwardCho
 */
function _end($content)
{
    _callback('event.before_complete', [
        'response' => $content
    ]);

    echo $content;

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
    list($controller, $action) = explode('/', $url);

    //URL格式检验
    if (empty($controller) || empty($action)) {
        _error("无法正常解析URL，检查URL格式是否正确");
    }

    return [$controller, $action];
}

/**
 * @notes 路由器转换
 * @param $route
 * @return array
 */
function _router($route)
{
    list($currentController, $currentMethod) = $route;
    if(is_null($currentController)){
        $currentController = config('app.default_controller');
    }
    if(is_null($currentMethod)){
        $currentMethod = config('app.default_method');
    }
    if($currentMethod === ''){
        $currentMethod = '*';
    }

    $route = config('route');

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
        $flag3 = $currentController == '*' && $currentMethod == $beforeMethod;
        //第四种全模糊
        $flag4 = $currentController == '*' && $currentMethod == '*';

        if ($flag1 || $flag2 || $flag3 || $flag4) {
            //路由转发支持转发到其他http网站
            if (stripos($after, 'http') > -1) {
                //将入参填充到url中
                header('Location:' . $after . array_to_get_string(input()));
                die();
            }

            //去除开头的/
            list($temp, $controller, $method) = explode('/', $after);
            unset($temp);

            if(empty($controller)){
                $controller = $currentController;
            }
            if(empty($method)){
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
    _end($content);
}

/**
 * @notes 检查配置项(仅开发模式检查)
 * @author EdwardCho
 *
 */
function _check_config()
{
    $ruleList = [
        ['类型名', '地址', '判断函数', '错误提示'],
        ['开发模式', 'app.debug', 'is_bool', '开发模式必须为布尔型'],
    ];

    //除去第一行标题
    array_shift($ruleList);

    foreach ($ruleList as $rule) {
        if (!call_user_func($ruleList[2], config($rule[1]))) {
            _error($rule[3]);
        }
    }
}

/**
 * @notes 函数调用
 * @param $name string 函数配置名
 * @param array $args
 */
function _callback($name, $args = [])
{
    try {
        $callback = config($name);
        if (is_callable($callback)) {
            $reflectFunction = new ReflectionFunction($callback);
            $reflectFunction->invokeArgs($args);
        }
    } catch (Exception $e) {
        _error("调用{$name}函数异常");
    }

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
 * 钩子调用和注册
 * @param $name
 * @param $args callback|array
 */
function hook($name, $args = [])
{
    if (is_callable($args)) {
        config("hook.{$name}", $args);
    } else {
        try {
            $function = config("hook.{$name}");
            $rf = new ReflectionFunction($function);
            $rf->invokeArgs($args);
        } catch (Exception $e) {
            _error("调用{$name}钩子异常");
        }
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
            _callback('event.before_action');

            $params = array_merge(input(), is_array($params) ? $params : []);
            $action = config("action.$express");
            $result = null;
            if (is_null($action)) {
                $result = model($express, $params);
            } elseif (is_callable($action)) {
                $result = $action($params);
            } else {
                _error('请求异常找不到action');
            }

            _callback('event.after_action');

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
 * @notes 获取PDO实例
 * @return PDO
 * @author EdwardCho
 */
function _mysql()
{
    if (!defined('PDO_MYSQL')) {
        //mysql连接
        $dsn = sprintf("%s:host=%s;port=%s;dbname=%s",
            $this->type, $this->host,
            $this->port, $this->dbname
        );
        $pdo = new PDO($dsn, $this->username, $this->password);
        //编码
        $pdo->exec(sprintf("set names %s", $this->charset));
        //持久化连接
        $pdo::ATTR_PERSISTENT;
        define('PDO_MYSQL', $pdo);
    }
    return PDO_MYSQL;
}

/**
 * @notes 执行SQL语句
 * @param $sql
 * @return array|int
 */
function mysql_exec($sql)
{

    try {
        $this->connect();

        $result = _mysql()->prepare($sql);

        $result->execute();

        $errorInfo = $result->errorInfo();

        if ($errorInfo[2] != null) {
            _error($errorInfo[2]);
        }

        $result = stripos($this->sql, 'select')
            ? $result->fetchAll(PDO::FETCH_ASSOC)
            : $result->rowCount();

        //钩子
        hook('sql', [
            'sql' => $this->sql
        ]);

        //执行日志
        log('run true :' . $this->sql, 'sql');

    } catch (Exception $e) {
        log('run false :' . $this->sql, 'sql');
        _error($e->getMessage());
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
function mysql_insert($table, $data)
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
    return mysql_exec(sprintf($sql, $fields, $values));
}

/**
 * @param $table
 * @param $where
 * @return array|false|int
 * @author EdwardCho
 */
function mysql_delete($table, $where)
{
    return mysql_exec(sprintf('DELETE FROM `%s` WHERE 1=1 %s', $table, $where));
}

/**
 * @param $table
 * @param $data
 * @param $where
 * @return array|false|int
 * @author EdwardCho
 */
function mysql_update($table, $data, $where)
{
    $sql = "UPDATE `{$table}` SET ";

    foreach ($data as $name => $value) {
        $sql .= " `{$name}` = '{$value}',";
    }
    if (substr($sql, -1, 1) == ',') {
        $sql = substr($sql, 0, strlen($sql) - 1);
    }
    return mysql_exec($sql . " WHERE 1=1 AND " . $where);
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
function mysql_select($table, $field = '', $where = '', $order = '', $group = '', $limit = '', $having = '')
{
    $sql = "SELECT ";

    $field = empty($field) ? '*' : $field;
    $sql .= $field;

    $sql .= ' FROM ' . $table;

    if (!empty($where)) {
        $sql .= ' WHERE ' . $where;
    }
    if (!empty($order)) {
        $sql .= ' ORDER BY ' . $order;
    }
    if (!empty($group)) {
        $sql .= ' GROUP BY ' . $group;
    }
    if (!empty($limit)) {
        $sql .= ' LIMIT ' . $limit;
    }
    if (!empty($limit)) {
        $sql .= ' HAVING ' . $having;
    }

    return mysql_exec($sql);
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
function db_select($table, $where, $field = '', $order = '', $group = '', $limit = '', $having = '')
{

    $result = _db_soft_delete($table);
    if ($result) {
        $where .= " AND {$result[0]} = {$result[1]} ";
    }

    $result = mysql_select($table, $field, $where, $order, $group, $limit, $having);

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
    return db_true_delete() ? mysql_delete($table, $where) : db_update($table, _db_soft_delete($table), $where);
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
    return mysql_update($table, $data, $where);
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
    $data = array_merge($data, _db_auto_timestamp($table,true));
    //补全插入
    $data = _db_complete_insert($table, $data);
    return mysql_insert($table, $data);
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
    return db_select($table, "{$pk} = {$id}", $field, '', '', 1);
}

/**
 * @notes 查总
 * @param $table
 * @param $where
 * @param string $count
 * @return string
 * @author EdwardCho
 */
function db_count($table, $where, $count = '')
{
    return db_select($table, $where, "COUNT({$count}) as `count`", '', '', 1)['count'] ?: 0;
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
    return array_column(db_select($table, $where, $field) ?: [], $field);
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
 * @notes 查单记录字段值
 * @param $table
 * @param $where
 * @param $field
 * @return string
 */
function db_field($table, $where, $field)
{
    $result = db_select($table, $where, $field, '', '', 1);
    return $result ? $result[$field] : null;
}

/**
 * @notes 获取表字段
 * @param $table
 * @return array
 */
function _db_table_fileds($table)
{
    $sql = sprintf('
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = "%s" AND TABLE_SCHEMA = "%s"',
        $table,
        config('database.dbname')
    );
    return array_column(mysql_exec($sql), 'COLUMN_NAME');
}

/**
 * @notes 下一次操作自动去除非表内字段，需要查表字段
 * @param null $bool
 * @return mixed|void
 * @author EdwardCho
 */
function db_allow_fields($bool = null)
{
    if ($bool === null) {
        $result = $GLOBALS['mysql_allow_fields'];
        unset($GLOBALS['mysql_allow_fields']);
        return $result;
    } else {
        $GLOBALS['mysql_allow_fields'] = $bool;
    }
}

/**
 * @notes 下一次操作为真删
 * @param null $bool
 * @return mixed|void
 */
function db_true_delete($bool = null)
{
    if ($bool === null) {
        $result = $GLOBALS['mysql_true_delete'];
        unset($GLOBALS['mysql_true_delete']);
        return $result;
    } else {
        $GLOBALS['mysql_true_delete'] = $bool;
    }
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
 * @notes 补全插入值
 * @param $table
 * @param $data
 * @return array
 * @author EdwardCho
 */
function _db_complete_insert($table, $data)
{
    //非空值
    return array_merge(_db_table_default_values($table), $data);
}

/**
 * 查询表字段默认值
 * @param $table
 * @return array
 * @author EdwardCho
 */
function _db_table_default_values($table)
{
    $sql = sprintf('
            SELECT COLUMN_NAME,COLUMN_DEFAULT,DATA_TYPE,IS_NULLABLE,EXTRA,COLUMN_KEY
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = "%s" AND TABLE_SCHEMA = "%s"',
        $table,
        config('database.dbname')
    );
    $fields = mysql_exec($sql);
    $fieldValues = [];
    foreach ($fields as $field) {
        $fieldName = $field['COLUMN_NAME'];

        if (stristr($field['EXTRA'], 'auto_increment')) {
            //属性携带自增长属性，则跳过
            continue;
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
 * @notes 自动写入时间戳字段值
 * @param $table
 * @param false $insert
 * @return mixed
 * @author EdwardCho
 */
function _db_auto_timestamp($table, $insert = false)
{
    $data = [];
    $config = config('database.auto.timestamp');
    if ($config['switch']) {
        $time = $config['generator'] ?: time();

        if ($insert) {
            $field = $config['createTime'][$table];
            !empty($field) && $data[$field] = $time;
        }

        $field = $config['updateTime'][$table];
        !empty($field) && $data[$field] = $time;
    }
    return $data;
}

/**
 * @notes 自动关联查询
 * @param $table
 * @param $data
 * @return mixed
 * @author EdwardCho
 */
function _db_auto_query($table, $data)
{
    $queryList = config('database.auto_event.query');
    if (is_array($queryList) && count($queryList) == 0) {
        return $data;
    }

    $relations = [];
    foreach ($queryList as $name => $value) {
        list($tempTable, $fk) = explode('.', $name);
        if($table != $tempTable){
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
            list($relationTable, $foreignKey) = $relation;
            $result = mysql_select(
                config('database.prefix') . $relationTable,
                '',
                "$foreignKey = {$record[$fk]}",
                '',
                '',
                $relation == ONE_TO_ONE ? 1 : ''
            );
            $record['relation'][$relationTable] = $relation == ONE_TO_ONE ? $result[0] : $result;
        }
    }
    unset($record);
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

    callback('event.before_hosp');

    list($sql, $options) = _hosp_resolve($express, $params);
    $result = _hosp_exec($sql, $options);

    callback('event.after_hosp');

    return $result;
}

/**
 * Hosp表达式执行
 * @param $sql string SQL语句
 * @param array $options 操作指令
 * @return string
 * @author EdwardCho
 */
function _hosp_exec($sql, $options = [])
{

    $result = mysql_exec($sql);
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
 * @param $expression string
 * @param array $params
 * @return array
 * @example 'select { only[user_id,id,name] none[nick]by[user_id,noned_id,ip] }'
 */
function _hosp_resolve($expression, $params)
{
    list($temp, $table, $expression) = explode('/', $expression);
    unset($temp);


    if (!strpos($expression, '{')) {
        //非高级表达式使用简易解析器
        $data = _hosp_simple_resolve($expression);
    } else {
        $data = _hosp_standard_resolve($expression);
    }
    $sql = '';
    switch ($data['option']['type']) {
        case 'insert':
            $fields = [];
            $values = [];
            foreach ($params as $name => $value) {
                $fields[] = $name;
                $values[] = $value;
            }
            $sql = sprintf(
                'INSERT INTO __TABLE__(%s) values(%s)',
                implode(',', $fields),
                implode(',', $values)
            );
            break;
        case 'delete':
            if ($this->trueDelete) {
                $sql = sprintf('DELETE FROM `%s`', $this->trueTableName);
            } else {
                $result = $this->getTableFalseDeleteField();
                if ($result) {
                    $sql = sprintf('UPDATE `%s` SET `%s` = "%s"', $this->trueTableName, $result[0], $result[1]);
                } else {
                    $sql = sprintf('DELETE FROM `%s`', $this->trueTableName);
                }
            }

            $where = $this->apinoExpression->by();
            if ($where) {
                $sql .= ' WHERE' . $where;
            }
            break;
        case 'update':
            $sql = sprintf("UPDATE `__TABLE__` SET %s", $data['set']);

            if (!empty($data['where'])) {
                $searches = [];
                $values = [];
                foreach ($params as $name => $value) {
                    $searches[] = '#{' . $name . '}';
                    $values[] = $value;
                }
                $data['by'] = str_replace($searches, $values, $data['by']);
                $sql .= ' WHERE' . $data['by'];
            }
            break;
        case 'select':

            $sql = "SELECT %s FROM `__TABLE__`";

            //是否设定指定得字段
            $fields = $data['field'];
            if (empty($fields)) {
                $fields = '*';
            }
            //标记字段
            if (array_search('COUNT', $data['option'])) {
                $fields = 'COUNT(*) as `count`';
            }
            $sql = sprintf($sql, $fields);

            $by = $data['by'];
            if (!empty($by)) {
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


            break;
        default :
            _error("hosp表达式无法解析操作类型，表达式：{$expression}");
    }

    $sql = str_replace('__TABLE__', $table, $sql);

    return [$sql, $data['option']];
}

/**
 * @notes 简易表达式解析
 * @param string $express
 * @return mixed
 * @example selectById|selectListByUser_idUsername|insert|delete|updateByUserSetName
 */
function _hosp_simple_resolve($express)
{
    $data = [];

    //去掉空格
    $expression = str_replace(' ', '', $express);
    //区分每个单词（在大写字母前加一个空格，并转成小写）
    $matches = preg_replace_callback('/[A-Z]/', function ($matches) {
        return ' ' . strtolower($matches[0]);
    }, $expression);
    $array = explode(' ', $matches);

    if (count($array) == 0) {
        _error("hosp表达式解析异常，表达式：{$express}");
    }

    //类型
    $data['type'] = array_shift($array);

    if (isset($array[0]) && strtolower($array[0]) != 'by') {
        //模式
        $data['option'][strtoupper(array_shift($array))] = true;
    }

    $key = '';
    foreach ($array as $value) {
        $lowerValue = strtolower($value);
        if (in_array($lowerValue, ['page', 'limit'])) {
            $key = $value;
            $data[$key] = $lowerValue;
        } elseif (in_array($lowerValue, ['by', 'only', 'none', 'order', 'group', 'set'])) {
            $key = $value;
            $data[$key] = [];
        } else {
            switch ($lowerValue) {
                case 'by':
                    if (!empty($data[$key])) {
                        $data[$key] .= ' AND ';
                    }
                    $data[$key] .= " `{$value}` = #[$value]";
                    break;
                case 'only':
                    if (!empty($data[$key])) {
                        $data[$key] .= ',';
                    }
                    $data[$key] .= $value;
                    break;
                case 'none':
                    $data['option'][$key][] = $value;
                    break;
                case 'order':
                    $type = substr($value, 1, 1) == '!' ? 'DESC' : 'ASC';
                    $data[$key] .= " {$value} {$type}";
                    break;
                case 'group':
                    $data[$key] .= " {$value}";
                    break;
                case 'set':
                    if (!empty($data[$key])) {
                        $data[$key] .= ',';
                    }
                    $data[$key] .= " `{$value}` = #[$value]";
                    break;
            }
        }
    }

    return $data;
}

/**
 * @notes 标准表达式解析
 * @param string $express
 * @return string
 */
function _hosp_standard_resolve($express)
{
    $data = [];
    $subExpressList = [];
    //去除空格
    $expression = str_ireplace(' ', '', $express);
    //取出事件名和子表达式
    preg_match('/(\w.*?){(.*?)}/', $expression, $typeAndSubs);
    if (count($typeAndSubs) > 0) {
        //事件名操作类型
        preg_match('/(.*[a-z])[A-Z]/', $typeAndSubs[1], $matches);
        if (count($matches) > 0) {
            $data['type'] = $matches[1];
        }
        //事件名模式
        preg_match('/([A-Z].*)/', $typeAndSubs[1], $matches);
        if (count($matches) > 0) {
            $data['option'][strtolower($matches[1])] = true;
        }
        //子表达式进行分组
        preg_match_all('/(\w.*?)\[(.*?)]/', $typeAndSubs[2], $matches);
        if (count($matches) > 0) {
            //遍历拿出子表达式名和值并进行一一组合
            foreach ($matches[0] as $key => $value) {
                $subExpressList[$matches[1][$key]] = $matches[2][$key];
            }
        }
    }


    if (gettype($value) != 'array' && preg_match('/[(|&~%]/', $value)) {
        //高级模式
        return $this->highResolverBy($value, $params);
    } else {
        //简易模式
        $str = ' 1=1';
        foreach ($value as $name => $valueName) {
            $symbol = '=';
            if (strpos($name, '!') !== false) {
                $symbol = '!=';
                $name = substr($name, 0, strlen($name) - 1);
            }
            $str .= " AND `{$name}` {$symbol} '{$params[$valueName]}'";
        }
        return $str;
    }
}

function hosp_high_by($expression, $params)
{
    $symbols = [
        '|' => ' OR ',
        '&' => ' AND ',
        '(' => '(',
        ')' => ')',
    ];
    $templatesHandler = [
        '' => function ($name, $valueName, $params) {
            $template = ' #name# = "#value#" ';
            $valueName = $valueName ?: $name;
            $value = $params[$valueName];
            return str_replace(['#name#', '#value#'], [$name, $value], $template);
        },
        '!' => function ($name, $valueName, $params) {
            $template = ' #name# != "#value#" ';
            $valueName = $valueName ?: $name;
            $value = $params[$valueName];
            return str_replace(['#name#', '#value#'], [$name, $value], $template);
        },
        '~' => function ($name, $valueName, $params) {
            $template = ' #name# LIKE "#value#" ';
            $realName = str_replace('%', '', $name);
            $valueName = $valueName ?: $realName;
            $value = str_replace($realName, $params[$valueName], $name);
            return str_replace(['#name#', '#value#'], [$realName, $value], $template);
        },
        '!~' => function ($name, $valueName, $params) {
            $template = ' #name# NOT LIKE "#value#" ';
            $realName = str_replace('%', '', $name);
            $valueName = $valueName ?: $realName;
            $value = str_replace($realName, $params[$valueName], $name);
            return str_replace(['#name#', '#value#'], [$realName, $value], $template);
        },
    ];

    $newExp = '';

    //使用模式
    $prefix = '';
    //单词
    $word = '';
    for ($i = 0; $i < strlen($expression); $i++) {
        $char = substr($expression, $i, 1);

        if (in_array($char, array_keys($symbols))) {
            //处理mod和word
            //获取格式
            if (!empty($word)) {
                $handler = $templatesHandler[$prefix];
                list($name, $valueName) = explode(':', $word);
                $newExp .= $handler($name, $valueName, $params);
            }
            $newExp .= $symbols[$char];
            $word = '';
            $prefix = '';
        } else {
            if (in_array($char, array_keys($templatesHandler))) {
                $prefix .= $char;
            } else {
                $word .= $char;
            }
        }
    }
    return $newExp;
}
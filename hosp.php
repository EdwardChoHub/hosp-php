<?php
/**
 *
 *     /\      |—————|   ———————    |\    |     |———————|
 *    /  \     |     |      |       | \   |     |       |
 *   /____\    |—————|      |       |  \  |     |       |
 *  /      \   |            |       |   \ |     |       |
 * /        \  |         ———————    |    \|     |———————|
 *
 *  版本：V0.2.200903
 *  作者：邱岷岷
 *  时间：2020/9/4
 */

//框架初始化
frameInitialize();

/**数据库配置*/
config('database', [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'tran_group',
    'username' => 'root',
    'password' => 'root',
    //表名前缀
    'tableNamePrefix' => 'hisi_',
    //打开得编码形式
    'charset' => 'utf-8',
    //自动关联查询，设定关联后会自动查询或者遵守格式(user_id为用户表的ID字段，user_ids则为用户表的ID集合，值格式为(1,2,3,4))
    'autoQuery' => [
        //源ID(主键) => [外键1 => 关系1, 外键2 => 关系2]
        'hisi_user.role_id' => [
            'hisi_role.id' => ONE_TO_ONE,
        ],
    ],
    //自动补全插入(插入时参数补全系统自动插入默认值)
    'autoCompleteInsert' => false,
    //自动填充字段不会存在null
    'autoCompleteNoNull' => true,
    //自动插入时间戳
    'autoTimestamp' => [
        'switch' => true,
        //创建时间字段
        'createTime' => [
            'hisi_user' => 'ctime',
            'hisi_news' => 'ctime'
        ],
        //修改时间字段
        'updateTime' => [
            'hisi_user' => 'mtime',
        ],
        //生成值
        'generator' => time()
    ],
    //假删除状态(配置后将不会拿出假删除数据)，使用删除时如果存在假删除状态默认使用假删除
    'falseDeleteValue' => [
        'hisi_user.isuse' => 2,
    ],
]);
/**
 * 应用配置
 */
config('app', [
    //调试模式，会检测配置信息
    'debug' => true,
    //入参规定
    'request' => [
        //分页规定
        'page' => [
            //页数
            'page' => 'page',
            //页记录数
            'size' => 'size',
        ]
    ],
    //出参格式
    'response' => [
        //放在data下面
        'page' => [
            'total' => 'total',
            'list' => 'list',
        ]
    ],
    //apino格式请求真删锁(仅对apino格式请求有效)
    'trueDeleteLock' => [
        //开关
        'switch' => true,
        //密钥
        'key' => '123123123213',
        //入参值
        'param' => 'trueDeleteKey'
    ],
    //用户表配置
    'user' => [
        /**
         * 用户ID关联数据库对应得表字段
         */
        'id' => 'hisi_front_user.id',
        /**
         * @return mixed 返回关联具体权限的字段值
         */
        'role' => function ($userId) {
        },
        /**权限*/
        'auth' => [
            /**
             * 模式
             */
            'mode' => CONFIG,
            /**
             * 权限来源
             */
            'source' => function ($roleId) {
                $role = dao('/user/select{by[role_id]}', ['role_id' => $roleId]);
                $auth = dao('/auth/select{by[role_id]}', ['role_id' => $role['role_id']]);
                return array_column($auth, 'url');
            },
            /**
             * 忽略
             */
            'except' => [
                '/util/verifyCode',
                '/util/token'
            ],
        ],
    ]
]);
/**
 * 邮箱配置
 */
config('smtp', [
    'server' => "",
    'port' => 22,
    'user' => '933',
    'pass' => '2123',
    'debug' => false,
    'type' => 'HTML',
]);
/**
 * 路由转发
 */
config('route', [

]);
/**
 * 角色权限声明，1则为全部权限，0或[]组则无任何权限
 * 默认角色 GUEST, GUEST为常量
 * 该操作为配置式权限申明，
 */
config('authority', [
    ADMIN => 1,
    //无登录用户权限信息
    GUEST => [],
    //用户自定义权限
]);
/**
 * 入参校验器(根据路由进行校验，位于路由转换后)
 * require(必填)|int(整形)|float(浮点行)|mobile(手机号)|email(邮箱)|具体值
 */
config('validate', [

]);
/**
 * 入参转换器
 */
config('input_converter', [

]);

/**
 * 控制器
 */
config('controller', [
    '/user/info' => function () {
    }

]);
/**
 * 服务者
 */
config('service', [

]);
/**
 * 持久化层(自定义执行得sql语句)
 */
config('dao', [

]);
/**
 * 系统预设API
 */
config('api', [
    'util' => [
        'upload' => [
            'image' => [
                'fileParam' => 'img',
                'maxSize' => 4096,
                'dir' => '/upload/img',
            ],
            'php' => [
                'fileParam' => 'file',
                'maxSize' => 4096,
                'dir' => '/upload/file',
            ],
        ],
        //获取验证码，使用sence指定获取验证码，在对应接口会自动验证验证码是否正确
        'verifyCode' => [
            'param' => 'code',
            'sence' => [
                '/user/register' => VerifyCode::asImage(4, 200, 100),
                '/user/login' => VerifyCode::asEmail(4, '验证码信息', '您的验证码位#code#,请5分钟内使用！', '#code#'),
                '/user/reg' => VerifyCode::asMobile(4, ''),
            ],
        ],
        //获取token接口
        'token' => [
            //开关，开启强制所有请求携带TOKEN值验证(每次请求前都需要先获取token
            //)
            'switch' => true,
            //入参名
            'param' => 'token',
            //获取TOKEN时间间隔
            'interval' => 1,
            //生成函数
            'generator' => function () {
            },
        ],
    ],
    'user' => [
        //注册接口
        'register' => [
            //注册用户名入参名
            "username" => "username",
            //注册密码入参名
            "password" => "password",
            //注册确认密码入参名
            "confirmPassword" => "confirmPassword",
            //密码加密储存回调
            'passwordEncode' => function ($password) {
                return md5($password);
            },
            /**
             * 登陆前处理
             * @return Result 返回数组则通过，返回false则登录失败
             */
            'safety' => function ($params) {
            },
            /**
             * 成功回调
             */
            'success' => function ($user) {
            },
            /**
             * 失败回调
             */
            'error' => function ($username) {
            },
        ],
        //登陆api
        'login' => [
            //登陆账户名入参名
            'username' => 'name',
            //登陆密码入参名
            'password' => 'pass',
            //密码加密回调
            'passwordEncode' => function ($password) {
//                return md5($password);
            },
            /**
             * 自定义安全回调（安全码校验）
             * @return bool|null
             * @var $params array 入参信息
             */
            'safety' => function ($params) {
            },
            /**
             * 登录成功回调(日志记录)
             */
            'success' => function ($user) {
            },
            /**
             * 登录失败回调，可用户记录用户登录日志
             * @var $username string 登录用户名
             */
            'error' => function ($username) {
            },
        ],
        /**
         * 登出回调(日志或者删除当前在线用户信息的操作)
         */
        'logout' => function ($user_id) {
        },
        /**
         * 验证是否登陆（追加判断默认已有判断session的用户ID）
         */
        'checkLogin' => function ($userId) {
        },
    ],
]);
/**
 * URL引用
 */
config('ref', [

]);
/**
 * 内置钩子
 */
config('hook', [
    /**
     * 执行于App初始化之前
     */
    'initialize' => function () {
//        //SESSION会话切换
//        $sessionId = input('PHPSESSID');
//        if (!empty($sessionId)) {
//            session_write_close();
//            session_id($sessionId);
//            session_start();
//        }
    },
    /**
     * 执行于路由解析前
     * @var $router Router
     */
    'router' => function (Router $router) {
    },
    /**
     * 路由权限拦截结果
     * @var $router Router 当前路由
     * @var $userId mixed|null 当前登录用户，null说明未登录
     * @var $userAuth array 用户权限
     * @var $result bool 拦截结果
     */
    'authority' => function (Router $router, ?string $userId, array $userAuth, bool $result) {
    },
    /**
     * 执行控制器方法时自动调用
     * @var $name string 控制器名
     * @var $request array 入参信息
     * @var $response mixed 出参结果
     */
    'controller' => function ($name, $request, $response) {
    },
    /**
     * 执行服务函数时自动调用
     * @var $name string 服务名
     * @var $request array 入参信息
     * @var $response mixed 出参结果
     */
    'service' => function ($name, $request, $response) {
    },
    /**
     * 执行dao函数时调用
     * @var $name string DAO名
     * @var $request array 入参信息
     * @var $response mixed 出参结果
     */
    'dao' => function ($name, $request, $response) {
    },
    /**
     * 执行apino执行器钩子
     */
    'apino' => function ($name, $request, $response) {
    },
    /**
     * 执行sql时调用
     * @var $sql string 执行SQL
     */
    'sql' => function (string $sql) {
    },
    /**
     * 全部完成后调用
     * @var Response mixed 输出结果
     */
    'complete' => function ($response) {
    },
]);
/**
 * php注册处理器
 */
config('handler', [
    'set_exception_handler' => function ($e) {
    },
    'set_error_handler' => function ($errno, $errorStr, $errorFile, $errorLine, $errorContext) {
    },
    'register_shutdown_function' => function () {
    },
    //短信发送函数
    'sms_sender' => function ($args) {
    }
]);
/**
 * 用户手动引入文件
 */
config('require_file', [
]);

/**
 * 钩子
 * Created by PhpStorm.
 * @param $name
 * @param $args
 * @throws ReflectionException
 * @author QiuMinMin
 * Date: 2020/8/1 19:58
 */
function hook($name, $args = [])
{
    $function = config("hook.{$name}");
    $rf = new ReflectionFunction($function);
    $rf->invokeArgs($args);
}

/**
 * 获取或配置参数
 * @param $name
 * @param string $value
 * @return array|mixed
 */
function config(string $name = '', $value = '')
{
    if (empty($name)) {
        return $GLOBALS['apino'];
    }

    if ($GLOBALS['apino'] == null) {
        $GLOBALS['apino'] = [];
    }
    //使用索引去操作
    $indexList = explode('.', $name);
    if ($value !== '') {
        $config = &$GLOBALS['apino'];
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
        $config = $GLOBALS['apino'];
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

/**
 * 错误处理
 * Created by PhpStorm.
 * @param $content
 * @author QiuMinMin
 * Date: 2020/5/17 7:47
 */
function error(string $content)
{
    Log::file($content, 'error', true);
    Response::error($content);
    die();
}

/**
 * 执行器
 * Created by PhpStorm.
 * @param string $expression
 * @param array $params
 * @return array|int|mixed|null
 * @throws ReflectionException
 * @author QiuMinMin
 * Date: 2020/6/27 14:37
 */
function apino(string $expression, $params = [])
{
    list($temp, $controller, $action) = explode('/', $expression);
    unset($temp);

    $response = model($controller)->apino($action, $params);

    hook(__FUNCTION__, [
        'name' => $expression,
        'request' => $params,
        'response' => $response
    ]);
    return $response;
}

/**
 * 表达式执行器，用户设定值优先于入参值
 * Created by PhpStorm.
 * @param string $expression
 * @param mixed ...$params
 * @return array|int|mixed|null
 * @throws ReflectionException
 * @author QiuMinMin
 * Date: 2020/5/17 7:00
 */
function controller(string $expression, array $params = [])
{
    $params = array_merge(Request::instance()->param(), Is::array($params) ? $params : []);
    $closure = config("controller.$expression");
    $response = Is::closure($closure) ? $closure($params) : service($expression, $params);
    //日志钩子
    hook(__FUNCTION__, [
        'name' => $expression,
        'request' => $params,
        'response' => $response
    ]);
    return $response;
}

/**
 * 服务者函数(调用服务里的方法)
 * Created by PhpStorm.
 * @param string $expression
 * @param $params array 指定参数
 * @return mixed
 * @throws ReflectionException
 * @author QiuMinMin
 * Date: 2020/5/19 8:55
 */
function service(string $expression, array $params = [])
{
    $closure = config("service.$expression");
    $response = Is::closure($closure) ? $closure($params) : dao($expression, $params);
    //日志钩子
    hook(__FUNCTION__, [
        'name' => $expression,
        'request' => $params,
        'response' => $response
    ]);
    return $response;
}

/**
 * 持久化层
 * Created by PhpStorm.
 * @param $expression string
 * @param $params array
 * @return mixed
 * @throws ReflectionException
 * @author QiuMinMin
 * Date: 2020/5/22 15:05
 */
function dao(string $expression, array $params = [])
{
    $value = config("dao.$expression");

    if (empty($value)) {
        $response = apino($expression, $params);
    } else {
        //直接SQL语句
        //替换SQL表达式里的动态属性值
        foreach ($params as $k => $v) {
            $value = str_ireplace("#{$k}#", $v, $value);
        }
        //直接执行sql语句
        $response = model()->sql($value)->execute();
    }

    //日志钩子
    hook(__FUNCTION__, [
        'name' => $expression,
        'request' => $params,
        'response' => $response
    ]);

    return $response;
}

/**
 * 模型函数
 * Created by PhpStorm.
 * @param string|null $table
 * @return Model
 * @author QiuMinMin
 * Date: 2020/6/27 20:48
 */
function model(string $table = null)
{
    return new Model($table);
}

/**
 * 综合打印
 * Created by PhpStorm.
 * @param $data
 * @author QiuMinMin
 * Date: 2020/6/1 11:40
 */
function dump($data)
{
    if (gettype($data) == 'object') {
        var_export($data);
    } else {
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

/**
 * sql拼装类功能入口
 * Created by PhpStorm.
 * @param null $tableName
 * @return Sql
 * @author QiuMinMin
 * Date: 2020/7/30 23:51
 */
function sql($tableName = null)
{
    return new Sql($tableName);
}

/**
 * 校验参数
 * Created by PhpStorm.
 * @param array $array
 * @return bool|mixed
 * @author QiuMinMin
 * Date: 2020/6/1 16:18
 * @example [[bool, errorInfo],[]...]
 */
function check(array $array)
{
    if (!count($array)) {
        return true;
    }
    foreach ($array as $item) {
        if ($item[0]) {
            return $item[1];
        }
    }
    return true;
}

/**
 * 入参辅助函数
 * Created by PhpStorm.
 * @param null $name
 * @param null $default
 * @return array|mixed|null
 * @author QiuMinMin
 * Date: 2020/6/19 0:09
 */
function input($name = null, $default = null)
{
    return Request::instance()->param($name, $default);
}

/**
 * 校验器
 * Created by PhpStorm.
 * @param $url
 * @param $params
 * @return array
 * @author QiuMinMin
 * Date: 2020/6/1 16:17
 */
function validate(string $url, array $params)
{
    $data = config("validate.$url");

    if (!empty($data)) {
        foreach ($data as $name => $rules) {
            //获取设定的和法值
            $values = array_diff_key($rules, VALIDATE_TYPE_ARRAY);

            if (!isset($params[$name]) && in_array(REQ, $rules)) {
                return Result::error($name . '参数是必填的');
            } else {
                if (count($values) > 0 && !in_array($params[$name], $values)) {
                    return Result::error($name . '参数值非法');
                } elseif (isset($params[$name])) {
                    $exp = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

                    $result = check([
                        [intval($params[$name]) != $params[$name] && in_array('int', $rules), '整数'],
                        [!is_numeric($params[$name]) && in_array('float', $rules), '小数'],
                        [preg_match('/^1\d{10}$/', $params[$name]) && in_array('mobile', $rules), '手机号'],
                        [preg_match($exp, $params[$name]) && in_array('email', $rules), '邮箱'],
                    ]);
                    if ($result !== true) {
                        return Result::error($name . '参数必须是' . $result);
                    }
                }
            }
        }
    }

    return Result::success($params);
}

/**
 * 入参转换器
 * Created by PhpStorm.
 * @param $url
 * @param $params
 * @return mixed
 * @author QiuMinMin
 * Date: 2020/6/1 13:58
 */
function inputConverter(string $url, array $params)
{
    $closure = config("input_converter.$url");
    return Is::closure($closure) ? $closure($params) : $params;
}

/**
 * 日志函数
 * Created by PhpStorm.
 * @param string $content
 * @param string $fileName
 * @param bool $dailyWrite
 * @param bool $writeTime
 * @author QiuMinMin
 * Date: 2020/5/30 16:06
 */
function log_file(string $content, string $fileName, bool $dailyWrite = true, bool $writeTime = true)
{
    Log::file($content, $fileName, $dailyWrite, $writeTime);
}

/**
 * 内嵌类
 */
class_alias(Embed::class, 'Em');

//只有api请求才会执行程序
IS_AJAX && Core::run();

/**
 * 系统初始化函数，请勿随意修改初始化函数内容
 */
function frameInitialize()
{
    define('APINO_START', microtime(true));

    //有使用composer则进行加载composer加载工具
    if(file_exists(__DIR__ . '/composer.json')){
        if(file_exists(__DIR__ . '/vendor/autoload.php')){
            require_once __DIR__ . '/vendor/autoload.php';
        }
    }

    /**
     * 定义系统常量
     * Created by PhpStorm.
     * @author QiuMinMin
     * Date: 2020/8/3 0:46
     */
    function defines()
    {
        //关闭严格模式
        ini_set("display_errors", 0);

        //定义常量
        //判断是否为api请求(是否是第一个引入文件)
        define('IS_AJAX', get_required_files()[0] == __FILE__ ? 1 : 0);

        //简化系统内置文件路径分隔符
        define("DS", DIRECTORY_SEPARATOR);

        //路径
        define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
        define("UPLOAD_PATH", ROOT_PATH . "/upload");

        define("LOG_PATH", ROOT_PATH . "/log");

        //validate正则类型
        define('REQ', 'require');
        define('INT', 'int');
        define('FLOAT', 'float');
        define('MOBILE', 'mobile');
        define('EMAIL', 'email');
        define('ARR', 'array');

        //validate支持的所有正则
        define('VALIDATE_TYPE_ARRAY', [REQ, INT, FLOAT, MOBILE, EMAIL, ARR]);

        //系统默认角色
        //访客(未登录权限)
        define('GUEST', 'guest');
        //超级管理员(一般拥有所有权限)
        define('ADMIN', 'admin');


        //真实SQL前缀
        define('SQL_RAW_PREFIX', random_bytes(10));

        //获取权限模式
        define('CONFIG', 'config');
        define('DATABASE', 'database');
        define('NONE', 'none');

        //表关联常量
        define('ONE_TO_ONE', 11); //一对一
        define('ONE_TO_MANY', 13); //一对多
    }

    defines();

    /**
     * 日志驱动
     * Class Log
     */
    class Log
    {

        /**
         * 日志文件夹
         * @var mixed
         */
        protected static $dir = LOG_PATH;

        /**
         * 日志驱动
         * @param $str
         * @param string $file_name
         * @param bool $daily_write
         * @param bool $writeTime
         */
        public static function file($str, $file_name, $daily_write = false, $writeTime = true)
        {
            $file_path = self::$dir . "/";
            if (!is_dir($file_path)) {
                mkdir($file_path, 0777, true);
            }
            $file_path .= $file_name ? $file_name . ($daily_write ? '.' . date(
                        'Y-m-d',
                        time()
                    ) : '') . '.log' : 'event.' . date('Y-m-d', time()) . '.log';
            $file = fopen($file_path, 'a');
            if ($writeTime) {
                fwrite($file, date('Y-m-d H:i:s', time()) . "\n" . $str . "\n");
            } else {
                fwrite($file, $str . "\n");
            }
            fclose($file);
        }
    }

    /**
     * 出参包装特性
     * Trait Result
     */
    trait Result
    {
        public static function success($data = [])
        {
            return [true, $data];
        }

        public static function error($msg = '')
        {
            return [false, $msg];
        }
    }

    /**
     * 日志特性
     */
    trait LogTrait
    {
        /**
         * 日志文件名
         * @var string
         */
        protected $log_file = "log";

        /**
         * 错误处理函数，返回值只会是False
         * @param $info string
         * @param bool $daily_write
         * @param bool $writeTime
         */
        protected function log_file($info, $daily_write = true, $writeTime = true)
        {
            Log::file($info, $this->log_file, $daily_write, $writeTime);
        }

    }

    /**
     * 错误特性
     * Trait ErrorTrait
     */
    trait ErrorTrait
    {
        protected $error;

        public function error($error = null)
        {
            if ($error === null) {
                return $this->error;
            }

            $this->error = $error;
            return false;
        }
    }

    /**
     * 邮箱驱动
     * Class Smtp
     */
    class Smtp
    {
        var $smtp_port;
        var $time_out;
        var $host_name;
        var $log_file;
        var $relay_host;
        var $debug;
        var $auth;
        var $user;
        var $pass;
        var $sock;

        function __construct($relay_host, $smtp_port, $auth, $user, $pass)
        {
            $this->debug = false;
            $this->smtp_port = $smtp_port;
            $this->relay_host = $relay_host;
            $this->time_out = 3600;
            $this->auth = $auth;
            $this->user = $user;
            $this->pass = $pass;
            $this->host_name = "localhost";
            $this->log_file = "";
            $this->sock = false;
        }

        function sendmail($to, $from, $subject, $body, $mailtype, $cc = "", $bcc = "", $additional_headers = "")
        {
            $mail_from = $this->get_address($this->strip_comment($from));
            $body = pereg_replace("(^|(\r\n))(\.)", "\1.\3", $body);
            $header = "MIME-Version:1.0\r\n";
            if ($mailtype == "HTML") {
                $header .= "Content-Type:text/html\r\n";
            }
            $header .= "To: " . $to . "\r\n";
            if ($cc != "") {
                $header .= "Cc: " . $cc . "\r\n";
            }
            $header .= "From: $from<" . $from . ">\r\n";
            $header .= "Subject: " . $subject . "\r\n";
            $header .= $additional_headers;
            $header .= "Date: " . date("r") . "\r\n";
            $header .= "X-Mailer:By Redhat (PHP/" . phpversion() . ")\r\n";
            list($msec, $sec) = explode(" ", microtime());
            $header .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $mail_from . ">\r\n";
            $TO = explode(",", $this->strip_comment($to));
            if ($cc != "") {
                $TO = array_merge($TO, explode(",", $this->strip_comment($cc)));
            }
            if ($bcc != "") {
                $TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
            }
            $sent = true;
            foreach ($TO as $rcpt_to) {
                $rcpt_to = $this->get_address($rcpt_to);
                if (!$this->smtp_sockopen($rcpt_to)) {
                    $this->log_write("Error: Cannot send email to " . $rcpt_to . "\n");
                    $sent = false;
                    continue;
                }
                if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
                    $this->log_write("E-mail has been sent to <" . $rcpt_to . ">\n");
                } else {
                    $this->log_write("Error: Cannot send email to <" . $rcpt_to . ">\n");
                    $sent = false;
                }
                fclose($this->sock);
                $this->log_write("Disconnected from remote host\n");
            }
            return $sent;
        }

        function smtp_send($helo, $from, $to, $header, $body = "")
        {
            if (!$this->smtp_putcmd("HELO", $helo)) {
                return $this->smtp_error("sending HELO command");
            }
            if ($this->auth) {
                if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) {
                    return $this->smtp_error("sending HELO command");
                }
                if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
                    return $this->smtp_error("sending HELO command");
                }
            }
            if (!$this->smtp_putcmd("MAIL", "FROM:<" . $from . ">")) {
                return $this->smtp_error("sending MAIL FROM command");
            }
            if (!$this->smtp_putcmd("RCPT", "TO:<" . $to . ">")) {
                return $this->smtp_error("sending RCPT TO command");
            }
            if (!$this->smtp_putcmd("DATA")) {
                return $this->smtp_error("sending DATA command");
            }
            if (!$this->smtp_message($header, $body)) {
                return $this->smtp_error("sending message");
            }
            if (!$this->smtp_eom()) {
                return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
            }
            if (!$this->smtp_putcmd("QUIT")) {
                return $this->smtp_error("sending QUIT command");
            }
            return true;
        }

        function smtp_sockopen($address)
        {
            if ($this->relay_host == "") {
                return $this->smtp_sockopen_mx($address);
            } else {
                return $this->smtp_sockopen_relay();
            }
        }

        function smtp_sockopen_relay()
        {
            $this->log_write("Trying to " . $this->relay_host . ":" . $this->smtp_port . "\n");
            $this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
            if (!($this->sock && $this->smtp_ok())) {
                $this->log_write("Error: Cannot connenct to relay host " . $this->relay_host . "\n");
                $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
                return false;
            }
            $this->log_write("Connected to relay host " . $this->relay_host . "\n");
            return true;
        }

        function smtp_sockopen_mx($address)
        {
            $domain = pereg_replace("^.+@([^@]+)$", "\1", $address);
            if (!@getmxrr($domain, $MXHOSTS)) {
                $this->log_write("Error: Cannot resolve MX \"" . $domain . "\"\n");
                return false;
            }
            foreach ($MXHOSTS as $host) {
                $this->log_write("Trying to " . $host . ":" . $this->smtp_port . "\n");
                $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
                if (!($this->sock && $this->smtp_ok())) {
                    $this->log_write("Warning: Cannot connect to mx host " . $host . "\n");
                    $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
                    continue;
                }
                $this->log_write("Connected to mx host " . $host . "\n");
                return true;
            }
            $this->log_write("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");
            return false;
        }

        function smtp_message($header, $body)
        {
            fputs($this->sock, $header . "\r\n" . $body);
            $this->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n> " . $body . "\n> "));
            return true;
        }

        function smtp_eom()
        {
            fputs($this->sock, "\r\n.\r\n");
            $this->smtp_debug(". [EOM]\n");
            return $this->smtp_ok();
        }

        function smtp_ok()
        {
            $response = str_replace("\r\n", "", fgets($this->sock, 512));
            $this->smtp_debug($response . "\n");
            if (!pereg("^[23]", $response)) {
                fputs($this->sock, "QUIT\r\n");
                fgets($this->sock, 512);
                $this->log_write("Error: Remote host returned \"" . $response . "\"\n");
                return false;
            }
            return true;
        }

        function smtp_putcmd($cmd, $arg = "")
        {
            if ($arg != "") {
                if ($cmd == "") {
                    $cmd = $arg;
                } else {
                    $cmd = $cmd . " " . $arg;
                }
            }
            fputs($this->sock, $cmd . "\r\n");
            $this->smtp_debug("> " . $cmd . "\n");
            return $this->smtp_ok();
        }

        function smtp_error($string)
        {
            $this->log_write("Error: Error occurred while " . $string . ".\n");
            return false;
        }

        function log_write($message)
        {
            $this->smtp_debug($message);
            if ($this->log_file == "") {
                return true;
            }
            $message = date("M d H:i:s ") . get_current_user() . "[" . getmypid() . "]: " . $message;
            if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) {
                $this->smtp_debug("Warning: Cannot open log file \"" . $this->log_file . "\"\n");
                return false;
            }
            flock($fp, LOCK_EX);
            fputs($fp, $message);
            fclose($fp);
            return true;
        }

        function strip_comment($address)
        {
            $comment = "\([^()]*\)";
            while (preg_match($comment, $address)) {
                $address = preg_replace($comment, "", $address);
            }
            return $address;
        }

        function get_address($address)
        {
            $address = preg_replace("([ \t\r\n])+", "", $address);
            $address = preg_replace("^.*<(.+)>.*$", "\1", $address);
            return $address;
        }

        function smtp_debug($message)
        {
            if ($this->debug) {
                echo $message;
            }
        }
    }

    /**
     * 请求信息封装类
     * Class Request
     */
    class Request
    {
        private static $request = null;

        /**
         * @var array 入参属性列表
         */
        private $params = [];

        private $loadParams = false;

        private function __construct()
        {
        }

        private function __clone()
        {
        }

        public static function instance()
        {
            if (empty(self::$request)) {
                self::$request = new self();
            }
            return self::$request;
        }

        public function isPost()
        {
            return $_SERVER["REQUEST_METHOD"] == "POST";
        }

        public function isGet()
        {
            return $_SERVER['REQUEST_METHOD'] == "GET";
        }

        /**
         * 上传文件
         * @param $name
         * @param null $destination
         * @return File
         */
        public function file($name, $destination = null)
        {
            $file = new File($name);
            if ($destination) {
                $file->move($destination);
            }
            return $file;
        }

        /**
         * 获取入参
         * @param $name
         * @param null $default
         * @return mixed|null
         */
        public function param($name = null, $default = null)
        {
            //懒加载，使用到入参信息才会去获取
            $this->initParams();
            if ($name == null) {
                return $this->params;
            }
            return isset($this->params[$name]) ? $this->params[$name] : $default;
        }

        /**
         * 初始化缓存汇总入参信息
         */
        private function initParams()
        {
            if ($this->loadParams) {
                return;
            }
            $this->loadParams = true;

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
            unset($_GET['_url']);
            $this->params = array_merge($urlParams, $_GET, $_POST, $json);
        }

        /**
         * @param $name string 魔术方法，自动获取入参值
         * @return array|mixed|null
         */
        public function __get($name)
        {
            return $this->param($name);
        }

        /**
         * 不存在的方法自动获取入参信息，并且传入的一个参数为无入参时的默认参数
         * @param $name
         * @param $arguments
         * @return array|mixed|null
         */
        public function __call($name, $arguments)
        {
            return $this->param($name, $arguments[0]);
        }

    }

    /**
     * 出参封装类
     * Class Response
     */
    class Response
    {
        private static function returnAjax($code, $data, $msg)
        {
            echo json_encode(
                [
                    'code' => $code,
                    'data' => $data,
                    'msg' => $msg
                ],
                JSON_UNESCAPED_UNICODE
            );
            die();
        }

        /**
         * 发送相应内容
         * @param $object mixed|array
         */
        public static function send($object = [])
        {
            //日志钩子
            hook('complete', [
                'response' => $object
            ]);

            self::returnAjax(0, $object, '');
        }

        public static function error($msg = '')
        {
            self::returnAjax(0, [], $msg);
        }
    }

    /**
     * Class Mysql 数据库驱动
     */
    class Mysql
    {
        use LogTrait;

        //类型
        protected $type;
        //主机名
        protected $host;
        //端口
        protected $port;
        //数据库名
        protected $dbname;
        //用户名
        protected $username;
        //密码
        protected $password;
        //编码
        protected $charset;

        protected $sql;

        /**
         * @var PDO
         */
        protected static $pdo;

        public function __construct()
        {
            $config = config('database');
            $this->type = $config['type'];
            $this->host = $config['host'];
            $this->port = $config['port'];
            $this->dbname = $config['dbname'];
            $this->username = $config['username'];
            $this->password = $config['password'];
            $this->charset = $config['charset'];

            $this->connect();
        }

        /**
         * 开启事务
         */
        public function startTrans()
        {
            self::$pdo->beginTransaction();
            return $this;
        }

        /**
         * 回滚
         */
        public function rollback()
        {
            self::$pdo->rollBack();
            return $this;
        }

        /**
         * 提交事务
         */
        public function commit()
        {
            self::$pdo->commit();
        }

        /**
         * 返回增删改影响记录数|查结果集
         * Created by PhpStorm.
         * @return array|int
         * @author QiuMinMin
         * Date: 2020/8/1 23:11
         */
        public function execute()
        {
            $result = self::$pdo->prepare($this->sql);

            $result->execute();

            $errorInfo = $result->errorInfo();

            if ($errorInfo[2] != null) {
                error($errorInfo[2]);
            }

            return stripos($this->sql, 'select')
                ? $result->fetchAll(PDO::FETCH_ASSOC)
                : $result->rowCount();
        }

        /**
         * 获取|修改SQL
         * Created by PhpStorm.
         * @param null|string $sql
         * @return mixed
         * @author QiuMinMin
         * Date: 2020/8/1 23:03
         */
        public function sql($sql = null)
        {
            if (empty($sql)) {
                return $this->sql;
            }

            $this->sql = $sql;
            return $this;
        }

        /**
         * 连接数据库
         * @return mixed
         */
        protected function connect()
        {
            if (empty(self::$pdo)) {
                //mysql连接
                $dsn = sprintf("%s:host=%s;port=%s;dbname=%s",
                    $this->type, $this->host,
                    $this->port, $this->dbname
                );
                self::$pdo = new PDO($dsn, $this->username, $this->password);
                //编码
                self::$pdo->exec(sprintf("set names %s", $this->charset));
                //持久化连接
                self::$pdo::ATTR_PERSISTENT;
            }

            return $this;
        }

        /**
         * 关闭数据库
         */
        protected function close()
        {
            self::$pdo = null;
        }

        public function __destruct()
        {
            // TODO: Implement __destruct() method.
            $this->close();
        }
    }

    /**
     * SQL组装增强特性
     * Trait DbPlusTrait
     */
    trait DbPlusTrait
    {
        /**
         * @var string 表全名
         */
        protected $trueTableName;
        /**
         * @var bool 删除非表字段
         */
        protected $allowFields = true;
        /**
         * @var bool 自动关联查询(预设定)
         */
        protected $autoQuery = true;
        /**
         * @var bool 是否真删（false如果有假删优先假删除true强制真删除）
         */
        protected $trueDelete = false;

        public function allowFields($bool = true)
        {
            $this->allowFields = $bool;
            return $this;
        }

        public function autoQuery($bool = true)
        {
            $this->autoQuery = $bool;
            return $this;
        }

        public function trueDelete($bool = true)
        {
            $this->trueDelete = $bool;
            return $this;
        }

        public function table($trueTableName = '')
        {
            if ($trueTableName === '') {
                return $this->trueTableName;
            } else {
                $this->trueTableName = $trueTableName;
                return $this;
            }
        }

        /**
         * 自动补全查询信息
         * Created by PhpStorm.
         * @param $data
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/31 9:04
         */
        protected function autoCompleteInsert(array $data)
        {
            return array_merge($this->getTableDefaultValues(), $data);
        }

        /**
         * 自动添加得条件(不能查询出假删除条件)，仅限于select
         * Created by PhpStorm.
         * @param string $where
         * @return string
         * @author QiuMinMin
         * Date: 2020/5/31 8:45
         */
        protected function appendWhereByFalseDelete($where = '')
        {
            $config = config('database.falseDeleteValue');
            foreach ($config as $k => $v) {
                list($table, $field) = explode('.', $k);
                if ($table == $this->trueTableName) {
                    $where .= " AND `$field` = $v ";
                    break;
                }
            }
            return $where;
        }

        /**
         * 获取表假删除字段和值
         * Created by PhpStorm.
         * @param $trueTableName
         * @return array|bool
         * @author QiuMinMin
         * Date: 2020/8/3 23:16
         */
        protected function getTableFalseDeleteField()
        {
            $list = config('database.falseDeleteValue');
            foreach ($list as $k => $v) {
                list($table, $field) = explode('.', $k);
                if ($table == $this->trueTableName) {
                    return [$field, $v];
                }
            }
            return false;
        }

        /**
         * 自动添加时间戳
         * Created by PhpStorm.
         * @param $data array  数据
         * @param bool $insert
         * @return mixed
         * @author QiuMinMin
         * Date: 2020/5/31 8:49
         */
        protected function autoTimestampWithUpdate($data, $insert = false)
        {
            $config = config('database.autoTimestamp');
            if ($config['switch']) {
                $time = $config['generator'] ?: time();

                if ($insert) {
                    $field = $config['createTime'][$this->trueTableName];
                    !empty($field) && $data[$field] = $time;
                }

                $field = $config['updateTime'][$this->trueTableName];
                !empty($field) && $data[$field] = $time;
            }
            return $data;
        }

        /**
         * 获取字段默认值
         * Created by PhpStorm.
         * @return array
         * @author QiuMinMin
         * Date: 2020/6/20 1:50
         */
        protected function getTableDefaultValues()
        {
            $mysql = new Mysql();
            $sql = sprintf('
            SELECT COLUMN_NAME,COLUMN_DEFAULT,DATA_TYPE,IS_NULLABLE,EXTRA,COLUMN_KEY
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = "%s" AND TABLE_SCHEMA = "%s"',
                $this->trueTableName,
                config('database.dbname')
            );
            $fields = $mysql->sql($sql)->execute();
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
         * 真删锁校验
         * Created by PhpStorm.
         * @param $sql
         * @author QiuMinMin
         * Date: 2020/7/30 23:23
         */
        protected function trueDeleteLock($sql)
        {
            if (stristr($sql, 'delete')) {
                //假删锁验证
                $config = config('database.canTrueDelete');
                if ($config['switch']) {
                    $key = input($config['param']);
                    if (empty($key)) {
                        error('真删密钥缺失');
                    }
                    if ($config['key'] != $key) {
                        error('真删密钥不正确');
                    }
                }
            }
        }

        /**
         * 获取表字段信息
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/6/19 9:17
         */
        public function getTableFields()
        {
            $mysql = new Mysql();
            $sql = sprintf('
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = "%s" AND TABLE_SCHEMA = "%s"',
                $this->trueTableName,
                config('database.dbname')
            );
            return array_column($mysql->sql($sql)->execute(), 'COLUMN_NAME');
        }

        /**
         * 数据补充(关联查询数据)
         * Created by PhpStorm.
         * @param $data array
         * @return array
         * @author QiuMinMin
         * Date: 2020/7/31 0:12
         */
        protected function suppleData($data)
        {
            if (!$this->autoQuery) {
                return $data;
            }

            $config = config('database.autoQuery');
            if (Is::array($config) && count($config) == 0) {
                return $data;
            }

            /**
             * @example [内键 => [外表, 外键, 关系], ...]
             */
            $relationDataList = [];

            foreach ($config as $foreignKey => $internalKeys) {
                list($foreignTrueTableName, $foreignKey) = explode('.', $foreignKey);
                foreach ($internalKeys as $internalKey => $relation) {
                    list($trueTableName, $internalKey) = explode('.', $internalKey);
                    if ($this->trueTableName != $trueTableName) {
                        continue;
                    }
                    $relationDataList[$internalKey] = [$foreignTrueTableName, $foreignKey, $relation];
                }
            }
            if (count($relationDataList) == 0) {
                return $data;
            }

            foreach ($data as &$record) {
                foreach ($relationDataList as $field => $relation) {
                    if (!isset($record[$field])) {
                        continue;
                    }
                    list($trueTableName, $foreignKey, $relation) = $relation;
                    $model = new Model();
                    $model->table($trueTableName);
                    $model = $model->and($foreignKey, $record[$field]);
                    $record['relation'][$field] = $relation == ONE_TO_MANY ? $model->select() : $model->get();
                }

                unset($record);
            }
            return $data;
        }
    }

    /**
     * Class Db 拓展执行器
     */
    class Db extends Mysql
    {

        use DbPlusTrait;

        /**
         * @var string 组装where语句
         */
        protected $where = "";

        public function insert($data)
        {
            //自动插入时间戳
            $data = $this->autoTimestampWithUpdate($data, true);
            //自动补全表字段
            $data = $this->autoCompleteInsert($data);

            $sql = "INSERT `{$this->trueTableName}`(%s) VALUES(%s)";
            $fields = "";
            $values = "";

            foreach ($data as $k => $v) {
                $fields .= "`$k`,";
                $values .= "'$v',";
            }
            $fields = substr($fields, 0, strlen($fields) - 1);
            $values = substr($values, 0, strlen($values) - 1);
            return sprintf($sql, $fields, $values);
        }

        public function update($set)
        {
            $set = $this->autoTimestampWithUpdate($set, false);

            $sql = "UPDATE `{$this->trueTableName}` SET ";
            foreach ($set as $k => $v) {
                list($space, $v) = explode(SQL_RAW_PREFIX, $v);
                if (empty($space)) {
                    $sql .= " `$k` = $v,";
                } else {
                    $sql .= " `$k` = '$space',";
                }
            }
            if (substr($sql, -1, 1) == ',') {
                $sql = substr($sql, 0, strlen($sql) - 1);
            }
            return $sql . " WHERE 1=1 " . $this->where;
        }

        public function delete()
        {
            if ($this->trueDelete) {
                return sprintf('DELETE FROM %s WHERE 1=1 %s', $this->trueTableName, $this->where);
            } else {
                $result = $this->getTableFalseDeleteField();
                if ($result) {
                    return sprintf('UPDATE %s SET `%s` = "%s" WHERE 1=1 %s', $this->trueTableName, $result[0],
                        $result[1], $this->where);
                } else {
                    return sprintf('DELETE FROM %s WHERE 1=1 %s', $this->trueTableName, $this->where);
                }
            }
        }

        public function select($fields = "*")
        {
            $result = $this->getTableFalseDeleteField();
            if ($result) {
                $this->where .= " AND {$result[0]} = {$result[1]} ";
            }

            return sprintf(
                'SELECT %s FROM `%s` WHERE 1=1 %s',
                $fields,
                $this->trueTableName,
                $this->where
            );
        }

        public function get($fields = "*")
        {
            $result = $this->getTableFalseDeleteField();
            if ($result) {
                $this->where .= " AND {$result[0]} = {$result[1]} ";
            }

            return sprintf(
                'SELECT %s FROM `%s` WHERE 1=1 %s LIMIT 1',
                $fields,
                $this->trueTableName,
                $this->where
            );
        }

        public function count()
        {
            $result = $this->getTableFalseDeleteField();
            if ($result) {
                $this->where .= " AND {$result[0]} = {$result[1]} ";
            }

            return sprintf(
                'SELECT COUNT(*) as `count` as `count` FROM `%s` WHERE 1=1 %s',
                $this->trueTableName,
                $this->where
            );
        }

        public function getField($field, $multi = false)
        {
            if (empty($field)) {
                throw new Exception('getField的入参不能为空');
            }

            $result = $this->getTableFalseDeleteField();
            if ($result) {
                $this->where .= " AND {$result[0]} = {$result[1]} ";
            }

            return sprintf(
                'SELECT %s FROM `%s` WHERE 1=1 %s %s',
                $field,
                $this->trueTableName,
                $this->where,
                $multi ? '' : 'LIMIT 1'
            );
        }

        public function like($name, $value)
        {
            if (!empty($value) || $value === 0) {
                $nameList = explode("|", $name);
                foreach ($nameList as $name) {
                    if (empty($name)) {
                        continue;
                    }
                    list($space, $name) = explode(SQL_RAW_PREFIX, $name);
                    if (empty($space)) {
                        $this->where .= " AND $name like $value";
                    } else {
                        $this->where .= " AND $space like '$value'";
                    }
                }
            }
            return $this;
        }

        public function and($name, $value)
        {
            if (!empty($value) || $value === 0) {
                $nameList = explode("|", $name);
                foreach ($nameList as $name) {
                    if (empty($name)) {
                        continue;
                    }
                    list($space, $value) = explode(SQL_RAW_PREFIX, $value);
                    if (empty($space)) {
                        $this->where .= " AND $name = $value";
                    } else {
                        $this->where .= " AND $name = '$space'";
                    }
                }
            }
            return $this;
        }

        public function or($condition)
        {
            $where = " AND ( 1 != 1 ";
            if (count($condition) > 0) {
                foreach ($condition as $name => $value) {
                    if (is_numeric($name)) {
                        continue;
                    }
                    list($space, $value) = explode(SQL_RAW_PREFIX, $value);
                    if (empty($space)) {
                        $where .= " OR $name = $value ";
                    } else {
                        $where .= " OR $name = '$space' ";
                    }
                }
            }
            $where .= " ) ";
            $this->where .= $where;
            return $this;
        }

        public function in($name, array $list)
        {
            if (empty($list)) {
                array_push($list, 0);
            }
            $temp = implode(",", $list);
            $this->where .= " AND $name IN($temp) ";
            return $this;
        }

        public function notIn($name, $list)
        {
            if (empty($list)) {
                array_push($list, 0);
            }
            $temp = implode(",", $list);
            $this->where .= " AND $name NOT IN($temp) ";
            return $this;
        }

        /**
         * 设置或获取SQL
         * @param $sql
         * @return $this|string
         */
        public function sql($sql = null)
        {
            if (!empty($sql)) {
                $this->sql = $sql;
                $this->where = "";
                return $this;
            } else {
                return $this->sql;
            }
        }

        /**
         * 返回记录数或结果集
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/8/1 22:35
         */
        public function execute()
        {
            if (empty($this->sql)) {
                return null;
            }

            try {
                $this->connect();

                $result = parent::execute();

                //钩子
                hook('sql', [
                    'sql' => $this->sql
                ]);

                //执行日志
                $this->log_file('run true :' . $this->sql, 'sql');

                return $result;
            } catch (Exception $e) {
                $this->log_file('run false :' . $this->sql, 'sql');
                error($e->getMessage());
                return false;
            }
        }

    }

    /**
     * SQL拼装类
     * Class Sql
     */
    class Sql extends Db
    {

        public function __construct($tableName = null)
        {
            parent::__construct();
            $this->trueTableName = config('database.tableNamePrefix') . $tableName;
        }

        public function apino($expression, $params)
        {
            return ApinoSql::build($this->trueTableName, $expression, $params)->sql();
        }

    }

    /**
     * 模型类(用于连接数据库和执行SQL语句)
     * Class Model
     */
    class Model extends Db
    {
        public function __construct($tableName = null)
        {
            parent::__construct();
            if ($tableName) {
                $this->trueTableName = config('database.tableNamePrefix') . $tableName;
            }
        }

        public function insert($data)
        {
            $this->sql = parent::insert($data);
            return $this->execute();
        }

        public function delete()
        {
            $this->sql = parent::delete();
            return $this->execute();
        }

        public function update($set)
        {
            $this->sql = parent::update($set);
            return $this->execute();
        }

        public function select($fields = "*")
        {
            $this->sql = parent::select($fields);
            return $this->suppleData($this->execute());
        }

        public function get($fields = "*")
        {
            $this->sql = parent::get($fields);
            return $this->suppleData([$this->execute()])[0];
        }

        public function count()
        {
            $this->sql = parent::count();
            return $this->execute()[__FUNCTION__];
        }

        public function getField($field, $multi = false)
        {
            $this->sql = parent::getField($field, $multi);
            $result = $this->execute()[$field];
            return $multi ? array_column($result, $field) : $result[$field];
        }

        public function apino($expression, $params)
        {
            $apinoSql = ApinoSql::build($this->trueTableName, $expression, $params);
            $result = $this->sql($apinoSql->sql())->execute();
            if ($apinoSql->isSelectOne()) {
                //只查单
                return $result[0];
            } elseif ($apinoSql->isSelectCount()) {
                //只需要总数
                return $result[0]['count'];
            }
            return $result;
        }
    }

    /**
     * Class ApinoSql 表达式模型转换sql类
     */
    class ApinoSql
    {
        use DbPlusTrait;

        protected $sql;

        private $expression;
        /**
         * @var ApinoExpression
         */
        private $apinoExpression;
        /**
         * @var array 入参值
         */
        private $params;
        /**
         * @var bool 查单
         */
        private $isSelectOne = false;
        /**
         * @var bool 查总数
         */
        private $iSelectCount = false;

        /**
         * 是否查单
         */
        public function isSelectOne()
        {
            return $this->isSelectOne;
        }

        /**
         * 是否查询汇总
         */
        public function isSelectCount()
        {
            return $this->iSelectCount;
        }

        /**
         * 构建器
         * Created by PhpStorm.
         * @param $trueTableName
         * @param $expression
         * @param array $params
         * @return ApinoSql
         * @author QiuMinMin
         * Date: 2020/9/4 0:33
         */
        public static function build($trueTableName, $expression, $params = []){
            $self = new self();
            $self->trueTableName = $trueTableName;
            $self->expression = $expression;
            $self->params = $params;
            return $self;
        }
        /**
         * 聚合执行入口
         * Created by PhpStorm.
         * @return string|null
         * @author QiuMinMin
         * Date: 2020/6/8 8:06
         */
        public function sql()
        {
            if(empty($this->sql)){
                $this->apinoExpression = ApinoExpression::resolver($this->expression, $this->params);
                foreach (['select', 'insert', 'update', 'delete'] as $item) {
                    if (strpos($this->expression, $item) > -1) {
                        $this->$item();
                    }
                }
            }
            return $this->sql;
        }
        /**
         * 生成删除SQL
         */
        protected function delete()
        {
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
            $this->sql = $sql;
        }
        /**
         * 生成插入SQL
         */
        protected function insert()
        {
            $fields = [];
            $values = [];
            foreach ($this->params as $k => $v) {
                array_push($fields, $k);
                array_push($values, $v);
            }
            $this->sql = sprintf(
            /** @lang text */ 'INSERT INTO %s(%s) values(%s)',
                $this->trueTableName,
                implode(',', $fields),
                implode(',', $values)
            );
        }
        /**
         * 生成更新SQL
         */
        protected function update()
        {
            $sql = "UPDATE `{$this->trueTableName}` SET %s";
            $set = $this->apinoExpression->set();
            $sql = sprintf($sql, $set);

            $where = $this->apinoExpression->by();
            if (!empty($where)) {
                $sql .= ' WHERE' . $where;
            }

            $sql = sprintf($sql, $set, $where);
            $this->sql = $sql;
        }
        /**
         * 生成查询SQL
         */
        protected function select()
        {

            $sql = "SELECT %s FROM `{$this->trueTableName}`";

            //是否设定指定得字段
            $fields = $this->apinoExpression->only();
            if (empty($fields)) {
                $fields = '*';
            }
            //标记字段
            $mod = $this->apinoExpression->mod();

            if ($mod == 'count') {
                $fields = 'COUNT(*) as `count`';
                $this->iSelectCount = true;
            }
            $sql = sprintf($sql, $fields);

            $where = $this->apinoExpression->by();
            empty($where) || $sql .= ' WHERE' . $where;


            $order = $this->apinoExpression->order();
            empty($order) || $sql .= ' ORDER BY ' . $order;


            $group = $this->apinoExpression->group();
            empty($group) || $sql .= ' GROUP BY ' . $group;

            $limit = $this->apinoExpression->limit();
            $page = $this->apinoExpression->page();
            if (!empty($limit)) {
                $sql .= ' LIMIT ' . $limit;
            } elseif (!empty($page)) {
                $sql .= " LIMIT {$page}";
            } elseif (in_array($mod, ['', 'one'])) {
                $sql .= " LIMIT 1";
                $this->isSelectOne = true;
            }

            $this->sql = $sql;
        }
    }

    /**
     * Class ApinoExpression Apino（表达式解析器）核心类
     */
    class ApinoExpression
    {
        use ErrorTrait;

        /**
         * @var string[] 支持的操作
         */
        private static $optionList = ['select', 'delete', 'update', 'insert'];
        /**
         * @var string[] 支持模式
         */
        private static $modList = ['list', 'page', 'all', 'one', 'count'];
        /**
         * @var string[] 支持子表达式
         */
        private static $subExpList = ['by', 'only', 'none', 'order', 'group', 'limit', 'set', 'page'];

        /**
         * @var $expression string 源表达式
         */
        private $expression;

        /**
         * @var $action string 事件名
         */
        private $action;

        /**
         * @var $option string 操作名
         * @var $mod string 模式
         */
        private $option, $mod;

        /**
         * @var array 子表达式
         * @key [by|only|none]
         */
        private $subExp = [];

        /**
         * @var array 解析后的缓存
         */
        private $subExpCache = [];

        /**
         * @var array 入参值
         */
        private $params = [];

        public function mod()
        {
            return $this->mod;
        }

        public function option()
        {
            return $this->option;
        }

        /**
         * 解析器
         * Created by PhpStorm.
         * @param $expression string
         * @param array $params
         * @return ApinoExpression
         * @author QiuMinMin
         * Date: 2020/6/5 17:03
         * @example 'select { only[user_id,id,name] none[nick]by[user_id,noned_id,ip] }'
         */
        public static function resolver(string $expression, array $params)
        {
            $self = new self($expression);
            $self->params = $params;
            if (!strpos($expression, '{')) {
                //非高级表达式使用简易解析器
                return $self->simpleResolver($expression);
            }
            //去除空格
            $expression = str_ireplace(' ', '', $expression);
            //取出事件名和子表达式
            preg_match('/(\w.*?)\{(.*?)\}/', $expression, $actionAndSub);
            if (count($actionAndSub) > 0) {
                //解析和保存事件名
                $self->actionResolver($actionAndSub[1]);
                //子表达式进行分组
                preg_match_all('/(\w.*?)\[(.*?)\]/', $actionAndSub[2], $matches);
                if (count($matches) > 0) {
                    //遍历拿出子表达式名和值并进行一一组合
                    foreach ($matches[0] as $key => $value) {
                        $self->subExp[$matches[1][$key]] = $matches[2][$key];
                    }
                }
            }
            return $self;
        }

        /**
         * 简易表达式解析器
         * Created by PhpStorm.
         * @param string $expression
         * @return mixed
         * @author QiuMinMin
         * Date: 2020/6/6 2:06
         */
        public function simpleResolver(string $expression)
        {
            //去掉空格
            $expression = str_replace(' ', '', $expression);
            //区分每个单词（在大写字母前加一个空格，并转成小写）
            $matches = preg_replace_callback('/[A-Z]/', function ($matches) {
                return ' ' . strtolower($matches[0]);
            }, $expression);
            $array = explode(' ', $matches);

            if (count($array) == 0) {
                $this->error('事件名不正确');
                return $this;
            }

            //保存事件类型
            if (!in_array($array[0], self::$optionList)) {
                $this->error('不存在该' . $array[0] . '事件类型');
                return $this;
            }

            $this->option = array_shift($array);

            //保存事件模式
            if (isset($array[0]) && in_array($array[0], self::$modList)) {
                $this->mod = array_shift($array);
            }
            //遍历并分配到指定的子表达式值中
            $subExp = [];
            $subExpKey = '';
            foreach ($array as $value) {
                if (in_array($value, self::$subExpList)) {
                    $subExpKey = $value;
                } else {
                    list($name, $valueName) = explode(':', $value);
                    if ($valueName === null) {
                        $valueName = $name;
                    }
                    $subExp[$subExpKey][$name] = $valueName;
                }
            }
            $this->subExp = $subExp;
            return $this;
        }

        /**
         * 事件名解析器
         * Created by PhpStorm.
         * @param $expression
         * @author QiuMinMin
         * Date: 2020/6/6 2:09
         */
        private function actionResolver($expression)
        {
            //事件名操作类型
            preg_match('/(.*[a-z])[A-Z]/', $expression, $matches);
            if (count($matches) > 0) {
                $this->option = $matches[1];
            }
            //事件名模式
            preg_match('/([A-Z].*)/', $expression, $matches);
            if (count($matches) > 0) {
                $this->mod = $matches[1];
            }
        }

        /**
         * 获取子表达式(内置缓存)
         * Created by PhpStorm.
         * @param $key
         * @param Closure|null $handler Closure
         * @param array $params
         * @return mixed|null
         * @author QiuMinMin
         * Date: 2020/6/5 17:56
         */
        private function getSubExpValue($key, Closure $handler = null, $params = [])
        {
            //查询缓存
            $value = $this->subExpCache($key);
            if ($value) {
                //使用缓存
                return $value;
            } else {
                if (!isset($this->subExp[$key])) {
                    return null;
                } else {
                    $value = $this->subExp[$key];
                    if ($value) {
                        if ($handler !== null) {
                            $value = $handler($value, $params);
                        }
                        //缓存
                        $this->subExpCache($key, $value);
                    }

                    return $value;
                }
            }
        }

        /**
         * 子表达式缓存
         * Created by PhpStorm.
         * @param $key
         * @param string $value
         * @return mixed|null
         * @author QiuMinMin
         * Date: 2020/6/5 17:44
         */
        private function subExpCache($key, $value = '')
        {
            if ($value === '') {
                return empty($this->subExpCache[$key]) ? null : $this->subExpCache[$key];
            }
            $this->subExpCache[$key] = $value;
            return null;
        }

        private function __construct($expression = '')
        {
            $this->expression = $expression;
        }

        /**
         * 获取子表达式中的only信息，对应的是sql中的field部位因为格式相同可直接使用
         * Created by PhpStorm.
         * @return null|string
         * Date: 2020/6/5 17:22
         * @author QiuMinMin
         */
        public function only()
        {
            return $this->getSubExpValue(__FUNCTION__);
        }

        /**
         * 子表达式中得set
         * Created by PhpStorm.
         * @param array $params
         * @return mixed|null
         * @author QiuMinMin
         * Date: 2020/6/7 20:51
         */
        public function set($params = [])
        {
            if (isset($this->subExp[''])) {
                $this->subExp[__FUNCTION__] = $this->subExp[''];
            }

            $params = array_merge($this->params, $params);
            return $this->getSubExpValue(__FUNCTION__, function ($array, $params) {
                if (gettype($array) != 'array') {
                    $array = explode(',', $array);
                }

                foreach ($array as $key => $value) {
                    list($field, $valueName) = explode(':', $value);
                    //如果为空则名字一致
                    $valueName = $valueName ?? $field;
                    //获取入参值
                    $value = $params[$valueName];
                    $array[$key] = "`$field` = '$value'";
                }
                return implode(',', $array);
            }, $params);
        }

        /**
         * 获取子表达式中的none值，在数据库查询后指定过滤得字段（不返给前端）
         * Created by PhpStorm.
         * @return array|null
         * Date: 2020/6/5 17:24
         * @author QiuMinMin
         */
        public function none()
        {
            return $this->getSubExpValue(__FUNCTION__);
        }

        /**
         * 条件，对应sql中的where部分
         * Created by PhpStorm.
         * @param array $params
         * @return string
         * @author QiuMinMin
         * Date: 2020/6/5 17:27
         */
        public function by($params = [])
        {
            $params = array_merge($this->params, $params);
            return $this->getSubExpValue(__FUNCTION__, function ($value, $params) {
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
            }, $params);
        }

        private function highResolverBy($expression, array $params)
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

        /**
         * 对应sql中的order by
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/6/5 17:28
         */
        public function order()
        {
            return $this->getSubExpValue(__FUNCTION__, function ($value) {
                $list = array_keys($value);
                foreach ($list as $k => $v) {
                    if (substr($v, 1, 1) === '!') {
                        $list[$k] = substr($v, 1) . ' DESC';
                    } else {
                        if (substr($v, -1, 1) === '!') {
                            $list[$k] = substr($v, 0, strlen($v) - 1) . ' DESC';
                        } else {
                            $list[$k] = $v . ' ASC';
                        }
                    }
                }
                return implode(' ', $list);
            });
        }

        /**
         * 对应数据库里的分组
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/6/5 17:29
         */
        public function group()
        {
            return $this->getSubExpValue(__FUNCTION__, function ($value) {
                return explode(',', $value);
            });
        }

        /**
         * 对应sql中的的limit
         * Created by PhpStorm.
         * @param array $params
         * @return mixed|null
         * @author QiuMinMin
         * Date: 2020/6/5 17:30
         */
        public function limit($params = [])
        {
            $params = array_merge($this->params, $params);
            return $this->getSubExpValue(__FUNCTION__, function ($value, $params) {
                $value = explode(',', $value);
                foreach ($value as $i => $item) {
                    if (!is_numeric($item)) {
                        $value[$i] = $params[$item];
                    }
                }
                return implode(',', $value);
            }, $params);
        }

        public function page($params = [])
        {
            $params = array_merge($this->params, $params);
            return $this->getSubExpValue(__FUNCTION__, function ($value, $params) {
                $value = explode(',', $value);
                foreach ($value as $i => $item) {
                    if (!is_numeric($item)) {
                        $value[$i] = $params[$item];
                    }
                }
                return implode(',', $value);
            }, $params);
        }
    }

    /**
     * 上传文件封装类
     * Class File
     */
    class File
    {
        /**
         * @var $name string 文件名
         */
        private $name;
        /**
         * @var $tmp string 临时文件名
         */
        private $tmpName;
        /**
         * @var $size string 文件大小
         */
        private $size;
        /**
         * @var $fileName string 上传的文件名
         */
        private $fileName;
        /**
         * @var $saveName string 保存的路径
         */
        private $saveName;
        /**
         * @var $error string 错误信息
         */
        private $error;

        public function __construct($name)
        {
            $this->name = $name;
        }

        /**
         * 是否有上传文件
         * @return bool
         */
        public function hasFile()
        {
            return isset($this->tmp);
        }

        /**
         * 文件是否已经转移
         * @return bool
         */
        public function isMove()
        {
            return isset($this->saveName);
        }

        /**
         * 获取保存的文件名
         */
        public function getSaveName()
        {
            return $this->saveName;
        }

        /**
         * 获取上传文件名
         * @return string
         */
        public function getFileName()
        {
            return $this->fileName;
        }

        /**
         * 获取错误信息
         * @return string
         */
        public function getError()
        {
            return $this->error;
        }

        /**
         * 校验上传文件得有效性
         * @param array $extArray 允许得格式
         * @param null $maxSize 允许得最大大小
         * @return bool
         */
        public function valid($extArray = [], $maxSize = null)
        {
            $temp = explode(".", $this->fileName);
            $ext = array_pop($temp);
            if (!in_array($ext, $extArray)) {
                $this->error = "上传文件的格式为{$ext}, 非合法格式：" . implode(",", $extArray);
                return false;
            }
            if ($this->size > $maxSize) {
                $this->error = "上传文件大小为{$this->size}，已超过$maxSize";
                return false;
            }
            return true;
        }

        /**
         * 移动文件
         * @param $destination
         * @return bool
         */
        public function move($destination)
        {
            $temp = explode("/", $destination);
            array_pop($temp);
            $path = implode("/", $temp);

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $res = move_uploaded_file($this->tmpName, $destination);
            if (!$res) {
                $this->error = "系统权限不足，无法上传文件";
                return false;
            }
            return $res;
        }
    }

    /**
     * 路由解析类
     * Class Router
     * @package apino
     */
    class Router
    {
        private $controller, $action, $url;

        private function __construct($controller = null, $action = null)
        {
            $this->controller = $controller;
            $this->action = $action;
        }

        /**
         * @param null $controller
         * @return mixed
         */
        public function controller($controller = null)
        {
            if ($controller) {
                $this->controller = $controller;
                return $this;
            } else {
                return $this->controller;
            }
        }

        /**
         * @param null $url
         * @return mixed
         */
        public function url($url = null)
        {
            if ($url) {
                $this->url = $url;
                return $this;
            } else {
                return $this->url;
            }
        }

        /**
         * @param null $action
         * @return mixed
         */
        public function action($action = null)
        {
            if ($action) {
                $this->action = $action;
                return $this;
            } else {
                return $this->action;
            }
        }

        /**
         * 路由解析器
         * @param null $url
         * @return Router
         * @todo 实现设置route之后可以有默认action操作，以及其他的操作等
         */
        public static function route()
        {
            $self = new self();

            $url = parse_url($_SERVER['REQUEST_URI'])['path'];
            $index = stripos($url, ".php");
            $url = substr($url, $index ? $index + 4 : 0);

            //自定义路由转换
            $self->url = self::converter($url);

            //拆分
            list($controller, $action) = explode('/', substr($self->url, 1));

            //URL格式检验
            if (empty($controller) || empty($action)) {
                error("无法正常解析URL，检查URL格式是否正确（正确格式：/控制器/事件名?参数名=参数值&参数名=参数值...）");
            }

            return $self->controller($controller)->action($action);
        }

        /**
         * 根据用户定义路由进行变更
         * Created by PhpStorm.
         * @param $url
         * @return string
         * @author QiuMinMin
         * Date: 2020/5/26 20:29
         */
        private static function converter($url)
        {
            list($controller, $action) = self::parse($url);

            if ($action === '') {
                $action = '*';
            }

            $route = config('route');

            foreach ($route as $key => $value) {
                list($controllerBefore, $actionBefore) = self::parse($key);

                //第一种完成一样
                $flag1 = $controller == $controllerBefore && $action == $actionBefore;
                //第二种后模糊
                $flag2 = $controller == $controllerBefore && $actionBefore == '*';
                //第三种前模糊
                $flag3 = $controllerBefore == '*' && $action == $actionBefore;
                //第四种全模糊
                $flag4 = $controllerBefore == '*' && $actionBefore == '*';

                if ($flag1 || $flag2 || $flag3 || $flag4) {
                    //路由转发支持转发到其他http网站
                    if (stripos($value, 'http') > -1) {
                        //将入参填充到url中
                        $params = input();
                        foreach ($params as $k => $v) {
                            $value = str_ireplace("#{$k}#", $v, $value);
                        }
                        header('Location:' . $value);
                        die();
                    }

                    list($controllerAfter, $actionAfter) = self::parse($value);
                    //目标不是*则替换为目标值
                    if ($controllerAfter != '*') {
                        $controller = $controllerAfter;
                    }
                    if ($actionAfter != '*') {
                        $action = $actionAfter;
                    }
                    if ($controller === '') {
                        $controller = $controllerAfter;
                    }
                    if ($action === '') {
                        $action = $actionAfter;
                    }
                    return "/$controller/$action";
                }
            }
            return $url;
        }

        /**
         * 解析url
         * Created by PhpStorm.
         * @param $url
         * @return array|string[]
         * @author QiuMinMin
         * Date: 2020/5/31 19:42
         */
        private static function parse($url)
        {
            list($controller, $action) = self::urlParse($url);
            if ($controller === null) {
                $controller = '*';
            }
            if ($action === null) {
                $action = '*';
            }
            return [$controller, $action];
        }

        /**
         * url解析工具
         * Created by PhpStorm.
         * @param $url
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/22 22:16
         */
        public static function urlParse($url)
        {
            //去除开头的/
            $url = (substr($url, 0, 1) == '/') ? substr($url, 1) : $url;
            $url = (substr($url, -1, 1) == '/') ? substr($url, 0, strlen($url) - 1) : $url;
            return explode('/', $url);
        }

    }

    /**
     * API拦截器
     * Class SystemApi
     */
    class Api
    {
        /**
         * 执行拦截器
         * Created by PhpStorm.
         * @param $controller
         * @param $action
         * @return false
         * @author QiuMinMin
         * Date: 2020/5/23 8:41
         */
        public static function interceptor($controller, $action)
        {
            if (empty($controller) || empty($action)) {
                //拦截失败
                return false;
            }
            $handlerClass = ucfirst($controller) . 'ApiHandler';
            $config = config('api.' . $controller);
            if (class_exists($handlerClass) || $config) {
                $config = $config[$action];

                //场景专用配置
                $sence = Request::instance()->param('sence');
                if (!empty($config[$sence])) {
                    $config = $config[$sence];
                }
                $handler = new $handlerClass();
                if (method_exists($handler, $action)) {
                    return $handler->$action($config);
                }
            }
            return null;
        }

        /**
         * 校验TOKEN值
         * Created by PhpStorm.
         * @param Router $router
         * @return array
         * @author QiuMinMin
         * Date: 2020/8/18 8:05
         */
        public static function checkToken(Router $router)
        {
            if ($router->controller() == 'util' && $router->action() == 'token') {
                return Result::success();
            }

            $c = config('api.util.token');
            if (!$c['switch']) {
                return Result::success();
            }

            if (!input($c['param'])) {
                return Result::error('缺少TOKEN值');
            }

            if (!session('token.value')) {
                return Result::error('请先获取TOKEN值');
            }
            if (session('token.value') != input($c['param'])) {
                return Result::error('TOKEN值错误');
            }

            return Result::success();
        }

        /**
         * 验证安全验证码
         * Created by PhpStorm.
         * @param Router $router
         * @return array
         * @author QiuMinMin
         * Date: 2020/8/18 19:27
         */
        public static function checkVerifyCode(Router $router)
        {
            /**
             * @var $verifyCode VerifyCode
             */
            $verifyCode = config("api.util.verifyCode.sence.{$router->url()}");
            if (!$verifyCode) {
                return Result::success();
            }
            $param = config('api.util.verifyCode.param');
            if (!VerifyCode::checkCode($verifyCode->type(), input($param))) {
                return Result::error('验证码错误');
            }
            return Result::success();
        }
    }

    /**
     * 验证码工具类
     * Class VerifyCode
     */
    class VerifyCode
    {

        const IMAGE = 'image';
        const EMAIL = 'email';
        const MOBILE = 'mobile';

        /**
         * @var string 验证码
         */
        private $code;

        //当前生成验证码类型
        private $type;
        //验证码长度
        private $codeNumber;
        //图片验证码信息
        private $width, $height;
        //邮箱验证码信息
        private $codeTemplate, $title, $content;
        //手机验证码信息
        private $args;

        /**
         * 标记为邮箱验证码
         * Created by PhpStorm.
         * @param $codeNumber
         * @param $title
         * @param $content
         * @param $codeTemplate
         * @return VerifyCode
         * @author QiuMinMin
         * Date: 2020/8/18 0:33
         */
        public static function asEmail($codeNumber, $title, $content, $codeTemplate)
        {
            $self = new self();
            $self->type = self::EMAIL;
            $self->title = $title;
            $self->content = $content;
            $self->codeTemplate = $codeTemplate;
            $self->codeNumber = $codeNumber;
            return $self;
        }

        /**
         * 标记为图片验证码
         * Created by PhpStorm.
         * @param $codeNumber
         * @param $width
         * @param $height
         * @return VerifyCode
         * @author QiuMinMin
         * Date: 2020/8/18 0:34
         */
        public static function asImage($codeNumber, $width, $height)
        {
            $self = new self();
            $self->type = self::IMAGE;
            $self->width = $width;
            $self->height = $height;
            $self->codeNumber = $codeNumber;
            return $self;
        }

        /**
         * 标识为手机验证码
         * Created by PhpStorm.
         * @param $codeNumber
         * @param array $args
         * @return VerifyCode
         * @author QiuMinMin
         * Date: 2020/8/18 20:32
         */
        public static function asMobile($codeNumber, $args = [])
        {
            $self = new self();
            $self->type = self::MOBILE;
            $self->codeNumber = $codeNumber;
            $self->args = $args;
            return $self;
        }

        /**
         * 执行
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/8/18 20:34
         */
        public function builder()
        {
            $this->generatorCode();
            switch ($this->type) {
                case self::EMAIL:
                    return $this->email();
                case self::IMAGE:
                    return $this->image();
                case self::MOBILE:
                    $closure = config('handler.sms_handler');
                    if (!Is::closure($closure)) {
                        return false;
                    }
                    return $closure($this->args);
            }
            return false;
        }

        /**
         * 生成验证码
         * @return false|string
         */
        private function generatorCode()
        {
            $codes = implode(
                "",
                array_merge(
                    range(0, 9),
                    range("a", "z"),
                    range("A", "Z")
                )
            );
            $this->code = substr(str_shuffle($codes), 0, $this->codeNumber);

            session("verifyCode.{$this->type}", $this->code);

            return $this;
        }

        public function image()
        {
            $width = $this->width;
            $height = $this->height;
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
            imagestring($image, 5, 10, 10, $this->code, $font_color);
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
            die();
        }

        public function email()
        {
            $emailConfig = config('smtp');
            $smtp = new Smtp(
                $emailConfig['server'], $emailConfig['port'], true, $emailConfig['sender'],
                $emailConfig['password']
            );//这里面的一个true是表示使用身份验证,否则不使用身份验证.
            $smtp->debug = false;//是否显示发送的调试信息
            //内容模板替换#code#关键字
            $this->content = str_ireplace($this->codeTemplate, $this->code, $this->content);
            $state = $smtp->sendmail(
                $emailConfig['receiver'],
                $emailConfig['sender'],
                $this->title,
                $this->content,
                $emailConfig['type']
            );
            if ($state == "") {
                return false;
            } else {
                return true;
            }
        }

        public function type()
        {
            return $this->type;
        }

        /**
         * 核对验证码
         * Created by PhpStorm.
         * @param $type
         * @param $code
         * @return array
         * @author QiuMinMin
         * Date: 2020/6/8 9:26
         */
        public static function checkCode($type, $code)
        {
            $sessCode = session('verifyCode.' . $type);
            if (empty($sessCode)) {
                return Result::error('请先获取验证码');
            }
            if (empty($code)) {
                return Result::error('验证码不能为空');
            }
            if ($sessCode != $code) {
                return Result::error('验证码不正确');
            } else {
                return Result::success();
            }
        }
    }

    /**
     * 用户类
     * Class User
     */
    class User
    {

        private static $prefix = 'apino_user.';

        /**
         * 编辑/获取用户ID
         * Created by PhpStorm.
         * @param string $id
         * @return array|mixed|string
         * @author QiuMinMin
         * Date: 2020/5/25 10:58
         */
        public static function id($id = '')
        {
            $sessId = self::$prefix . 'id';
            if ($id === '') {
                return session($sessId);
            } else {
                session($sessId, $id);
                return null;
            }
        }

        /**
         * 权限列表
         * Created by PhpStorm.
         * @param string $authority
         * @return array|mixed|string|null
         * @author QiuMinMin
         * Date: 2020/5/25 11:03
         */
        public static function authority($authority = '')
        {
            $sessName = self::$prefix . 'authority';
            if ($authority === '') {
                return session($sessName);
            } else {
                session($sessName, $authority);
                return null;
            }
        }

        /**
         * 是否已经登录
         * Created by PhpStorm.
         * @return bool
         * @author QiuMinMin
         * Date: 2020/5/25 11:01
         */
        public static function isLogin()
        {
            return !empty(self::id());
        }

        /**
         * 退出登录
         * Created by PhpStorm.
         * @return bool
         * @author QiuMinMin
         * Date: 2020/5/25 11:04
         */
        public static function logout()
        {
            session_destroy();
            self::id(null);
            return true;
        }

        /**
         * 是否拥有该权限
         * Created by PhpStorm.
         * @param $url
         * @return bool
         * @author QiuMinMin
         * Date: 2020/5/25 11:04
         */
        public static function hasAuth($url)
        {
            $auth = self::authority();

            //全部权限(true)或无任何权限(false)
            if (gettype($auth) === 'boolean') {
                return $auth;
            }

            //添加白名单地址到当前用户权限中
            $auth = array_merge(config('app.user.auth.except'), $auth);

            //不在权限列表中
            if (!in_array($url, $auth)) {
                return false;
            }

            return true;
        }

        /**
         * 同步权限信息
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/6/21 11:26
         */
        public static function syncAuth()
        {
            $userId = User::id();
            $config = config('app.user');
            switch ($config['auth']['mode']) {
                case DATABASE:
                    //权限入库形式
                    $roleId = $config['role']($userId);
                    session(self::$prefix . 'role', $roleId);
                    $auth = $config['auth']($roleId);
                    session(self::$prefix . 'auth', $auth);
                    break;
                case CONFIG:
                    //权限配置形式
                    $roleId = empty($userId) ? GUEST : $config['role']($userId);
                    session(self::$prefix . 'role', $roleId);
                    $auth = config('authority.' . $roleId);
                    session(self::$prefix . 'authority', $auth);
                    break;
                case NONE:
                    if (self::isLogin()) {
                        session(self::$prefix . 'role', 'ROOT');
                        //登录后直接获得全部权限
                        session(self::$prefix . 'auth', true);
                    } else {
                        session(self::$prefix . 'role', GUEST);
                        //未登录只能用游客权限
                        session(self::$prefix . 'auth', config('authority.' . GUEST));
                    }
                    //无权限控制概念，仅登录和未登录
                    break;
            }
        }
    }

    /**
     * 配置检查工具
     * Class AppConfigInspectionTool
     */
    class ConfigChecker
    {

        const CLOSURE = Closure::class;
        const INT = 'integer';
        const STR = 'string';
        const BOOL = 'boolean';
        const ARR = 'array';
        const FLOAT = 'float';
        const DOUBLE = 'double';

        public static function run()
        {
            if (empty($GLOBALS['config_checker'])) {
                return;
            }

            $GLOBALS['config_checker'] = 1;

            try {
                $self = new self();

                $self->checkAppConfig();
                $self->checkDatabaseConfig();
                $self->checkSmtpConfig();
                $self->checkRouteConfig();
                $self->checkAuthorityConfig();
                $self->checkApiConfig();
            } catch (Exception $e) {
                Response::error($e->getMessage());
                die();
            }
        }

        private function throwConfigError($configNickName, $configName, $typeName)
        {
            throw new Exception("配置{$configNickName}({$configName})必须存在且值类型为{$typeName}");
        }

        private function checkArray($vc, $prefixName = '', $prefixNick = '')
        {
            $name = $prefixName . $vc['name'];
            $nick = $prefixNick . $vc['nick'];
            $this->check($name, $vc['nick'], $vc['type']);
            foreach ($vc['children'] as $child) {
                $this->checkArray($child, $name . '.', $nick);
            }
        }

        private function check($configName, $configNickName, $type)
        {
            $var = [
                self::BOOL => '布尔型',
                self::INT => '整型',
                self::STR => '字符串型',
                self::ARR => '数组型',
                self::FLOAT => '浮点型',
                self::DOUBLE => '双精度型',
            ];
            $class = [
                self::CLOSURE => Closure::class
            ];

            if (in_array($type, array_keys($var))) {
                if (config($configName) === null || gettype(config($configName)) !== $type) {
                    $this->throwConfigError($configNickName, $configName, $var[$type]);
                }
            } elseif (in_array($type, array_keys($class))) {
                if (config($configName) === null || get_class(config($configName)) !== $type) {
                    $this->throwConfigError($configNickName, $configName, $class[$type]);
                }
            } else {
                $this->throwConfigError($configNickName, $configName, $type);
            }
        }

        private function checkAppConfig()
        {
            $this->checkArray($this->c('app', '应用', self::ARR, [
                $this->c('debug', '调试模式', self::BOOL),
                $this->c('response', '出参配置', self::ARR, [
                    $this->c('page', '分页', self::ARR, [
                        $this->c('total', '记录总数出参名', self::STR),
                        $this->c('list', '记录数据出参名', self::STR)
                    ]),
                ]),
                $this->c('trueDeleteLock', '真删锁', self::ARR, [
                    $this->c('switch', '验证开关', self::BOOL),
                    $this->c('key', '值', self::STR),
                    $this->c('param', '入参名', self::STR),
                ]),
                $this->c('user', '用户', self::ARR, [
                    $this->c('id', 'ID', self::STR),
                    $this->c('role', '角色', self::CLOSURE),
                    $this->c('auth', '权限', self::ARR, [
                        $this->c('mode', '模式', self::STR),
                        $this->c('source', '获取源', self::CLOSURE),
                        $this->c('except', '权限白名单', self::ARR),
                    ]),
                ])
            ]));
        }

        private function checkDatabaseConfig()
        {
            $this->checkArray($this->c('database', '数据库', self::ARR, [
                $this->c('type', '类型', self::STR),
                $this->c('host', '地址', self::STR),
                $this->c('port', '端口', self::STR),
                $this->c('dbname', '库名', self::STR),
                $this->c('username', '用户名', self::STR),
                $this->c('password', '密码', self::STR),
                $this->c('tableNamePrefix', '表名前缀', self::STR),
                $this->c('charset', '编码方式', self::STR),
                $this->c('autoQuery', '自动关联查询', self::ARR),
                $this->c('autoCompleteInsert', '自动补全插入', self::BOOL),
                $this->c('autoCompleteNoNull', '自动填充字段不存在null值', self::BOOL),
                $this->c('autoTimestamp', '自动插入时间戳', self::ARR, [
                    $this->c('switch', '开关', self::BOOL),
                    $this->c('createTime', '创建时间', self::ARR),
                    $this->c('updateTime', '修改时间', self::ARR),
                    $this->c('generator', '时间戳值', self::INT)
                ]),
                $this->c('falseDeleteValue', '表假删除状态', self::ARR)
            ]));
        }

        private function checkSmtpConfig()
        {
            $this->checkArray($this->c('smtp', '邮箱', self::ARR, [
                $this->c('server', '域名', self::STR),
                $this->c('port', '端口', self::INT),
                $this->c('user', '授权用户名', self::STR),
                $this->c('pass', '授权密码', self::STR),
                $this->c('debug', '调试模式', self::BOOL),
                $this->c('type', '内容格式', self::STR),
            ]));
        }

        private function checkRouteConfig()
        {
            $this->checkArray($this->c('route', '路由', self::ARR));
        }

        private function checkAuthorityConfig()
        {
            $this->checkArray($this->c('authority', '角色权限', self::ARR, [
                $this->c(GUEST, '游客', self::ARR),
            ]));
        }

        private function checkApiConfig()
        {
            $this->checkArray($this->c('api', '预设接口', self::ARR, [
                $this->c('util', '通用', self::ARR, [
                    $this->c('upload', '上传配置', self::ARR, [
                        $this->c('image', '图片', self::ARR, [
                            $this->c('fileParam', '文件入参', self::STR),
                            $this->c('maxSize', '大小最大限制', self::INT),
                            $this->c('dir', '存储定制', self::STR),
                        ]),
                        $this->c('php', 'PHP', self::ARR, [
                            $this->c('fileParam', '文件入参', self::STR),
                            $this->c('maxSize', '大小最大限制', self::INT),
                            $this->c('dir', '存储定制', self::STR),
                        ]),
                    ]),
                    $this->c('verifyCode', '验证码', self::ARR, [
                        $this->c('param', '入参名', self::STR),
                        $this->c('sence', '场景', self::ARR),
                    ]),
                    $this->c('token', 'TOKEN', self::ARR, [
                        $this->c('switch', '开关', self::BOOL),
                        $this->c('param', '入参名', self::STR),
                        $this->c('generator', '生成器', self::CLOSURE)
                    ]),
                ]),
                $this->c('user', '用户', self::ARR, [
                    $this->c('register', '注册', self::ARR, [
                        $this->c('username', '用户名入参名', self::STR),
                        $this->c('password', '密码入参名', self::STR),
                        $this->c('confirmPassword', '确认密码入参名', self::STR),
                        $this->c('passwordEncode', '密码加密回调', self::CLOSURE),
                        $this->c('safety', '前置安全验证回调', self::CLOSURE),
                        $this->c('success', '成功后置回调', self::CLOSURE),
                        $this->c('error', '失败后置回调', self::CLOSURE),
                    ]),
                    $this->c('login', '登录', self::ARR, [
                        $this->c('username', '用户名入参名', self::STR),
                        $this->c('password', '密码入参名', self::STR),
                        $this->c('passwordEncode', '密码加密回调', self::CLOSURE),
                        $this->c('safety', '前置安全验证回调', self::CLOSURE),
                        $this->c('success', '成功后置回调', self::CLOSURE),
                        $this->c('error', '失败后置回调', self::CLOSURE),
                    ]),
                    $this->c('logout', '登出后置回调', self::CLOSURE),
                    $this->c('checkLogin', '检查登录验证回调', self::CLOSURE),
                ])
            ]));
        }

        //校验配置
        private function c($name, $nick, $type, $children = [])
        {
            return [
                'name' => $name,
                'nick' => $nick,
                'type' => $type,
                'children' => $children
            ];
        }
    }

    /**
     * 判断类
     * Class Is
     */
    class Is
    {
        /**
         * 是否闭包
         * Created by PhpStorm.
         * @param $closure
         * @return bool
         * @author QiuMinMin
         * Date: 2020/6/27 20:48
         */
        public static function closure($closure)
        {
            return $closure && get_class($closure) == Closure::class;
        }

        /**
         * 是否数组
         * Created by PhpStorm.
         * @param $array
         * @return bool
         * @author QiuMinMin
         * Date: 2020/6/27 20:48
         */
        public static function array($array)
        {
            return $array && gettype($array) == 'array';
        }

    }

    /**
     * 系统内置工具API处理类
     * Class UtilApi
     */
    class UtilApiHandler
    {

        /**
         * 系统内置上传接口
         * Created by PhpStorm.
         * @param array $c
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/17 23:06
         */
        public function upload(array $c)
        {
            $exts = [
                'php' => ['php'],
                'image' => ['jpg', 'png', 'jpeg', 'ico', 'gif'],
            ];
            $ext = $exts[$c['sence']];
            $param = $c['fileParam'];
            $maxSize = $c['maxSize'];
            $file = Request::instance()->file($param);
            if (!$file->valid($ext, $maxSize)) {
                return Result::error($file->getError());
            }
            $dir = $c['dir'] ?: '/upload';

            $filename = $dir . '/' . $file->getFileName();
            if (!$file->move(ROOT_PATH . $filename)) {
                return Result::error($file->getError());
            }
            return Result::success([
                'url' => $filename
            ]);
        }

        /**
         * 系统内置安全码接口
         * Created by PhpStorm.
         * @param array $c
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/17 23:06
         */
        public function verifyCode(array $c)
        {
            $sence = input('sence');

            /**
             * @var $verifyCode VerifyCode
             */
            $verifyCode = config("api.util.verifyCode.{$sence}");
            if (empty($verifyCode)) {
                return Result::error('无该场景的验证码');
            }

            $result = $verifyCode->builder();

            //如果有返回值判断为false则生成失败
            if (!$result) {
                return Result::error('获取验证码失败');
            }
            return Result::success([]);
        }

        /**
         * token api 处理方法
         * Created by PhpStorm.
         * @param array $c
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/17 23:11
         */
        public function token(array $c)
        {
            $now = time();

            if (!$c['switch']) {
                return Result::error('当前已关闭token验证');
            }

            $lastTime = session('token.last_time') ?: 0;
            $lastToken = session('token.value');
            if ($now - $lastTime > $c['interval']) {
                //超过时间可重新生成新TOKEN
                $lastToken = md5(base64_encode(substr($now, 0, 8)));
                session('token.value', $lastToken);
                session('token.last_time', $now);
            }

            if (!$lastToken) {
                return Result::error('当前获取TOKEN频率过快');
            }

            return Result::success([
                'token' => $lastToken
            ]);
        }
    }

    /**
     * 系统内置用户API处理类
     * Class UserApi
     */
    class UserApiHandler
    {
        /**
         * 登录
         * Created by PhpStorm.
         * @param array $c
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/23 20:52
         */
        public function login(array $c)
        {
            $appUserConfig = config('app.user');

            $nameParam = $c['username'];
            $passParam = $c['password'];
            $passEncode = $c['passwordEncode'];
            list($table, $field) = explode('.', $appUserConfig['id']);
            $safety = $c['safety'];
            $success = $c['success'];
            $error = $c['error'];

            //用户自定义校验
            if (Is::closure($safety)) {
                $array = $safety(input());
                if (Is::array($array) && !$array[0]) {
                    return $array;
                }
            }


            $name = input($nameParam);
            if (!isset($name) || empty(trim($name))) {
                return Result::error("登录账户名不能为空");
            }

            $pass = input($passParam);

            //加密闭包
            if (Is::closure($passEncode)) {
                $pass = $passEncode($pass) ?: $pass;
            }

            $user = dao("/$table/selectOneBy" . ucfirst($nameParam), [
                $nameParam => $name
            ]);

            if (!$user) {
                //调用用户自定义失败回调
                $error($name);
                return Result::error('用户不存在');
            }

            if ($user[$passParam] !== $pass) {
                return Result::error('密码错误');
            }

            //调用获取用户信息后的成功回调
            $result = $success($user);
            if (!empty($result)) {
                $error($name);
                return Result::error('用户不存在');
            }

            //储存用户ID
            User::id($user[$field]);

            return Result::success();
        }

        /**
         * 登出
         * Created by PhpStorm.
         * @param array $c
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/23 20:51
         */
        public function logout()
        {
            return User::logout() ? Result::success() : Result::error();
        }

        /**
         * 用户注册处理方法
         * Created by PhpStorm.
         * @param array $c
         * @return array
         * @author QiuMinMin
         * Date: 2020/6/8 9:00
         */
        public function register(array $c)
        {
            $username = input($c['username']);
            $password = input($c['password']);
            $confirmPassword = input($c['confirmPassword']);
            $passwordEncode = $c['passwordEncode'];

            $success = $c['success'];
            $error = $c['error'];

            if (empty($username)) {
                return Result::error('账户名不能为空');
            }
            if (empty($password)) {
                return Result::error('密码不能为空');
            }
            if ($password != $confirmPassword) {
                return Result::error('两次密码不一致');
            }

            //用户自定义安全校验
            $safety = $c['safety'];
            if (get_class($safety) == Closure::class) {
                list($result, $error) = $safety(input());
                if (!$result) {
                    return Result::error($error);
                }
            }

            if (get_class($passwordEncode) == Closure::class) {
                $password = $passwordEncode($password);
            }
            $data = [];
            $values = $c['values'];
            if (get_class($values) == Closure::class) {
                $data = array_merge($data, $values());
            }

            $data[$c['username']] = $username;
            $data[$c['password']] = $password;

            list($table, $pk) = explode('.', $c['id']);

            $result = model($table)->insert($data);
            if ($result == 0) {
                //失败回调
                $error($username);
                return Result::error('注册失败');
            }

            $user = model($table)
                ->and($c['username'], $username)
                ->get();

            //调用用户自定义得成功回调
            $success($user);

            return Result::success();
        }

        /**
         * 检查用户是否已登录接口
         * Created by PhpStorm.
         * @return array
         * @author QiuMinMin
         * Date: 2020/5/25 5:58
         */
        public function checkLogin()
        {
            return User::isLogin() ? Result::success() : Result::error();
        }
    }

    /**
     * 程序核心流程
     * Class App
     * @package apino
     */
    class Core
    {
        /**
         * 主程序执行流程
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/5/30 15:33
         */
        public static function run()
        {
            if (config('app.debug')) {
                ini_set("display_errors", "On");
                error_reporting(E_ALL);
                //配置检查员(app.debug开启后才会执行)
                ConfigChecker::run();
            }

            //注册信息
            self::register();

            //页面不进行直接渲染
            ob_start();

            //加载用户配置引入文件
            $files = config('include_file');
            if(Is::array($files)){
                foreach ($files as $file){
                    if(file_exists($file)){
                        require_once $file;
                    }
                }
            }

            //初始化钩子
            hook('initialize');

            //获取路由信息
            $router = Router::route();

            //校验TOKEN(全指定或全不校验)
            list($success, $result) = Api::checkToken($router);
            !$success && Response::error($result);

            //校验验证码(仅指定api校验)
            list($success, $result) = Api::checkVerifyCode($router);
            !$success && Response::error($result);

            //日志钩子
            hook('router', [
                'router' => $router
            ]);

            //同步当前用户角色权限信息
            User::syncAuth();

            //权限判断
            $result = User::hasAuth($router->url());

            //日志钩子
            hook('authority', [
                'router' => $router,
                'userId' => User::id(),
                'userAuth' => User::authority(),
                'result' => $result
            ]);

            if (!$result) {
                Response::error('当前用户没有该权限');
            }

            //内置api拦截
            $result = Api::interceptor($router->controller(), $router->action());
            if ($result !== false) {
                $result[0] ? Response::send($result[1]) : Response::error($result[1]);
            }

            $params = Request::instance()->param();

            //入参校验器
            list($success, $result) = validate($router->url(), $params);
            !$success && Response::error($result);

            //入参转换器
            $params = inputConverter($router->url(), $result);

            //调用相应方法
            $object = controller($router->url(), $params);

            //响应内容
            Response::send($object);

            //页面缓存输出
            ob_end_flush();
        }

        /**
         * 注册函数
         * Created by PhpStorm.
         * @author QiuMinMin
         * Date: 2020/8/16 0:28
         */
        private static function register()
        {
            //注册错误处理函数
            set_error_handler(config('handler.set_error_handler'));
            set_exception_handler(config('handler.set_exception_handler'));
            register_shutdown_function(config('handler.register_shutdown_function'));
        }
    }

    /**
     * JS工具
     * Class Js
     */
    class EmbedJs
    {
        /**
         * JS跳转
         * Created by PhpStorm.
         * @param $url
         * @author QiuMinMin
         * Date: 2020/7/4 14:44
         */
        public function locationHref($url)
        {
            echo "<script>location.href='{$url}';</script>";
        }

        /**
         * JS弹窗
         * Created by PhpStorm.
         * @param $content
         * @author QiuMinMin
         * Date: 2020/7/4 14:44
         */
        public function alert($content)
        {
            echo "<script>alert('$content');</script>";
        }

        /**
         * JS打印日志
         * Created by PhpStorm.
         * @param $content
         * @author QiuMinMin
         * Date: 2020/7/4 14:44
         */
        public function consoleLog($content)
        {
            echo "<script>console.log('$content')</script>";
        }

    }

    /**
     * 请求类
     * Class EmbedReq
     */
    class EmbedReq
    {
        /**
         * post请求
         * Created by PhpStorm.
         * @param $url
         * @param null|Closure $callback
         * @param array $params
         * @return array|null
         * @author QiuMinMin
         * Date: 2020/7/4 14:44
         */
        public function post($url, Closure $callback = null, $params = [])
        {
            if (!Request::instance()->isPost()) {
                return null;
            }
            list($success, $result) = controller($url, $params);
            if (!$callback) {
                return [$success, $result];
            }
            $callback($success, $result);
            return null;
        }

        /**
         * get请求
         * Created by PhpStorm.
         * @param $url
         * @param Closure|null $callback
         * @param array $params
         * @return array|null
         * @author QiuMinMin
         * Date: 2020/7/4 14:43
         */
        public function get($url, Closure $callback = null, $params = [])
        {
            if (!Request::instance()->isGet()) {
                return null;
            }

            list($success, $result) = controller($url, $params);
            if ($callback) {
                $callback($success, $result);
                return null;
            } else {
                return [$success, $result];
            }
        }

        public function query($url, Closure $callback, $params = [])
        {
            list($success, $result) = controller($url, $params);
            if ($callback) {
                $callback($success, $result);
                return null;
            } else {
                return [$success, $result];
            }
        }
    }

    /**
     * 嵌入页面代码工具类
     * Class Embed
     */
    class Embed
    {
        /**
         * @var EmbedJs
         */
        public static $js;

        /**
         * @var EmbedReq
         */
        public static $req;

        /**
         * Created by PhpStorm.
         * @param $url
         * @param array $params
         * @return mixed
         * @author QiuMinMin
         * Date: 2020/8/3 13:02
         */
        public static function api($url, $params = [])
        {
            list($success, $result) = controller($url, $params);
            return $result;
        }

        /**
         * 地址引用方法，当ID存在则$url为后缀url，否则url为调用url的参数
         * Created by PhpStorm.
         * @param $id
         * @param $url
         * @return mixed|string|null
         * @author QiuMinMin
         * Date: 2020/7/1 20:50
         */
        public static function ref($id, $url = '')
        {
            $value = null;
            $urlArray = config('ref');
            foreach ($urlArray as $urlId => $urlAddress) {
                if ($urlId == $id) {
                    $value = $urlAddress;
                    break;
                }
            }
            return $value ? $value . $url : self::url($url);
        }

        /**
         * url组装
         * Created by PhpStorm.
         * @param $expression
         * @return string
         * @author QiuMinMin
         * Date: 2020/7/4 14:45
         */
        public static function url($expression)
        {
            if (strpos($expression, '/') != 0) {
                $expression = '/' . $expression;
            }
            return '/apino.php' . $expression;
        }

        /**
         * 生成标签
         * Created by PhpStorm.
         * @param $name
         * @param array $attrs
         * @param array|string $html
         * @return string
         * @author QiuMinMin
         * Date: 2020/8/8 18:54
         */
        public static function tag($name, $attrs = [], $html = '')
        {
            if (gettype($html) == 'array') {
                $html = implode('', $html);
            }

            $attrsString = '';
            foreach ($attrs as $name => $value) {
                $attrsString .= sprintf(' %s = "%s"', $name, $value);
            }
            return sprintf('<%s %s>%s</%s>', $name, $attrsString, $html, $name);
        }

        public function __get($name)
        {
            if (empty(self::$name)) {
                $class = 'Embed' . ucfirst($name);
                self::$name = new $class();
            }
            return self::$name;
        }
    }


}

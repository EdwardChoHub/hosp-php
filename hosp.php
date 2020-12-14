<?php

namespace hosp;

use Exception;
use PDO;
use ReflectionException;
use ReflectionFunction;

init();

/** 用户自定义注册和变更配置代码，框架升级替换函数内容即可 */
function custom()
{
    //替换数据库配置
    config('database', [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'tran_group',
        'username' => 'root',
        'password' => 'root',
        //表名前缀
        'tableNamePrefix' => 'hisi_',
    ]);
    action('/user/updatePassword', function($name){

    });

    action('/user/getUserInfo', function(){

    });

    action('/user/userInfo', function(){


    });

    model('/user/selectUserById', function(){

    });

}

/** 应用配置 */
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
    //格式请求真删锁(仅对格式请求有效)
    'true_delete_lock' => [
        //开关
        'switch' => true,
        //密钥
        'key' => '123123123213',
        //入参值
        'param' => 'trueDeleteKey'
    ],
]);
/** 数据库配置 */
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
/** 邮箱配置 */
config('smtp', [
    'server' => "",
    'port' => 22,
    'user' => '933',
    'pass' => '2123',
    'debug' => false,
    'type' => 'HTML',
]);
/** 配置路由 */
config('route', [
    '/' => function () {
        echo '欢迎使用Hosp';
        die();
    },
]);
/** 角色权限声明，1则为全部权限，0或[]组则无任何权限 */
config('authority', [
    /** 角色权限控制(支持静态权限和动态权限) */
    'role' => [
        //用户自定义权限
    ],
//动态权限写闭包返回结果为数组即可
//    'role' => function(){
//
//    },
    /** 白名单 */
    'except' => [

    ],
]);
/** 请求配置 */
config('request', [
    /** 接口自动校验器，require(必填)|int(整形)|float(浮点行)|mobile(手机号)|email(邮箱)|具体值 */
    'validate' => [],
    /** 入参自动转换器 */
    'converter' => [],
]);
/** 控制器方法注册 */
config('action', [
    '/system/upload_image' => [
        'api' => API_UPLOAD,
        'fileParam' => 'img',
        'maxSize' => 4096,
        'dir' => '/upload/img',
    ],
    '/system/upload_php' => [
        'api' => API_UPLOAD,
        'fileParam' => 'file',
        'maxSize' => 4096,
        'dir' => '/upload/file',
    ],
    '/system/verify_code_image' => [
        'api' => API_VERIFY_CODE_IMAGE,
        'param' => 'code',
        'number' => 4,
        'width' => 200,
        'height' => 100,
    ],
    '/system/verify_code_email' => [
        'api' => API_VERIFY_CODE_EMAIL,
        'param' => 'code',
        'number' => 4,
        'title' => '验证码信息',
        'content' =>  '您的验证码位#code#,请5分钟内使用！',
        'code_template' => '#code#',
    ],
    '/system/verify_code_mobile' => [
        'api' => API_VERIFY_CODE_MOBILE,
        'param' => 'code',
        'number' => 4,
    ],
    '/system/token' => [
        'api' => API_TOKEN,
        'switch' => true,
        //入参名
        'param' => 'token',
        //获取TOKEN时间间隔
        'interval' => 1,
        //生成函数
        'generator' => function () {
        },
    ],
    '/system/register' => [
        'api' => API_REGISTER,
        /** 表 */
        'table' => 'hisi_table',
        /** ID */
        'id' => 'id',
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
         * @return mixed 返回数组则通过，返回false则登录失败
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
    '/system/login' => [
        'api' => API_LOGIN,
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
    '/system/logout' => [
        'api' => API_LOGOUT,
        'callback' => function () {

        },
    ],
    '/system/check_login' => [
        'api' => API_CHECK_LOGIN,
        'callback' => function () {

        }
    ]
]);
/** 模型方法注册 */
config('model', []);
/** 视图配置 */
config('view', [
    'path' => ROOT_PATH . DS . 'view',
    /** 模板文件 */
    'layout_file' => '',
    /** 模板替换字符 */
    'layout_replace' => '<__CONTENT__>',
    /** 视图缓存 */
    'layout_runtime' => ROOT_PATH . DS . 'runtime' . DS . 'view',
]);
/** 日志配置 */
config('log', [
    /** 日志路径 */
    'path' => ROOT_PATH . DS . 'log',
    /** 写入日志等级最低要求（用于生产环境修改此值减少日志量） */
    'require' => LOG_NORMAL,
]);
/** URL映射 */
config('ref', [

]);
/** 事件钩子 */
config('event', [
    'after_init' => function () {
    },
    'before_router' => function () {
    },
    'after_router' => function () {
    },
    'before_authority' => function () {
    },
    'after_authority' => function () {
    },
    'before_action' => function () {
    },
    'after_action' => function () {
    },
    'before_model' => function(){

    },
    'after_model' => function(){

    },
    'before_hosp' => function(){

    },
    'after_hosp' => function(){

    },
    'before_sql' => function () {
    },
    'after_sql' => function () {
    },
    'before_complete' => function ($data) {
    }
]);
/** 日志钩子 */
config('log_hook', [
    /**
     * 执行于App初始化之前
     */
    'init' => function () {
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
    'route' => function ($action) {
    },
    /**
     * 路由权限拦截结果
     * @var $router Router 当前路由
     * @var $result bool 拦截结果
     */
    'authority' => function ($action, $result) {
    },
    /**
     * 执行控制器方法时自动调用
     * @var $action string 行为名
     * @var $params array 入参
     * @var $result mixed 出参
     */
    'action' => function ($action, $params, $result) {
    },
    /**
     * 执行服务函数时自动调用
     * @var $action string 模型方法
     * @var $params array 入参信息
     * @var $result mixed 出参结果
     */
    'model' => function ($action, $params, $result) {
    },
    /**
     * 执行HOSP执行器钩子
     * @var $express string 表达式
     * @var $params array 入参
     * @var $result mixed 出参
     */
    'hosp' => function ($express, $params, $result) {
    },
    /**
     * 执行sql时调用
     * @var $sql string 执行SQL
     */
    'sql' => function (string $sql) {
    },
    /** 全部完成后调用 */
    'complete' => function ($data) {
    },
]);
/** php注册处理器 */
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

/** 框架执行 */
run();



/** 工具 */

/** 配置检查器 */
function config_checker()
{

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
                callback('event.before_action');

                $params = array_merge(input(), is_array($params) ? $params : []);
                $callback = config("action.$express");
                $result = is_callable($callback) ? $callback($params) : model($express, $params);

                callback('event.after_action');

                return $result;
            }catch (Exception $e){
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

/** 日志
 * @param $content mixed 日志内容
 * @param $filename string 文件名
 */
function log($content, string $filename)
{
    if (!defined('LOG_ID')) {
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
            $logId =
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
            $logId = md5(strval(microtime() . rand(0, 9999)));
        }
        define('LOG_ID', $logId);
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
 * 钩子调用和注册
 * @param $name
 * @param $args callback|array
 * @throws ReflectionException
 */
function hook($name, $args = [])
{
    if (is_callable($args)) {
        config("hook.{$name}", $args);
    } else {
        $function = config("hook.{$name}");
        $rf = new ReflectionFunction($function);
        $rf->invokeArgs($args);
    }
}

/**
 * @notes 函数调用函数
 * @param $name string 函数配置名
 * @param array $args
 */
function callback(string $name, $args = [])
{
    try {
        $reflectFunction = new ReflectionFunction(config($name));
        $reflectFunction->invokeArgs($args);
    } catch (ReflectionException $e) {
    }
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


/** 数据库函数 */

/**
 * @notes 表达式解析器
 * @param string $express
 * @param array $params
 * @return mixed
 */
function hosp(string $express, $params = [])
{

    callback('event.before_hosp');

    $result = hosp_exec($express, $params);

    callback('event.after_hosp');

    return $result;
}

function hosp_exec()
{

}

function hosp_select()
{

}

function hosp_update()
{

}

function hosp_insert()
{

}

function hosp_delete()
{

}

function mysql_select()
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

function mysql_count()
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

function mysql_column()
{

}

function mysql_delete()
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

function mysql_update()
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

function mysql_insert($data)
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

function mysql_insert_all($array)
{
    array_map(function ($data) {
        mysql_insert($data);
    }, $array);
}

/**
 * @notes 查单
 * @param $id
 * @param $field
 * @param $pk
 * @author EdwardCho
 */
function mysql_get($id, $field, $pk)
{

}

function mysql_field()
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

/**
 * @notes 执行SQL语句
 * @param $sql
 * @return array|int
 */
function mysql_exec($sql)
{

    try {
        $this->connect();

        $result = mysql()->prepare($sql);

        $result->execute();

        $errorInfo = $result->errorInfo();

        if ($errorInfo[2] != null) {
            error($errorInfo[2]);
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
        error($e->getMessage());
        return false;
    }

    return $result;
}

/**
 * @notes 获取PDO实例
 * @return PDO
 * @author EdwardCho
 */
function mysql()
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


/** 入参工具 */

/**
 * 获取入参
 * @param $name
 * @param null $default
 * @return mixed|null
 */
function input($name = null, $default = null)
{
    //懒加载，使用到入参信息才会去获取
    if (!defined('REQUEST_PARAMS')) {
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
        define('REQUEST_PARAMS', array_merge($urlParams, $_GET, $_POST, $json));
    }

    if ($name == null) {
        return $this->params;
    }
    return isset(REQUEST_PARAMS[$name]) ? REQUEST_PARAMS[$name] : $default;
}

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
 * @notes 上传文件保存
 * @param $name string 文件名
 * @param $destination string 目标文件路径
 * @return bool
 * @author EdwardCho
 */
function file_move($name, string $destination)
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

/**
 * @notes 上传文件有效性校验
 * @param $name string 文件名
 * @param array $extArray
 * @param null $maxSize
 * @return bool
 * @author EdwardCho
 */
function file_valid(string $name, $extArray = [], $maxSize = null)
{
    $temp = explode(".", $name);
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

/** 路由解析 */
function route()
{

}

/** 出参工具 */

/**
 * @notes 输出JSON对象
 * @param $code
 * @param $data
 * @param $msg
 * @throws ReflectionException
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

    echo json_encode($object, JSON_UNESCAPED_UNICODE);
    die();
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
    log($content, 'error');
    Response::error($content);
    die();
}


/** API操作 */

/**
 * @notes 内置API拦截器
 * @param $controller
 * @param $action
 * @return false|null
 */
function api_interceptor($controller, $action)
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
 * @notes API TOKEN校验
 * @param Router $router
 * @return array
 */
function api_token(Router $router)
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
 * @notes 系统预设API处理器
 * @param $api
 * @param array $vars
 * @return array|bool
 * @author EdwardCho
 */
function api_handler($api, $vars = [])
{
    switch ($api) {
        case API_UPLOAD:
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
        case API_VERIFY_CODE:
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
        case API_TOKEN:
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
        case API_REGISTER:
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
            if (get_class($safety) == callback::class) {
                list($result, $error) = $safety(input());
                if (!$result) {
                    return Result::error($error);
                }
            }

            if (get_class($passwordEncode) == callback::class) {
                $password = $passwordEncode($password);
            }
            $data = [];
            $values = $c['values'];
            if (get_class($values) == callback::class) {
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
        case API_LOGIN:
            $appUserConfig = config('app.user');

            $nameParam = $c['username'];
            $passParam = $c['password'];
            $passEncode = $c['passwordEncode'];
            list($table, $field) = explode('.', $appUserConfig['id']);
            $safety = $c['safety'];
            $success = $c['success'];
            $error = $c['error'];

            //用户自定义校验
            if (is_callable($safety)) {
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
            if (is_callable($passEncode)) {
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
        case API_LOGOUT:
            return User::logout() ? Result::success() : Result::error();
        case API_CHECK_LOGIN:
            return !empty(User::id());

    }
}


/** 验证码功能 */

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
 * @notes 邮箱验证码
 * @param $content
 * @param $title
 * @return bool
 */
function verify_code_email($content, $title)
{
    //内容模板替换#code#关键字
    return config('handler.smtp_sender')(array_merge(config('smtp'), [
        'title' => $title,
        'content' => $content
    ]));
}

/** 手机验证码
 * @param $params array
 * @return bool
 */
function verify_code_mobile($params = [])
{
    return config('handler.sms_sender')($params);
}

/**
 * @notes 校验验证码
 * @param $type
 * @return array
 */
function verify_code_check($type)
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


/** 登录用户操作 */

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
 * @notes 角色
 * @param string|int $role
 * @return string|void
 */
function user_role($role = '')
{
    return session('user_role', $role);
}

/**
 * @notes 权限
 * @
 * @param array|bool|mixed $authority
 * @return array|bool|void
 */
function user_authority($authority = '')
{
    return session('user_authority', $authority);
}

/**
 * @notes 登录
 * @param $id
 * @param null $role
 * @param array $authority
 */
function user_login($id, $role = '', $authority = [])
{
    user_id($id);
    user_role($role);
    user_authority($authority);
}

/**
 * @notes 退出登录
 */
function user_logout()
{
    user_id(null);
    user_role(null);
    user_authority(false);
    session_destroy();
}

/**
 * @notes 全部权限(true)、无任何权限(false)、指定权限列表(array)
 * @param $url
 * @return bool
 */
function user_access($url)
{
    return is_bool(user_authority()) ? user_authority() : in_array($url, user_authority());
}

/**
 * @notes 用户信息同步
 */
function user_sync()
{
    $auth = config('authority.auth');
    session('user_authority', array_merge(
        is_callable($auth) ? $auth() : $auth,
        config('authority.except')
    ));
}

/** 返回结果封装 */

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


/** 框架初始化 */
function init(){
    //关闭严格模式
    ini_set("display_errors", 0);

    define('MICRO_TIME', microtime(true));
    define('TIME', intval(MICRO_TIME));

    /** composer引入 */
    is_file(__DIR__ . '/vendor/autoload.php') && require_once __DIR__ . '/vendor/autoload.php';

    define("DS", DIRECTORY_SEPARATOR);
    define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
    define("UPLOAD_PATH", ROOT_PATH . DS . "upload");
    define("LOG_PATH", ROOT_PATH . DS . "log");

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

    /** 内置预设API */
    define('API_UPLOAD', 10001);
    define('API_TOKEN', 10003);
    define('API_REGISTER', 10004);
    define('API_LOGIN', 10005);
    define('API_LOGOUT', 10006);
    define('API_CHECK_LOGIN', 10007);
    define('API_VERIFY_CODE_IMAGE', 10008);
    define('API_VERIFY_CODE_EMAIL', 10009);
    define('API_VERIFY_CODE_MOBILE', 10010);

    /** 日志类型 */
    define('LOG_NORMAL', 1);
    define('LOG_WARN',  3);
    define('LOG_ERROR', 5);

    if (config('app.debug')) {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
        //配置检查员(app.debug开启后才会执行)
//        ConfigChecker::run();
    }

    //加载用户配置引入文件
    $files = config('include_file');
    if (is_array($files)) {
        foreach ($files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}

/** 框架执行 */
function run()
{
    //注册信息
    //注册错误处理函数
    set_error_handler(config('handler.set_error_handler'));
    set_exception_handler(config('handler.set_exception_handler'));
    register_shutdown_function(config('handler.register_shutdown_function'));


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
            if (is_array($config) && count($config) == 0) {
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
        public static function build($trueTableName, $expression, $params = [])
        {
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
            if (empty($this->sql)) {
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
        protected $error;

        public function error($error = null)
        {
            if ($error === null) {
                return $this->error;
            }

            $this->error = $error;
            return false;
        }

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
         * @param callback|null $handler callback
         * @param array $params
         * @return mixed|null
         * @author QiuMinMin
         * Date: 2020/6/5 17:56
         */
        private function getSubExpValue($key, callback $handler = null, $params = [])
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

//
//    /**
//     * 配置检查工具
//     * Class AppConfigInspectionTool
//     */
//    class ConfigChecker
//    {
//
//        const callback = callback::class;
//        const INT = 'integer';
//        const STR = 'string';
//        const BOOL = 'boolean';
//        const ARR = 'array';
//        const FLOAT = 'float';
//        const DOUBLE = 'double';
//
//        public static function run()
//        {
//            if (empty($GLOBALS['config_checker'])) {
//                return;
//            }
//
//            $GLOBALS['config_checker'] = 1;
//
//            try {
//                $self = new self();
//
//                $self->checkAppConfig();
//                $self->checkDatabaseConfig();
//                $self->checkSmtpConfig();
//                $self->checkRouteConfig();
//                $self->checkAuthorityConfig();
//                $self->checkApiConfig();
//            } catch (Exception $e) {
//                Response::error($e->getMessage());
//                die();
//            }
//        }
//
//        private function throwConfigError($configNickName, $configName, $typeName)
//        {
//            throw new Exception("配置{$configNickName}({$configName})必须存在且值类型为{$typeName}");
//        }
//
//        private function checkArray($vc, $prefixName = '', $prefixNick = '')
//        {
//            $name = $prefixName . $vc['name'];
//            $nick = $prefixNick . $vc['nick'];
//            $this->check($name, $vc['nick'], $vc['type']);
//            foreach ($vc['children'] as $child) {
//                $this->checkArray($child, $name . '.', $nick);
//            }
//        }
//
//        private function check($configName, $configNickName, $type)
//        {
//            $var = [
//                self::BOOL => '布尔型',
//                self::INT => '整型',
//                self::STR => '字符串型',
//                self::ARR => '数组型',
//                self::FLOAT => '浮点型',
//                self::DOUBLE => '双精度型',
//            ];
//            $class = [
//                self::callback => callback::class
//            ];
//
//            if (in_array($type, array_keys($var))) {
//                if (config($configName) === null || gettype(config($configName)) !== $type) {
//                    $this->throwConfigError($configNickName, $configName, $var[$type]);
//                }
//            } elseif (in_array($type, array_keys($class))) {
//                if (config($configName) === null || get_class(config($configName)) !== $type) {
//                    $this->throwConfigError($configNickName, $configName, $class[$type]);
//                }
//            } else {
//                $this->throwConfigError($configNickName, $configName, $type);
//            }
//        }
//
//        private function checkAppConfig()
//        {
//            $this->checkArray($this->c('app', '应用', self::ARR, [
//                $this->c('debug', '调试模式', self::BOOL),
//                $this->c('response', '出参配置', self::ARR, [
//                    $this->c('page', '分页', self::ARR, [
//                        $this->c('total', '记录总数出参名', self::STR),
//                        $this->c('list', '记录数据出参名', self::STR)
//                    ]),
//                ]),
//                $this->c('trueDeleteLock', '真删锁', self::ARR, [
//                    $this->c('switch', '验证开关', self::BOOL),
//                    $this->c('key', '值', self::STR),
//                    $this->c('param', '入参名', self::STR),
//                ]),
//                $this->c('user', '用户', self::ARR, [
//                    $this->c('id', 'ID', self::STR),
//                    $this->c('role', '角色', self::callback),
//                    $this->c('auth', '权限', self::ARR, [
//                        $this->c('mode', '模式', self::STR),
//                        $this->c('source', '获取源', self::callback),
//                        $this->c('except', '权限白名单', self::ARR),
//                    ]),
//                ])
//            ]));
//        }
//
//        private function checkDatabaseConfig()
//        {
//            $this->checkArray($this->c('database', '数据库', self::ARR, [
//                $this->c('type', '类型', self::STR),
//                $this->c('host', '地址', self::STR),
//                $this->c('port', '端口', self::STR),
//                $this->c('dbname', '库名', self::STR),
//                $this->c('username', '用户名', self::STR),
//                $this->c('password', '密码', self::STR),
//                $this->c('tableNamePrefix', '表名前缀', self::STR),
//                $this->c('charset', '编码方式', self::STR),
//                $this->c('autoQuery', '自动关联查询', self::ARR),
//                $this->c('autoCompleteInsert', '自动补全插入', self::BOOL),
//                $this->c('autoCompleteNoNull', '自动填充字段不存在null值', self::BOOL),
//                $this->c('autoTimestamp', '自动插入时间戳', self::ARR, [
//                    $this->c('switch', '开关', self::BOOL),
//                    $this->c('createTime', '创建时间', self::ARR),
//                    $this->c('updateTime', '修改时间', self::ARR),
//                    $this->c('generator', '时间戳值', self::INT)
//                ]),
//                $this->c('falseDeleteValue', '表假删除状态', self::ARR)
//            ]));
//        }
//
//        private function checkSmtpConfig()
//        {
//            $this->checkArray($this->c('smtp', '邮箱', self::ARR, [
//                $this->c('server', '域名', self::STR),
//                $this->c('port', '端口', self::INT),
//                $this->c('user', '授权用户名', self::STR),
//                $this->c('pass', '授权密码', self::STR),
//                $this->c('debug', '调试模式', self::BOOL),
//                $this->c('type', '内容格式', self::STR),
//            ]));
//        }
//
//        private function checkRouteConfig()
//        {
//            $this->checkArray($this->c('route', '路由', self::ARR));
//        }
//
//        private function checkAuthorityConfig()
//        {
//            $this->checkArray($this->c('authority', '角色权限', self::ARR, [
//                $this->c(GUEST, '游客', self::ARR),
//            ]));
//        }
//
//        private function checkApiConfig()
//        {
//            $this->checkArray($this->c('api', '预设接口', self::ARR, [
//                $this->c('util', '通用', self::ARR, [
//                    $this->c('upload', '上传配置', self::ARR, [
//                        $this->c('image', '图片', self::ARR, [
//                            $this->c('fileParam', '文件入参', self::STR),
//                            $this->c('maxSize', '大小最大限制', self::INT),
//                            $this->c('dir', '存储定制', self::STR),
//                        ]),
//                        $this->c('php', 'PHP', self::ARR, [
//                            $this->c('fileParam', '文件入参', self::STR),
//                            $this->c('maxSize', '大小最大限制', self::INT),
//                            $this->c('dir', '存储定制', self::STR),
//                        ]),
//                    ]),
//                    $this->c('verifyCode', '验证码', self::ARR, [
//                        $this->c('param', '入参名', self::STR),
//                        $this->c('sence', '场景', self::ARR),
//                    ]),
//                    $this->c('token', 'TOKEN', self::ARR, [
//                        $this->c('switch', '开关', self::BOOL),
//                        $this->c('param', '入参名', self::STR),
//                        $this->c('generator', '生成器', self::callback)
//                    ]),
//                ]),
//                $this->c('user', '用户', self::ARR, [
//                    $this->c('register', '注册', self::ARR, [
//                        $this->c('username', '用户名入参名', self::STR),
//                        $this->c('password', '密码入参名', self::STR),
//                        $this->c('confirmPassword', '确认密码入参名', self::STR),
//                        $this->c('passwordEncode', '密码加密回调', self::callback),
//                        $this->c('safety', '前置安全验证回调', self::callback),
//                        $this->c('success', '成功后置回调', self::callback),
//                        $this->c('error', '失败后置回调', self::callback),
//                    ]),
//                    $this->c('login', '登录', self::ARR, [
//                        $this->c('username', '用户名入参名', self::STR),
//                        $this->c('password', '密码入参名', self::STR),
//                        $this->c('passwordEncode', '密码加密回调', self::callback),
//                        $this->c('safety', '前置安全验证回调', self::callback),
//                        $this->c('success', '成功后置回调', self::callback),
//                        $this->c('error', '失败后置回调', self::callback),
//                    ]),
//                    $this->c('logout', '登出后置回调', self::callback),
//                    $this->c('checkLogin', '检查登录验证回调', self::callback),
//                ])
//            ]));
//        }
//
//        //校验配置
//        private function c($name, $nick, $type, $children = [])
//        {
//            return [
//                'name' => $name,
//                'nick' => $nick,
//                'type' => $type,
//                'children' => $children
//            ];
//        }
//    }



    //载入用户自定义代码
    custom();

    //页面不进行直接渲染
    ob_start();



    //初始化钩子
    callback('event.after_init');

    /** 事件钩子 */
    callback('event.before_router');

    //获取路由信息
    $router = Router::route();

    /** 事件钩子 */
    callback('event.after_router');

    //校验TOKEN(全指定或全不校验)
    list($success, $result) = api_token($router);
    !$success && json(1, $result);

    //校验验证码(仅指定api校验)
    list($success, $result) = verify_code_check($router);
    !$success && json(1, $result);


    /** 事件钩子 */
    callback('event.before_authority');

    //同步当前用户角色权限信息
    user_sync();

    //权限判断
    $result = user_access($router->url());

    /** 事件钩子 */
    callback('event.after_authority');

    $result || json(1, '当前用户没有该权限');

    //内置api拦截
    $result = api_interceptor($router->controller(), $router->action());
    if ($result !== false) {
        $result[0] ? json(0, $result[1]) : json(1, $result[1]);
    }

    $params = input();

    //入参校验器
    list($success, $result) = validate($router->url(), $params);
    !$success && json(1, $result);

    //入参转换器
    $callback = config("input_converter.{$router->url()}");
    $params = is_callable($callback) ? $callback($params) : $params;

    callback('event.before_action');

    //调用相应方法
    $object = action($router->url(), $params);

    callback('event.before_action');

    //响应内容
    json(0, $object);

    //页面缓存输出
    ob_end_flush();

    callback('event.before_complete');

}
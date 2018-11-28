<?php
define('ACCESSKEYID', ''); // $accessKeyId替换成您自己的Access Key ID.
define('SECRET', ''); // $secret替换成您Access Key ID对应的 Access Key Secret.
define('KEY_TYPE','aliyun'); //固定值aliyun
define('APP_NAME', 'my_test'); // $app_name替换成您想创建的应用名称.
define('SDK_VERSION', 'php_sdk_v2.0.3'); //调用的SDK版本

function dd($params)
{
    echo '<pre>';
    print_r($params);
    exit;
}

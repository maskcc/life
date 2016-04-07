<?php 
require_once("DBInstance.php");
/***
注册管理员
*/
header("Content-Type: application/json;charset=utf-8"); 
if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"管理员名称未填写."}';
        return;
    }

$name = $_REQUEST['name'];

$dbHandler = new DBInstance;
$dbHandler->open();


if(array_key_exists("create", $_REQUEST))
{
    if (!isset($_REQUEST['passwd'])) {
        echo '{"success":false,"msg":"管理员密码."}';
        return;
    }
    $passwd = $_REQUEST['passwd'];
    
    $ret = $dbHandler->create_account($name, $passwd);
    if(1 === $ret){
        echo '{"success":false,"msg":"账户名重复."}';
        return;
    }

    if(-1 === $ret){
        echo '{"success":false,"msg":"查询数据库失败."}';
        return;
    }
    echo '{"success":true,"msg":"创建鹳狸猿账户成功."}';
    return;
    
    
}




if(array_key_exists("del", $_REQUEST))
{     
    $ret = $dbHandler->del_account($name);
    if(1 === $ret){
        echo '{"success":false,"msg":"用户名不存在."}';
        return;
    }

    if(-1 === $ret){
        echo '{"success":false,"msg":"查询数据库失败."}';
        return;
    }
    echo '{"success":true,"msg":"删除鹳狸猿账户成功."}';
    return;
    
}

if(array_key_exists("login", $_REQUEST))
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"管理员密码."}';
        return;
    }
    if (!isset($_REQUEST['passwd'])) {
        echo '{"success":false,"msg":"管理员密码."}';
        return;
    }
    $name = $_REQUEST['name'];
    $passwd = $_REQUEST['passwd'];

    $ret = $dbHandler->login($name, $passwd);
    if(1 === $ret){
        echo '{"success":false,"msg":"账号名或密码不正确."}';
        return;
    }

    if(-1 === $ret){
        echo '{"success":false,"msg":"查询数据库失败."}';
        return;
    }
    echo '{"success":true,"msg":"登录成功,你好!."}';

    
    //跳转到新网页
//    Header("HTTP/1.1 303 See Other"); 
//    Header("Location: control.html"); 
    exit;    
    
}
 ?>
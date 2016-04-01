<?php 
require_once("DBInstance.php");

$dbHandler = new DBInstance;
$dbHandler->open();

/*添加员工*/
if(array_key_exists("add", $_REQUEST))
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"请填写员工名字!"}';
        return;
    }  
    if (!isset($_REQUEST['sex'])) {
        echo '{"success":false,"msg":"请选择员工性别!"}';
        return;
    }    
    $name = $_REQUEST['name'];
    $sex = $_REQUEST['sex'];
    $ret = $dbHandler->add_staff($name, $sex);
    if(1 === $ret){
        echo '{"success":false,"msg":"员工姓名重复!"}';
        return;
    }

    if(-1 === $ret){
        echo '{"success":false,"msg":"查询数据库失败."}';
        return;
    }
    echo '{"success":true,"msg":"添加员工成功!"}';
    return;
    
    
}



if(array_key_exists("hide", $_REQUEST))
{     
    $name = $_REQUEST['name'];
    $ret = $dbHandler->hide_staff($name);
    if(1 === $ret){
        echo '{"success":false,"msg":"用户名不存在."}';
        return;
    }

    if(-1 === $ret){
        echo '{"success":false,"msg":"查询数据库失败."}';
        return;
    }
    echo '{"success":true,"msg":"将员工标记为隐藏成功."}';
    return;
    
}


 ?>
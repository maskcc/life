<?php 
require_once("DBInstance.php");

$dbHandler = new DBInstance;
$dbHandler->open();

/*添加项目*/
if(array_key_exists("add", $_REQUEST))
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"请填写项目名称!"}';
        return;
    }  
       
    $name = $_REQUEST['name'];    
    $ret = $dbHandler->add_project($name);
    if(1 === $ret){
        echo '{"success":false,"msg":"项目名称重复!"}';
        return;
    }

    if(-1 === $ret){
        echo '{"success":false,"msg":"查询数据库失败."}';
        return;
    }
    echo '{"success":true,"msg":"添加项目成功!"}';
    return;
    
    
}


/*过时的项目不再显示*/
if(array_key_exists("hide", $_REQUEST))
{     
    $name = $_REQUEST['name'];
    $ret = $dbHandler->hide_project($name);
    if(1 === $ret){
        echo '{"success":false,"msg":"项目不存在."}';
        return;
    }

    if(-1 === $ret){
        echo '{"success":false,"msg":"查询数据库失败."}';
        return;
    }
    echo '{"success":true,"msg":"将项目标记为隐藏成功."}';
    return;
    
}


 ?>
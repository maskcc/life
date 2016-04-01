<?php 
require_once("DBInstance.php");

$dbHandler = new DBInstance;
$dbHandler->open();

/*管理项目进度*/

/*给员工添加工作进度*/
if(array_key_exists("add", $_REQUEST))
{
    if (!isset($_REQUEST['staff'])) {
        echo '{"success":false,"msg":"请填写员工名字!"}';
        return;
    } 
    if (!isset($_REQUEST['project'])) {
        echo '{"success":false,"msg":"请填写项目名称!"}';
        return;
    }  
    if (!isset($_REQUEST['count'])) {
        echo '{"success":false,"msg":"请填写项目数量!"}';
        return;
    }   
       
    $staff = $_REQUEST['staff'];
    $project = $_REQUEST['project'];
    $count = $_REQUEST['count'];


    /*这里的判断逻辑是否应该加到db函数里面,但是提示信息页不好写那边*/
    /*没有该员工*/
    $ret = $dbHandler->add_progress($staff, $project, $count);
    if (1 === $ret) {
        echo '{"success":false,"msg":"员工不存在!"}';
        return;
    }
    if (2 === $ret) {
        echo '{"success":false,"msg":"项目不存在!"}';
        return;
    }
    if(3 === $ret){    
        echo '{"success":false,"msg":"你仿佛在特意逗我笑,输入数据非法!"}';
        return;
    }

    
    echo '{"success":true,"msg":"添加工作进度成功!"}';
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
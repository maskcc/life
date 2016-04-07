<?php 
require_once("DBInstance.php");

$dbHandler = new DBInstance;
$dbHandler->open();

/*添加项目*/
/*if(array_key_exists("add", $_REQUEST))
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"请填写项目名称!"}';
        return;
    }  
       
    $name = $_REQUEST['name'];    */

function add_project($name)
{
    $dbHandler = new DBInstance;
    $dbHandler->open();

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
    


function hide_project($name)
{
/*过时的项目不再显示*/
    $dbHandler = new DBInstance;
    $dbHandler->open();
    
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


/*返回所有项目列表*/
function list_project()
{
    $dbHandler = new DBInstance;
    $dbHandler->open();

    $ret = $dbHandler->get_projectList();

    if(false === $ret){
        /*当数组为空时,这里的判断写 if(!$ret)是有问题的,并没有失败,只是没有数据*/
        echo '{"success":false,"msg":"查询失败."}';
        return;
    }
    /*组装成json应答*/
    $result['success'] = true;
    $result['msg'] = $ret;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

}


 ?>
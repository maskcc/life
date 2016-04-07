<?php 
require_once("DBInstance.php");



/*添加员工*/
function add_staff($name, $sex)
{
    $dbHandler = new DBInstance;
    $dbHandler->open();
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



function hide_staff($name)
{ 
    $dbHandler = new DBInstance;
    $dbHandler->open();
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

/*返回所有员工列表*/
function list_staff()
{
    $dbHandler = new DBInstance;
    $dbHandler->open();

    $ret = $dbHandler->get_staffList();

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
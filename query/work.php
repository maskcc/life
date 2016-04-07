<?php 
require_once("DBInstance.php");


/*管理项目进度*/

/*给员工添加工作进度*/
function add_progress($staff, $project, $count)
{
    $dbHandler = new DBInstance;
    $dbHandler->open();

    
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


/*过时的项目不再显示
if(array_key_exists("hide", $_REQUEST))
{     
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
    
}*/


/*显示当前所有员工进度*/
function show_staff($year, $month) 
{ 
    $dbHandler = new DBInstance;
    $dbHandler->open();
    $ret = $dbHandler->show_staff($year,$month);
    if(false === $ret){
        /*当数组为空时,这里的判断写 if(!$ret)是有问题的,并没有失败,只是没有数据*/
        echo '{"success":false,"msg":"查询失败."}';
        return;
    }
    /*组装成json应答*/
    $result['success'] = true;
    $result['msg'] = $ret;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

     return;
    /*PHP5.4版本，已经给Json新增了一个选项: JSON_UNESCAPED_UNICODE。加上这个选项后，就不会自动把中文编码了。
    另，由于 json_encode 和 json_decode只支持utf-8编码的字符，GBK的字符要用json就得转换一下，附自己写的GBK转UTF-8的代码：*/

    /*
    字符串GBK转码为UTF-8，数字转换为数字。
    
    function ct2($s){
        if(is_numeric($s)) {
            return intval($s);
        } else {
            return iconv("GBK","UTF-8",$s);
        }
    }*/
    /*
        批量处理gbk->utf-8
   
    function icon_to_utf8($s) {

      if(is_array($s)) {
        foreach($s as $key => $val) {
          $s[$key] = icon_to_utf8($val);
        }
      } else {
          $s = ct2($s);
      }
      return $s;

    }

    echo json_encode(icon_to_utf8("厦门"));
    */    
}


/*显示当前员工进度*/

function show_staff_detail($staff, $year, $month)
{
    $dbHandler = new DBInstance;
    $dbHandler->open();
    $ret = $dbHandler->show_staff_detail($staff, $year, $month);
    if(false === $ret){
        echo '{"success":false,"msg":"查询显示当前员工进度失败."}';
        return;
    }
    /*组装成json应答*/
    $result['success'] = true;
    $result['msg'] = $ret;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

    return;
    
}


/*显示当前所有项目进度*/

function show_project($year, $month)
{
    $dbHandler = new DBInstance;
    $dbHandler->open();
    $ret = $dbHandler->show_projects($year, $month);
    if(false === $ret){
        echo '{"success":false,"msg":"查询当前所有项目进度失败."}';
        return;
    }
    /*组装成json应答*/
    $result['success'] = true;
    $result['msg'] = $ret;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

    return;
    
}

function set_goodCount($year, $month, $count, $timeShow)
{
    $dbHandler = new DBInstance;
    $dbHandler->open();
    $ret = $dbHandler->set_goodCount($year, $month, $count, $timeShow);
    if(false === $ret){
        echo '{"success":false,"msg":"设置失败."}';
        return;
    }elseif (1 === $ret) {
        echo '{"success":false,"msg":"请填写正确的数量."}';
        return;
    }elseif (2 === $ret) {
        echo '{"success":false,"msg":"数量应该在[-1000,1000]."}';
        return;
    }
    /*组装成json应答*/
    $result['success'] = true;
    $result['msg'] = "设置成功";
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

    return;
}


function load_goodCount($year, $month)
{
    $dbHandler = new DBInstance;
    $dbHandler->open();
    $ret = $dbHandler->load_goodCount($year, $month);
    if(false === $ret){
        echo '{"success":false,"msg":"查询失败."}';
        return;
    }
    /*组装成json应答*/
    $result['success'] = true;
    $result['msg'] = $ret;
    echo json_encode($result, JSON_UNESCAPED_UNICODE);

    return;
}


 ?>
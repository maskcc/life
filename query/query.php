<?php
require_once("project.php");
require_once("staff.php");
require_once("work.php");
/*protocols
========================================
添加项目
protocol = 1;
参数:名称
    deal_add_project()
    ?name=world;
    
隐藏项目
protocol = 2;
参数:名称
    deal_hide_project()
    ?name=jack;
    
    
    
    
===================================================
添加员工
protocol = 3;
参数:名称, 性别
    deal_add_staff()
    ?name=jack&sex=1
    
隐藏员工
protocol = 4;
参数:名称
    deal_hide_staff()
    ?name=jack




===================================================    
添加项目进度
参数:角色名称,项目名称,数量
protocol = 5;
    deal_add_progress()
    ?staff=jack&project=world&count=6
    
    
显示所有角色项目进度
参数:年费,月份
protocol = 6;
    deal_show_staff()
    ?year=2016&month=4
    
    
显示角色详细信息
参数:角色的id, 年费,月份
protocol = 7;
    deal_show_staff_detail()
    ?staff_id=1&year=2016&month=4
    
    
显示所有项目进度
参数:年费,月份
protocol = 8;
    deal_show_project()
    ?year=2016&month=4
 


==================================================== 
    
protocol = 9;
显示所有角色名单
参数:无
    deal_list_staff();

protocol = 10;
显示所有的项目名单
参数:无
    deal_list_project();



设置优秀的数量    
protocol = 11;
参数:年份,月份,数量,字符串显示上面日期显示的文字
    deal_set_goodCount();

获取当月优秀的数量   
protocol = 12;
参数:年份,月份
    deal_load_goodCount();

    
protocol = 13;
protocol = 14;
protocol = 15;
*/
if (!isset($_REQUEST['protocol'])) {
    echo '{"success":false,"msg":"协议格式不对!"}';
    return;
}

$protocol_id = $_REQUEST['protocol'];
switch($protocol_id)
{
    case 1:
        deal_add_project();
        break;
    
    case 2:
        deal_hide_project();
        break;
    
    case 3:
        deal_add_staff();
        break;
    
    case 4:
        deal_hide_staff();
        break;
    
    case 5:
        deal_add_progress();
        break;
    
    case 6:
        deal_show_staff();
        break;

    case 7:
        deal_show_staff_detail();
        break;
    
    case 8:
        deal_show_project();
        break;
    
    case 9:
        deal_list_staff();
        break;
    
    case 10:
        deal_list_project();
        break;
    
    case 11:
        deal_set_goodCount();
        break;
    
    case 12:
        deal_load_goodCount();
        break;
       
    case 13:
        break;
    case 14:
        break;
    case 15:
        break;
}

function deal_add_project()
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"请填写项目名称!"}';
        return;
    }  
       
    $name = trim($_REQUEST['name']);
    add_project($name);
    
}

function deal_hide_project()
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"请填写项目名称!"}';
        return;
    }  
       
    $name = $_REQUEST['name'];
    hide_project($name);
    
}


function deal_add_staff()
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"请填写员工名字!"}';
        return;
    }  
    if (!isset($_REQUEST['sex'])) {
        echo '{"success":false,"msg":"请选择员工性别!"}';
        return;
    }    
    $name = trim($_REQUEST['name']);
    $sex = $_REQUEST['sex'];
    add_staff($name, $sex);
}


function deal_hide_staff()
{
    if (!isset($_REQUEST['name'])) {
        echo '{"success":false,"msg":"请填写项目名称!"}';
        return;
    }  
       
    $name = $_REQUEST['name'];
    hide_staff($name);
    
}

function deal_add_progress()
{
    if (!isset($_REQUEST['staff'])) {
        echo '{"success":false,"msg":"请填写员工id!"}';
        return;
    } 
    if (!isset($_REQUEST['project'])) {
        echo '{"success":false,"msg":"请填写项目id!"}';
        return;
    }  
    if (!isset($_REQUEST['count'])) {
        echo '{"success":false,"msg":"请填写项目数量!"}';
        return;
    }   
       
    $staff = $_REQUEST['staff'];
    $project = $_REQUEST['project'];
    $count = $_REQUEST['count'];
    
    add_progress($staff, $project, $count);
}

function deal_show_staff()
{
    if (!isset($_REQUEST['year'])) {
        echo '{"success":false,"msg":"请填写年份!"}';
        return;
    } 
    if (!isset($_REQUEST['month'])) {
        echo '{"success":false,"msg":"请填写月份!"}';
        return;
    } 
    $year = $_REQUEST['year'];
    $month = $_REQUEST['month'];
    show_staff($year, $month);
}
function deal_show_staff_detail()
{   
    if (!isset($_REQUEST['staff_id'])) {
        echo '{"success":false,"msg":"请填写员工id!"}';
        return;
    } 
    if (!isset($_REQUEST['year'])) {
        echo '{"success":false,"msg":"请填写年份!"}';
        return;
    } 
    if (!isset($_REQUEST['month'])) {
        echo '{"success":false,"msg":"请填写月份!"}';
        return;
    } 
    $staff = $_REQUEST['staff_id'];  
    $year = $_REQUEST['year'];
    $month = $_REQUEST['month'];
    show_staff_detail($staff, $year, $month);
}

function deal_show_project()
{     
    if (!isset($_REQUEST['year'])) {
        echo '{"success":false,"msg":"请填写年份!"}';
        return;
    } 
    if (!isset($_REQUEST['month'])) {
        echo '{"success":false,"msg":"请填写月份!"}';
        return;
    } 
    $year = $_REQUEST['year'];
    $month = $_REQUEST['month'];
    show_project($year, $month);
    
}

function deal_list_staff()
{
    list_staff();
}

function deal_list_project()
{
    list_project();
}


function deal_set_goodCount()
{     
    if (!isset($_REQUEST['year'])) {
        echo '{"success":false,"msg":"请填写年份!"}';
        return;
    } 
    if (!isset($_REQUEST['month'])) {
        echo '{"success":false,"msg":"请填写月份!"}';
        return;
    } 
     if (!isset($_REQUEST['count'])) {
        echo '{"success":false,"msg":"请填写项目数量!"}';
        return;
    } 
    if (!isset($_REQUEST['timeShow'])) {
        echo '{"success":false,"msg":"请填进度显示内容!"}';
        return;
    }

    $year = $_REQUEST['year'];
    $month = $_REQUEST['month'];
    $count = $_REQUEST['count'];
    $timeShow = $_REQUEST['timeShow'];
    $rst = explode('/', $timeShow);
    if (2 != count($rst) || !is_numeric($rst[0]) || !is_numeric($rst[1])) {
        echo '{"success":false,"msg":"请填正确的进度显示内容!按照 3/12 来填写!!!"}';
        return;
    }

    set_goodCount($year, $month, $count, $timeShow);
    
}


function deal_load_goodCount()
{     
    if (!isset($_REQUEST['year'])) {
        echo '{"success":false,"msg":"请填写年份!"}';
        return;
    } 
    if (!isset($_REQUEST['month'])) {
        echo '{"success":false,"msg":"请填写月份!"}';
        return;
    } 
  
    $year = $_REQUEST['year'];
    $month = $_REQUEST['month'];

    load_goodCount($year, $month);
    
}
?>
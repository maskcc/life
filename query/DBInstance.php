<?php
/**
 *
 *
 * @version $Id$
 * @copyright 2012
 */
class DBInstance{	
	protected $db_addr="127.0.0.1";
	protected $db_user="root";
	protected $db_password="123456";
	protected $db_name="check_db";	
	protected $conn;
	

	public function open(){		
		ini_set('date.timezone','Asia/Shanghai');
		$this->conn = mysql_connect($this->db_addr, $this->db_user, $this->db_password) or die("Could not connect");
		mysql_select_db($this->db_name, $this->conn) or die("Could not select database");
		
	}

	public function close(){
		mysql_close($this->conn);
	}

	public function create_account($name, $passwd)
	{
		$email = strtolower($name);			
		$sql_str = "select admin_name from admin_tbl where admin_name='$name'";
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return -1;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);
		if($result) {//有数据,查询到重复name
			return 1;   
		}
		
		$sql_str="insert into admin_tbl(admin_name,admin_passwd, level) values('$name','$passwd', 1)";			
		$result = mysql_query($sql_str, $this->conn);
		if (!$result) {	
			return -1;
		} 
		
		return 0;
	}

	
	public function del_account($name)
	{		
		$email = strtolower($name);
		
		$sql_str = "select admin_name from admin_tbl where admin_name='$name'";
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败			
			return -1;  
		}

		$result = mysql_fetch_array($result);
		if(!$result) 
		{			
			return 1; //没找到要删除的角色				 
		}

		$sql_str = "delete from admin_tbl where admin_name = '$name'";
		$result = mysql_query($sql_str);
		if (!$result) {	//查询失败			
			return -1;  
		}		
		return 0;
	}

	public function login($name, $passwd)
	{
		$email = strtolower($name);	

		$sql_str="select admin_name,admin_passwd from admin_tbl where admin_name='$name' and admin_passwd='$passwd'";
		$result=mysql_query($sql_str, $this->conn);

		if (!$result) {	//查询失败			
			return -1;  
		}
				
		$result = mysql_fetch_array($result);
		if(!$result) 
		{			
			return 1; //没有该账号或者密码不正确				 
		}

		return 0;
	}



	/*name是员工名称, sex是员工性别, 0代表男性, 1代表女性*/
	public function add_staff($name, $sex = 0)
	{		
		if ($this->has_staff($name)) {
			return 1;  //有数据,查询到重复name
		}	
		
		//新加的员工肯定是在公司的,stay=0
		$sql_str="insert into staff_tbl(name,sex,stay) values('$name',$sex, 0)";
		$result = mysql_query($sql_str, $this->conn);
		if (!$result) {	
			return -1;
		} 
		
		return 0;
	}

	/*name是员工名称*/
	public function hide_staff($name)
	{		
		if(!$this->has_staff($name)){
			return 1; //没找到该员工,返回1
		}
		
		$sql_str="update staff_tbl set stay=1 where name='$name' ";
		$result = mysql_query($sql_str, $this->conn);
		if (!$result) {	
			return -1;
		}  		
		
		return 0;
	}

	/*判断是否有该员工,没有返回false,有返回员工id*/
	public function has_staff($name)
	{		
		$sql_str = "select staff_id from staff_tbl where name='$name'";
		
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);

		if(!$result){//没有数据,没有这个员工
			return false;
		}

		return (integer)$result['staff_id'];

	}

	/*检查员工是否还在公司*/
	public function is_staff_stay($staff)
	{
		$sql_str = "select stay from staff_tbl where staff_id=$staff";
			
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);

		if(!$result){
			return false;
		}

		/*等于1表示还没员工已经离职*/
		if (1 === (integer)$result['stay']) {
			return false;
		}
		return true;
	}


/*获取项目名称,没有返回false,有返回项目名称*/
	public function get_staffName($staff)
	{
		$sql_str = "select name from staff_tbl where staff_id='$staff'";
		
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);

		if(!$result){
			return false;
		}
		return $result['name'];

	}


	/*name是项目名称*/
	public function add_project($name)
	{		
		/*项目已经存在*/
		if ($this->has_project($name)) {
			return 1;
		}
		
		//新加的项目肯定显示的,outmoded=0,  outmoded=1表示不显示该项目
		$sql_str="insert into project_tbl(project_name,outmoded) values('$name',0)";

		$result = mysql_query($sql_str, $this->conn);
		if (!$result) {	
			return -1;
		} 
		
		return 0;
	}

	/*name是项目名称, 隐藏项目*/
	public function hide_project($name)
	{		
		//没找到项目
		if (!$this->has_project($name)) {
			return 1;
		}
		
		$sql_str="update project_tbl set outmoded=1 where project_name='$name' ";
		$result = mysql_query($sql_str, $this->conn);
		if (!$result) {	
			return -1;
		}  		
		
		return 0;
	}


	/*判断是否有该项目,没有返回false,有返回项目id*/
	public function has_project($name)
	{
		$sql_str = "select project_id from project_tbl where project_name='$name'";
		
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);

		if(!$result){
			return false;
		}

		return (integer)$result['project_id'];

	}

	/*获取项目名称,没有返回false,有返回项目名称*/
	public function get_projectName($project)
	{
		$sql_str = "select project_name from project_tbl where project_id=$project";
		
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);

		if(!$result){
			return false;
		}
		return $result['project_name'];

	}

	/*查询项目是否过期*/
	public function is_project_outmoded($project)
	{
		$sql_str = "select outmoded from project_tbl where project_id=$project";
		
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);

		if(!$result){
			return false;
		}

		/*等于0表示还没有过期*/
		if (0 === (integer)$result['outmoded']) {
			return false;
		}
		return true;
	}


	/*添加项目进度信息*/
	public function add_progress($staff, $project, $count)
	{
		$year = date('Y');
		$month = date('m');
		$timeStamp = time();
		
		$staff_id = $this->has_staff($staff);
		if (!$staff_id) {
			return 1;//没有该stff
		}

		$project_id = $this->has_project($project);
		if (!$project_id) {
			return 2;//没有该project
		}
		/*输入信息不是数字*/
		if(!is_numeric($count)){
	        return 3;
	    }
	    $count = (integer)$count;
	    /*输入数字超标*/
	    if ($count > 1000 || $count < -1000) {
	    	return 3;
	    }
	    $total = 0;

		
	    $sql_str = "select project_count from works_tbl where project_id=$project_id and staff_id=$staff_id and year=$year and month=$month";
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/
		$result = mysql_fetch_array($result);

		if($result){
			$total = $result['project_count'];
		}
		$total = $total + $count;
		if ($total < 0) {
			$total = 0;
		}

		
		$sql_str = "replace into works_tbl(staff_id,project_id,project_count,time_stamp,month,year) values ($staff_id,$project_id,$total,$timeStamp,$month,$year)";
		$result = mysql_query($sql_str, $this->conn);	
		
		if (!$result) {	//查询失败
			return false;
		}

	    return 0;
	}
	

	public function show_staff($year, $month)
	{
		$sql_str = "select staff_id,sum(project_count) as counts from works_tbl where year=$year and month=$month group by staff_id order by counts desc;";
		
		$result = mysql_query($sql_str, $this->conn);	
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/

		$rst = array();
		while ($row = mysql_fetch_array($result)) {
			if (!$this->is_staff_stay($row['staff_id'])) {
				continue;//员工已经离职
			}
			$item['staff_id'] = $row['staff_id'];
			$item['staff_name'] = $this->get_staffName($item['staff_id']);
			$item['counts'] = $row['counts'];		

			array_push($rst, $item);
		}
		return $rst;			
	}


	public function show_staff_detail($staff, $year, $month)
	{
		$sql_str = "select project_id,project_count from works_tbl where year=$year and month=$month and staff_id=$staff order by project_count desc;";
		
		$result = mysql_query($sql_str, $this->conn);	
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/

		$rst = array();
		//$rst['staff_id'] = $staff;
		//$rst['staff_name'] = $this->get_staffName($staff);
		
		while ($row = mysql_fetch_array($result)) {
			
			$item['project_id'] = $row['project_id'];
			$item['project_name'] = $this->get_projectName($item['project_id']);
			$item['project_count'] = $row['project_count'];		
			
			array_push($rst, $item);
		}
		return $rst;			
	}

	public function show_projects($year, $month)
	{
		$sql_str = "select project_id,sum(project_count) as counts from works_tbl where year=$year and month=$month group by project_id order by counts desc;";
		$result = mysql_query($sql_str, $this->conn);	
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/

		$rst = array();
		while ($row = mysql_fetch_array($result)) {
			if ($this->is_project_outmoded($row['project_id'])) {
				continue;
			}
			$item['project_id'] = $row['project_id'];
			$item['project_name'] = $this->get_projectName($item['project_id']);
			$item['counts'] = $row['counts'];		

			array_push($rst, $item);
		}
		return $rst;			
	}



public function get_staffList()
	{
		$sql_str = "select staff_id,name,sex,stay from staff_tbl order by staff_id asc;";
		
		$result = mysql_query($sql_str, $this->conn);	
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/

		$rst = array();
		while ($row = mysql_fetch_array($result)) {
			if (1 === (integer)$row['stay']) {
				continue;//员工已经离职
			}
			$item['staff_id'] = $row['staff_id'];
			$item['staff_name'] = $row['name'];
			$item['staff_sex'] = $row['sex'];
			$item['stay'] = $row['stay'];		

			array_push($rst, $item);
		}
		return $rst;			
	}




public function get_projectList()
	{
		$sql_str = "select project_id,project_name,outmoded from project_tbl order by project_id asc;";
		
		$result = mysql_query($sql_str, $this->conn);	
		if (!$result) {	//查询失败
			return false;
		}

		/*mysql_fetch_array()
		* 返回根据从结果集取得的行生成的数组，如果没有更多行则返回 false			
		*/

		$rst = array();
		while ($row = mysql_fetch_array($result)) {
			if (1 === (integer)$row['outmoded']) {
				continue;//项目已经过期
			}
			$item['project_id'] = $row['project_id'];
			$item['project_name'] = $row['project_name'];
			$item['outmoded'] = $row['outmoded'];

			array_push($rst, $item);
		}
		return $rst;			
	}

	public function set_goodCount($year, $month, $count, $timeShow)
	{	
		if(!is_numeric($count)){
	        return 1;
	    }
	    $count = (integer)$count;
	    /*输入数字超标*/
	    if ($count > 1000 || $count < -1000) {
	    	return 2;
	    }		
		$stamp = time();
		$sql_str="replace into setting_tbl(year,month,count,timeStamp, timeShow) values($year,$month, $count, $stamp,'$timeShow')";

		$result = mysql_query($sql_str, $this->conn);
		if (!$result) {	
			return false;
		} 
		
		return 0;
	}

	public function load_goodCount($year, $month)
	{		
		$sql_str="select count,timeShow from setting_tbl where year=$year and month=$month";

		$result = mysql_query($sql_str, $this->conn);
		if (!$result) {	
			return false;
		} 
		$row = mysql_fetch_array($result);
		if(!$row){/*月初还没设置,默认就是30*/
			$this->set_goodCount($year, $month, 30,"");
			$rst['count'] = 30;
			$rst['timeShow'] = '';
			return $rst;
		}
		$rst['count'] = $row['count'];
		$rst['timeShow'] = $row['timeShow'];
		return $rst;
	}
	
}
?>
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


	/*添加项目进度信息*/
	public function add_progress($staff, $project, $count)
	{
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
	    if ($count > 10000 || $count < -10000) {
	    	return 3;
	    }
	    $total = 0;

		echo "staffID:" . $staff_id;
		echo "projectID:" . $project_id;
		
	    $sql_str = "select project_count from works_tbl where project_id=$project_id";
		echo $sql_str;
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
		echo "count:" . $result['project_count'];


	    return 0;
	}
	public function login_platform($open_id, $platform, &$user_id)
	{
		

		$sql_str="select mac,user_id from user_tbl where open_id='$open_id' and platform ='$platform'";
		$result=mysql_query($sql_str, $this->conn);

		$ret = "";
		$user_id = 0;

		//echo $result."logined!";
		if($row = mysql_fetch_array($result))
		{
			//echo "mac:".$row['mac'];
			//echo "user_id:".$row['user_id'];
			$ret=$row['mac'];
			$user_id = (integer)$row['user_id'];
		}

		return $ret;
	}

}

?>
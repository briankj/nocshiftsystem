<?php
date_default_timezone_set('Asia/Taipei');
include('config1.php');
$type = $_POST['type'];
// input data into database
if($type == 'insert') {
	$NOC_members_userid = $_POST['NOC_members_userid'];
	$shift_date = $_POST['shift_date'];
	$shift = $_POST['shift'];
	$required_id = $_POST['required_id'];
	// ask mysql to execute the command below
	$insert = mysqli_query($con,"INSERT INTO NOC_shift(`NOC_members_userid`, `shift_date`, `shift`, `required_id`) VALUES('$NOC_members_userid', '$shift_date', '$shift', '$required_id')");
}
/* delete data from database
----------------------------------------------------*/
if($type == 'delete') {
	 $NOC_members_userid = $_POST['NOC_members_userid'];
	 $shift_date = $_POST['shift_date'];
	// ask mysql to execute the command below
	$delete = mysqli_query($con,"DELETE FROM NOC_shift where NOC_members_userid='$NOC_members_userid' and shift_date='$shift_date'");
}
/* input data into database takeoff
----------------------------------------------------*/
if($type == 'inserttakeoff') {
	$NOC_members_userid = $_POST['NOC_members_userid'];
	$takeoff_sdate = $_POST['takeoff_sdate'];
	$takeoff_edate = $_POST['takeoff_edate'];
	$dev = (strtotime($takeoff_edate) - strtotime($takeoff_sdate))/3600;
	$takeoff = round($dev,1);
	$required_id = $_POST['required_id'];
	/* ask mysql to execute the command below
	-----------------------------------------*/
	$insert = mysqli_query($con,"INSERT INTO NOC_takeoff(`NOC_members_userid`, `takeoff_sdate`, `takeoff_edate`, `takeoff`, `required_id`) VALUES('$NOC_members_userid', '$takeoff_sdate', '$takeoff_edate', '$takeoff', '$required_id')");
}
/* fetch all employee data from database by username
----------------------------------------------------*/
if($type == 'employeefetch') {
	$events = array();
	$query = mysqli_query($con, "SELECT NOC_members.userid, NOC_members.username, NOC_members.noc_color, NOC_members.noc_icon FROM NOC_members");
	while($fetch_employee = mysqli_fetch_array($query,MYSQLI_ASSOC))
	{
		$temp = array();
		$temp['employeeID']=$fetch_employee['userid'];
		$temp['employeeName']=$fetch_employee['username'];
		$temp['employeeColor']=$fetch_employee['noc_color'];
		$temp['employeeIcon']=$fetch_employee['noc_icon'];
		array_push($events,$temp);
	}
		echo json_encode($events);
}
/* fetch all workdays from database by username
----------------------------------------------------*/
if($type == 'shiftfetch') {
	$today = date('Y-m-d');
	$year = $_POST['year'];
	$month = $_POST['month'];

	$cuser = $_POST['userid'];
	$suser = $_POST['userid'];
	$events = array();
	$query = mysqli_query($con, "SELECT NOC_shift.NOC_members_userid, NOC_members.username, NOC_members.noc_color, NOC_shift.shift_date, NOC_shift.shift FROM NOC_shift INNER JOIN NOC_members ON NOC_shift.NOC_members_userid=NOC_members.userid WHERE YEAR(shift_date)=$year and MONTH(shift_date)=$month ");
	while($fetch_workdays = mysqli_fetch_array($query,MYSQLI_ASSOC)) {
		$temp = array();
		switch ($fetch_workdays['shift']) {
			case "Night":
				$temp['eventIdentity']=$fetch_workdays['username']."\n".(int)explode("-", $fetch_workdays['shift_date'])[0]."/".(int)explode("-", $fetch_workdays['shift_date'])[1]."/".(int)explode("-", $fetch_workdays['shift_date'])[2];
				$temp['shift']=$fetch_workdays['shift'];
				$temp['color']=$fetch_workdays['noc_color'];
				$temp['start']=str_replace("-", "/", $fetch_workdays['shift_date'] . " 00:00");
				$temp['end']=str_replace("-", "/", $fetch_workdays['shift_date'] . " 09:30");
				$temp['description']="On: night shift";
				if($today <= $fetch_workdays['shift_date']) {
    	            if($cuser == $fetch_workdays['NOC_members_userid'])
	    		    	$temp['editable'] = true;
	    	        else if($suser == $fetch_workdays['NOC_members_userid'])
	    		    	$temp['editable'] = true;
	            }
				break;
			case "Day":
				$temp['eventIdentity'] = $fetch_workdays['username'] . "\n" . (int)explode("-", $fetch_workdays['shift_date'])[0] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[1] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[2];
				$temp['shift'] = $fetch_workdays['shift'];
				$temp['color'] = $fetch_workdays['noc_color'];
				$temp['start'] = str_replace("-", "/", (int)explode("-", $fetch_workdays['shift_date'])[0] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[1] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[2] . " 09:00");
				$temp['end'] = str_replace("-", "/", (int)explode("-", $fetch_workdays['shift_date'])[0] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[1] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[2] . " 18:30");
				$temp['description'] = "On: day shift";
				if($today <= $fetch_workdays['shift_date']){
    	        	if($cuser == $fetch_workdays['NOC_members_userid'])
    		    		$temp['editable'] = true;
    	        	else if($suser == $fetch_workdays['NOC_members_userid'])
    		    		$temp['editable'] = true;
	            }
				break;
			case "Swing":
				$temp['eventIdentity'] = $fetch_workdays['username'] . "\n" . (int)explode("-", $fetch_workdays['shift_date'])[0] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[1] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[2];
				$temp['shift'] = $fetch_workdays['shift'];
				$temp['color'] = $fetch_workdays['noc_color'];
				$temp['start'] = str_replace("-", "/", (int)explode("-", $fetch_workdays['shift_date'])[0] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[1] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[2] . " 15:00");
				$temp['end'] = str_replace("-", "/", (int)explode("-", $fetch_workdays['shift_date'])[0] . "/" . (int)explode("-", $fetch_workdays['shift_date'])[1] . "/" . ((int)explode("-", $fetch_workdays['shift_date'])[2]+1) . " 00:30");
				$temp['description'] = "On: swing shift";
				if($today <= $fetch_workdays['shift_date']){
    	        	if($cuser == $fetch_workdays['NOC_members_userid'])
    		    		$temp['editable'] = true;
    	        	else if($suser == $fetch_workdays['NOC_members_userid'])
    		    		$temp['editable'] = true;
	            }
				break;
		}
		array_push($events,$temp);
	}
	echo json_encode($events);
}
/* fetch all off hour events from database
----------------------------------------------------*/
if($type == 'fetchtakeoff') {
	$events = array();
	$year = $_POST['year'];
	$month = $_POST['month'];

	$query = mysqli_query($con, "SELECT NOC_takeoff.NOC_members_userid, NOC_members.username, NOC_members.noc_color, NOC_takeoff.takeoff_sdate, NOC_takeoff.takeoff_edate, NOC_takeoff.takeoff FROM NOC_takeoff INNER JOIN NOC_members ON NOC_takeoff.NOC_members_userid=NOC_members.userid WHERE YEAR(takeoff_sdate)=$year and MONTH(takeoff_sdate)=$month");
	while ($fetch_offhours = mysqli_fetch_array($query,MYSQLI_ASSOC)) {
		$temp = array();
			
			$off=$fetch_offhours['takeoff'];
			$dates=explode(" ", $fetch_offhours['takeoff_sdate'])[0];//日期 起
			$datee=explode(" ", $fetch_offhours['takeoff_edate'])[0];//日期 迄
			$datess=str_replace("-", "/", $dates);                   //replace
			$datees=str_replace("-", "/", $datee);
            $time=explode(" ", $fetch_offhours['takeoff_sdate'])[1]."-".explode(" ", $fetch_offhours['takeoff_edate'])[1];

        if ($datee > $dates) {
        	$times=explode("-", $time)[0];
			$timess=explode(":", $times)[0].":".explode(":", $times)[1];//只剩下時分 起
			$timee=explode("-", $time)[1];                             //只剩下時分 迄
			$timees=explode(":", $timee)[0].":".explode(":", $timee)[1]."*";
			$timep=$timess."-".$timees;                               //合併時分
			$temp['tempID']=$fetch_offhours['username']."\n".(int)explode("/", $datess)[0]."/".(int)explode("/", $datess)[1]."/".(int)explode("/", $datess)[2];
        }
        else {
			$times=explode("-", $time)[0];
			$timess=explode(":", $times)[0].":".explode(":", $times)[1];//只剩下時分 起
			$timee=explode("-", $time)[1];                             //只剩下時分 迄
			$timees=explode(":", $timee)[0].":".explode(":", $timee)[1];
			$timep=$timess."-".$timees;                               //合併時分
			$temp['tempID']=$fetch_offhours['username']."\n".(int)explode("/", $datess)[0]."/".(int)explode("/", $datess)[1]."/".(int)explode("/", $datess)[2];
		}

		if ($off === "9.5") {
			$temp['title']="All day";
		}
		else {
			$temp['title']=$timep;
		}
	    $temp['description']="Off: ".$timess."-".$timees;
	    $temp['takeoff']=$off;

		array_push($events,$temp);
	}
	echo json_encode($events);
}
/* delete takeoffdata from database
----------------------------------------------------*/
if($type == 'deletetakeoff') {
	 $NOC_members_userid = $_POST['NOC_members_userid'];
	 $takeoff_sdate = $_POST['takeoff_sdate'];
	// ask mysql to execute the command below
	$delete = mysqli_query($con,"DELETE FROM NOC_takeoff where NOC_members_userid='$NOC_members_userid' and takeoff_sdate LIKE '%$takeoff_sdate%'");
}
/* retrieve data from shift and takeoff, and calculate them to summarize the data
-----------------------------------------------------*/
if($type == 'workdayandtakeoffday') {
	
	$year = $_POST['year'];
	$month = $_POST['month'];

$eventsa = array();
		$query = mysqli_query($con, "SELECT NOC_members.username,NOC_members.userid,NOC_members.noc_color,SUM(NOC_takeoff.takeoff) AS OffHour FROM NOC_takeoff LEFT JOIN NOC_members ON NOC_takeoff.NOC_members_userid=NOC_members.userid WHERE YEAR(takeoff_sdate)=$year and MONTH(takeoff_sdate)=$month GROUP BY username");
	while($fetch_workdays = mysqli_fetch_array($query,MYSQLI_ASSOC))
	{
		$temp = array();
		$temp['username']=$fetch_workdays['username'];
		$temp['userid']=$fetch_workdays['userid'];
		$temp['noccolor']=$fetch_workdays['noc_color'];
		$temp['OffHour']=$fetch_workdays['OffHour'];

		array_push($eventsa,$temp);
	}
		//echo json_encode($eventsa);

$events = array();
		$query = mysqli_query($con, "SELECT NOC_members.username,NOC_members.userid,NOC_members.noc_color,COUNT(NOC_shift.NOC_members_userid) AS workday FROM NOC_shift LEFT JOIN NOC_members ON NOC_shift.NOC_members_userid=NOC_members.userid WHERE YEAR(shift_date)=$year and MONTH(shift_date)=$month GROUP BY username");
	while($fetch_workdays = mysqli_fetch_array($query,MYSQLI_ASSOC))
	{
		$temp = array();
		$temp['username']=$fetch_workdays['username'];
		$temp['userid']=$fetch_workdays['userid'];
		$temp['noccolor']=$fetch_workdays['noc_color'];
		$temp['workdays']=$fetch_workdays['workday'];

		array_push($events,$temp);

	}
	    $arr=array_merge($events,$eventsa);
		echo json_encode($arr);
}
/* to insert modify time and id
-----------------------------------------------------*/
if ($type == 'insertMod') {
	$NOC_members_userid = $_POST['NOC_members_userid'];
    $date_time = date("Y-m-d H:i:s");
	$events = array();
	//$update = mysqli_query($con,"UPDATE NOC_modify SET NOC_members_userid='$title'");
	$delete = mysqli_query($con,"DELETE FROM NOC_modify");  // delete all data in NOC_modify table
	$insert = mysqli_query($con,"INSERT INTO NOC_modify(`NOC_members_userid`, `modify_time`) VALUES('$NOC_members_userid', '$date_time')");
	//$insert = mysqli_query($con,"INSERT INTO NOC_modify(`NOC_members_userid`) VALUES('$NOC_members_userid')");
}
/* to fetch modify time and id
-----------------------------------------------------*/
if ($type == 'fetchMod') {
	$events = array();
	$query = mysqli_query($con,"SELECT NOC_modify.NOC_members_userid, NOC_members.username, NOC_modify.modify_time FROM NOC_modify INNER JOIN NOC_members ON NOC_modify.NOC_members_userid=NOC_members.userid");

	while($fetch_modify = mysqli_fetch_array($query,MYSQLI_ASSOC))
	{
		$temp = array();
		$temp['userid']=$fetch_modify['NOC_members_userid'];
		$temp['username']=$fetch_modify['username'];
		$temp['modifytime']=$fetch_modify['modify_time'];


		array_push($events,$temp);
	}
		echo json_encode($events);
}

?>

 <?
 //-----------------------on duty是路徑---------------------
 include('config1.php');
 $type = $_POST['type'];

// $user = 'backend';
//   // database password
//   $pass = 'noc';
//   // data source = mysql driver, localhost, database = class
//   $dsn = 'mysql:host=localhost;dbname=shift_system';
//   // PDO class represents the connection
//   $dbh = new PDO($dsn, $user, $pass);


$date=date("Y-m-d");
$year=date("Y");
$month=date("m");
$day=date('d');
/* fetch all employee data from database by username
----------------------------------------------------*/
if ($type == 'FetchOnDuty') {
  $shiftArr = array();
  $query = mysqli_query($con, "SELECT NOC_shift.NOC_members_userid, NOC_members.username, NOC_members.noc_icon, NOC_shift.shift FROM `NOC_shift` INNER JOIN NOC_members ON NOC_shift.NOC_members_userid=NOC_members.userid WHERE YEAR(shift_date)=$year and MONTH(shift_date)=$month and DAY(shift_date)=$day order by shift");
  while($fetch_employee = mysqli_fetch_array($query,MYSQLI_ASSOC))
  {
    $temp = array();
    $temp['icon']=$fetch_employee['noc_icon'];
    $temp['shift']=$fetch_employee['shift'];
    array_push($shiftArr,$temp);
  }
    echo json_encode($shiftArr);
 }
//----------------fetch last modify-------------------------------------------------------------------
if ($type == 'FetchModify') {
  $modifyArr = array();
  $query = mysqli_query($con, "SELECT NOC_modify.NOC_members_userid, NOC_modify.modify_time, NOC_members.username FROM `NOC_modify` INNER JOIN NOC_members ON NOC_modify.NOC_members_userid=NOC_members.userid");
  while($fetch_employee = mysqli_fetch_array($query,MYSQLI_ASSOC))
  {
    $temp = array();
    $name=explode("_", $fetch_employee['username']);
    $temp['username']=ucfirst($name[0]);
    $temp['moddate']=explode(" ", $fetch_employee['modify_time'])[0];
    $temp['modtime']=explode(" ", $fetch_employee['modify_time'])[1];
    array_push($modifyArr,$temp);
  }
    echo json_encode($modifyArr);
}

//-----------------------fetch takeoff-------------------------------------------------------------------------
if ($type == 'FetchTakeoff') {
$year = date("Y");
$month = date("m");
$day = date("d");


$events = array();
  $query = mysqli_query($con,"SELECT NOC_takeoff.NOC_members_userid, NOC_members.username, NOC_takeoff.takeoff_sdate, NOC_takeoff.takeoff_edate, NOC_takeoff.takeoff FROM NOC_takeoff INNER JOIN NOC_members ON NOC_takeoff.NOC_members_userid=NOC_members.userid where YEAR(takeoff_sdate)=$year and MONTH(takeoff_sdate)=$month and Day(takeoff_sdate)>=$day order by takeoff_sdate");

  while($fetch_takeoff = mysqli_fetch_array($query,MYSQLI_ASSOC))
  {
    $temp = array();
    $name=explode("_", $fetch_takeoff['username'])[0];
    $temp['username']=ucfirst($name);
    $s=explode(" ",$fetch_takeoff['takeoff_sdate'])[0];
    $temp['date']=explode("-", $s)[1]."/".explode("-", $s)[2];
    $t=explode(" ",$fetch_takeoff['takeoff_sdate'])[1];
    $st=explode(":", $t)[0].":".explode(":", $t)[1];
    $t=explode(" ",$fetch_takeoff['takeoff_edate'])[1];
    $et=explode(":", $t)[0].":".explode(":", $t)[1];
        $temp['time']=$st."-".$et;
    //$temp['d']=strtotime($fetch_takeoff['takeoff_sdate']);
    


    array_push($events,$temp);
  }
    echo json_encode($events);

}
  ?>
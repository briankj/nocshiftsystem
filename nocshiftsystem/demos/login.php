<?php 
//session destroy

  session_start();
  session_destroy();
  session_unset();


$con = mysqli_connect('localhost','backend','noc','shift_system');
$redir_url = $_SERVER['REQUEST_URI'];



$err_msg="";

if(isset($_POST['submit']))
{
  $user = 'backend';
  // database password
  $pass = 'noc';
  // data source = mysql driver, localhost, database = class
  $dsn = 'mysql:host=localhost;dbname=shift_system';

  // PDO class represents the connection
  $dbh = new PDO($dsn, $user, $pass);

  // $name=$_POST['username'];
  // $pwd=$_POST['noc_passwd'];
  $_POST["username"]=mysqli_escape_string($con,$_POST["username"]);
  $_POST["noc_passwd"]=mysqli_escape_string($con,$_POST["noc_passwd"]);

  $name=$_POST["username"];
  $pwd=$_POST["noc_passwd"];
  

  if($name!=''&&$pwd!='') 
  {

    // SQL statement
    $sql = "select * from NOC_members where username='".$name."' and noc_passwd='".$pwd."'";
    //$res = $dbh->query($sql, PDO::FETCH_ASSOC;
    // Use query() for "one-time" SQL requests
    // PDO::FETCH_ASSOC = return results in the form of an associative array
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll();
    $row_count = $query->rowCount();
    //$rows = $result->fetch(PDO::FETCH_NUM);

    $name_arr = array('Kevin','Brian','Meg','Jacky','Forrest','Julia','Jack');

    if ($row_count>0) 
    {
      $color=$results[0]['noc_color'];
      $userid=$results[0]['userid'];
      $icon=$results[0]['noc_icon'];
      foreach ($name_arr as $user) 
      {
        if(stripos($name,$user,$userid)!==false)
        {
          $name = $user;
        }
      }
      
      session_start();//send session to nocshiftsystem
  if($name!='')
  {
  $_SESSION['username']="$name";
  $_SESSION['uid']="$userid";
  $_SESSION['color']="$color";
  $_SESSION['icon']="$icon";
  }

      header("location:landingpage.php");
      //header("location:2.html?");
      //foreach ($res as $row) {
      // each $row = an associative array representing one row in the database
      // the key = the column name
    }
    else
    {
      $err_msg = 'Incorrect username or password';
    }
  }
}

// closes the database connection
$dbh = NULL;
?>


<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>NOC Login</title>
	<style>
        #title {
            margin: 5% auto 0px;
            padding:20px;
            border: 1px #eae9eb solid;
            border-top-left-radius:4px;
            border-top-right-radius:4px;
            background: #eae9eb;
            width:335px;
            height:18px;
            text-align:center;
            font-family: arial rounded mt bold,Helvetica,Arial,Verdana,sans-serif;
            font-size: 12px;
            color: #54487e;
        }
        #form {
            margin: 0px auto;
            padding: 20px;
            border: 1px #eae9eb solid;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
            width: 335px;
            height: 362px;
            text-align: center;
        }
        #input {
            margin-bottom: 10px;
            border: 1px #eae9eb solid;
            border-radius: 4px;
            width: 330px;
            height: 55px;
            text-indent: 20px;
            font-size: 14px;
        }
        #login {
            border: 1px #54487e solid;
            border-radius: 4px;
            width: 332px;
            height: 60px;
            background: #54487e;
            color: white;
            font-family: arial rounded mt bold,Helvetica,Arial,Verdana,sans-serif;
        }
	</style>
</head>
<body>
    <div id="title">
        <div>WELCOME!</div>
    </div>
    
    <div id="form">
        <div style="margin: -10px 20px 10px 0px;">
            <img src="image/logo_red.png" width="300px">
        </div>
        <form action="" method="post">
            <div><input type="text" name="username" placeholder="Username" id="input"></div>
            <div><input type="password" name="noc_passwd" placeholder="Password" id="input"></div>
            <div style="padding-bottom: 5px;"><font color="red"><?php echo $err_msg;?></font></div>
            <div><input type="submit" name="submit" value="LOGIN" id="login">
            </div>
        </form>
    </div>
</body>
</html>
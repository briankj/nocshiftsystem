<!DOCTYPE html>
<?php 
//----------------get session from lonin page---------------------------------------------
session_start();

  if($_SESSION["username"]== '')
     { 
        header("location:login.php");
     }
     else
     {
        $username=$_SESSION["username"];
        $usericon=$_SESSION["icon"];
     }
// --------------------session destroy after 600 seconds----------------------------------

  if ( isset($_SESSION[ ' time ' ]) && (time( )-$_SESSION[ ' time ' ] > 600))  
 {
  //last request was more than 10 minutes age
  session_unset( );
  session_destroy( );
  header("location:login.php");

 }
  $_SESSION[ ' time ' ] = time( );
//----------------------auto reflesh after 602 seconds------------------------------------
  $url1=$_SERVER['REQUEST_URI'];
header("Refresh: 602; URL=$url1");

header("Content-Type: text/html; charset=utf-8");
include('config1.php');




//執行更新動作
if(isset($_POST["action"])&&($_POST["action"]=="update")){	
	
	//若有修改密碼，則更新密碼。
	if(($_POST["m_passwd"]!="")&&($_POST["m_passwd"]==$_POST["m_passwdrecheck"])){
		//$query_update .= "`noc_passwd`='".md5($_POST["m_passwd"])."',";
		$query_update = "UPDATE NOC_members SET noc_passwd='$_POST[m_passwd]' WHERE userid='$userid'";
    mysqli_query($con,$query_update);
    $redirectUrl="login.php";
     header("Location: login.php");
	}else{
    header("Location: member_update.php");
       }	
	
	}		
	


//繫結登入會員資料
$sql = "SELECT * FROM `NOC_members` WHERE `userid`='".$_SESSION["uid"]."'";
$result = mysqli_query($con,$sql);	
$row_Member=mysqli_fetch_assoc($result);
$username=$row_Member['username'];
$userid=$row_Member['userid'];
$userpasswd=$row_Member['noc_passwd'];
//echo $userid;
//print_r($row_Member); 

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Passwd</title>
    <link rel="shortcut icon" type="image/x-icon" href="image/calendar_icon.png">
    <script type="text/javascript">
        
        var nocUsericon = "<?php echo $usericon;?>";
        window.onload = function() {
            var x = document.getElementById("demo1");
            x.setAttribute('style', 'background-image:url('+nocUsericon+');');  
        };
        
        //logout button
        function logout() {
            location.replace("login.php");
        }
        function checkForm(){
            if(document.formJoin.m_passwd.value!="" || document.formJoin.m_passwdrecheck.value!=""){
                if(!check_passwd(document.formJoin.m_passwd.value,document.formJoin.m_passwdrecheck.value)){
                    document.formJoin.m_passwd.focus();
                    return false;
                }
            } 
        }
        function check_passwd(pw1,pw2){
            if(pw1==''){
                alert("密碼不可以空白!");
                return false;
            }
            if(pw1 == "<?php echo $userpasswd;?>"){
                alert("與原密碼相同,請重新輸入 !\n");
                return false;
            }
            for(var idx=0;idx<pw1.length;idx++){
                if(pw1.charAt(idx) == ' ' || pw1.charAt(idx) == '\"'){
                    alert("密碼不可以含有空白或雙引號 !\n");
                }
                if(pw1.length<5 || pw1.length>10){
                    alert( "密碼長度5到10個字母 !\n" );
                    return false;
                }
                if(pw1!= pw2){
                    alert("密碼二次輸入不一樣,請重新輸入 !\n");
                    return false;
                }
            }
            return true;
        }
    </script>
</head>
<style>
/* default settings */
    body {
        height: 39rem;
        margin: 0rem 1.25rem 0rem 1.25rem;
        padding: 0rem;
        font-size: 0.75rem;
        font-family: 'Trebuchet MS', Helvetica, sans-serif;
        color: #7e7c78
    }
/* popup */
    .avgrund-cover {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0rem;
        left: 0rem;
        z-index: 1;
        visibility: hidden;
        opacity: 0;
        background: rgba( 0, 0, 0, 0.5 );
    }
    .avgrund-active .avgrund-cover {
        visibility: visible;
        opacity: 1;
    }
    .avgrund-popup {
        position: absolute;
        width: 282px;
        height: 95px;
        left: 52.5%;
        top: 50%;
        margin: -130px 0 0 -190px;
        visibility: hidden;
        opacity: 0;
        z-index: 2;
        padding: 20px;
        background: white;
        box-shadow: 0px 0px 20px rgba( 0, 0, 0, 0.6 );
        border-radius: 3px;
        -webkit-transform: scale( 0.8 );
           -moz-transform: scale( 0.8 );
            -ms-transform: scale( 0.8 );
             -o-transform: scale( 0.8 );
                transform: scale( 0.8 );
    }
    .avgrund-active .avgrund-popup {
        visibility: visible;
        opacity: 1;
        -webkit-transform: scale( 1.1 );
		   -moz-transform: scale( 1.1 );
		    -ms-transform: scale( 1.1 );
             -o-transform: scale( 1.1 );
                transform: scale( 1.1 );
    }
    .avgrund-popup.stack {
        -webkit-transform: scale( 1.5 );
		   -moz-transform: scale( 1.5 );
            -ms-transform: scale( 1.5 );
             -o-transform: scale( 1.5 );
                transform: scale( 1.5 );
    }
    .avgrund-active .avgrund-popup.stack {
        -webkit-transform: scale( 1.1 );
		   -moz-transform: scale( 1.1 );
		    -ms-transform: scale( 1.1 );
             -o-transform: scale( 1.1 );
                transform: scale( 1.1 );
    }
    .avgrund-ready body,
    .avgrund-ready .avgrund-contents,
    .avgrund-ready .avgrund-popup,
    .avgrund-ready .avgrund-cover {
        -webkit-transform-origin: 50% 50%;
		   -moz-transform-origin: 50% 50%;
            -ms-transform-origin: 50% 50%;
             -o-transform-origin: 50% 50%;
                transform-origin: 50% 50%;
        -webkit-transition: 0.3s all cubic-bezier(0.250, 0.460, 0.450, 0.940);
		   -moz-transition: 0.3s all cubic-bezier(0.250, 0.460, 0.450, 0.940);
            -ms-transition: 0.3s all cubic-bezier(0.250, 0.460, 0.450, 0.940);
             -o-transition: 0.3s all cubic-bezier(0.250, 0.460, 0.450, 0.940);
                transition: 0.3s all cubic-bezier(0.250, 0.460, 0.450, 0.940);
    }
    .avgrund-ready .avgrund-popup.no-transition {
        -webkit-transition: none;
		   -moz-transition: none;
            -ms-transition: none;
             -o-transition: none;
                transition: none;
    }
/* popup title */
    .popuptitle {
        padding: 10px;
        border: 1px #eae9eb solid;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        position: relative;
        width: 300px;
        height: 20px;
        top: -1.3rem;
        left: -1.23rem;
        font-size: 12px;
        text-align: center;
        line-height: 20px;
        font-family: arial rounded mt bold, Helvetica, Arial, Verdana, sans-serif;
        background-color: #eae9eb;
        color: #54487e;
    }
/* popup button */
    button {
        border: 0;
        padding: 8px 10px;
        margin: 5px 0px;
        border-radius: 4px;
        cursor: pointer;
        color: #fff;
        background: #54487e;
        font-size: 15px;
        width: 282px;
        height: 40px;
        font-family: arial rounded mt bold, Helvetica, Arial, Verdana, sans-serif;
        -webkit-transition: 0.15s background ease;
           -moz-transition: 0.15s background ease;
            -ms-transition: 0.15s background ease;
             -o-transition: 0.15s background ease;
                transition: 0.15s background ease;
    }
        button:hover {
            background: #645791;
        }
        button:active {
            background: #433573;
        }
        button+button {
            margin-left: 5px;
        }
/*noc logo*/
    .logoform {
        width: 87px;
        height: 45.32px;
        position: relative;
        margin-top: 0.1rem;
        display: inline-block;
    }
    .noc-logo {
        width: 87px;
    }
/* 路徑 */
    .home1 {
        color: #979a9b;
        font-size: 12px;
        display: inline;
        position: relative;
        left: 5rem;
        top: -0.2rem;
        text-decoration: none;
    }
        .home1:hover {
            color: #10b9bc;
        }
    .next {
        color: #979a9b;
        font-size: 12px;
        display: inline;
        position: relative;
        left: 5rem;
        top: -0.2rem;
    }
    .changpwd1 {
        color: #979a9b;
        font-size: 12px;
        display: inline;
        position: relative;
        left: 5rem;
        top: -0.2rem;
        text-decoration: none;
    }
        .changpwd1:hover {
            color: #10b9bc;
        }
/* 換頁button */
    .home2 {
        color: #5169ce;
        font-size: 16px;
        display: inline;
        position: absolute;
        top: 1rem;
        left: 58rem;
        text-decoration: none;
    }
        .home2:hover {
            color: #10b9bc;
        }
    .shift2 {
        color: #5169ce;
        font-size: 16px;
        display: inline;
        position: absolute;
        top: 1rem;
        left: 65rem;
        text-decoration: none;
    }
        .shift2:hover {
            color: #10b9bc;
        }
/* login img */
    .circle {
        display: inline-block;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 1px solid #ccc;
        padding: 2px;
        background: #fff;
        position: absolute;
        left: 73.5rem;
        top: 0.282rem;
    }
        .circle:hover{
            border: 1px solid #6074c6;
        }
    .inner {
        background-size: cover;
        background-position: center center;
        height: 100%;
        border-radius: 50%;
    }
/* banner line*/
    .bannerline {
        border-bottom: 1px solid #ccc;
        position: relative;
        width: 76.55rem;
        top: 0rem;
        margin-bottom: 0.3rem;
    }
/* 一排連結範圍 */
    .linkarea2 {
        position: relative;
/*        margin-bottom: 1px;*/
        height: 2rem;
        left: 2rem;
        top: 13.1rem;
        width: 72.5rem;
        text-align: center;
        line-height: 2rem;
        color: black;
    }
</style>
<body>

<!-- 登入者圖像popup -->
    <div class="avgrund-cover"></div>
    <aside class="avgrund-popup">
        <div class="popuptitle">PROFILE</div>
        <button onclick="logout()">Logout</button><br>
    </aside>
    <script type="text/javascript" src="popwindow.js"></script>
<!-- noc logo -->
    <div class="logoform">
        <a href="landingpage.php">
            <img src="image/logo_gray.png" class="noc-logo">
        </a>
    </div>
<!-- 路徑 -->
    <a href="landingpage.php" class="home1">HOME</a>
    <p class="next">></p>
    <a href="member_update.php" class="changpwd1">CHANG PASSWORD</a>
<!-- shift button -->
    <a href="landingpage.php" class="home2">HOME</a>
    <a href="nocShiftSystem.php" class="shift2">SHIFT</a>
<!-- 登入者圖像 -->
    <div class="circle" onclick="avgrund.activate( 'stack' );">
        <!-- onclick="avgrund.activate();" 另一種popup-->
        <div class="inner" id="demo1"></div>
    </div>
<!-- banner line-->
    <div class="bannerline"></div>
    
    
    
    <table width="780" border="0" align="center" cellpadding="4" cellspacing="0">
    <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="10">
      <tr valign="top">
        <td><form action="" method="POST" name="formJoin" id="formJoin" onSubmit="return checkForm();">
          <p style="font-size:20px;text-align:center"><strong>Change Password</strong></p>
          <div class="dataDiv">
<!--            <hr size="1" />-->
            <p class="heading"></p>
            <p style="padding-left:33px;font-size:13px;position: relative;left: 16rem;margin-top: 2rem;">Username：<?php echo $username;?><br></p>
            <p style="padding-left:8px;font-size:13px;position: relative;left: 16.1rem;margin-top: 2rem;">New Password：
              <input name="m_passwd" type="password" id="m_passwd">
            <br></p>
            <p style="font-size:13px;position: relative;left: 16rem;margin-top: 2rem;">Check Password：
              <input name="m_passwdrecheck" type="password" id="m_passwdrecheck"><br></p>
            
<!--          <hr size="1" />-->
          <p align="center">
            <input name="m_id" type="hidden" id="m_id" value="<?php echo $userid;?>">
            <input name="action" type="hidden" id="action" value="update">
            <input type="submit" name="Submit2" value="Reset" style="width:80px;margin-top: 2rem;">
            <input type="reset" name="Submit3" value="Clear" style="width:80px;position: relative;left: 2rem;margin-top: 2rem;">
          </p>
        </form></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- HTC Corporation All rights reserved -->  
    <div class="linkarea2">
        <p>&copy; 2016 HTC Corporation All rights reserved</p>
    </div>
</body>
</html>

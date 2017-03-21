<!DOCTYPE html>
<?php
session_start();
//----------------get session from lonin page---------------------------------------------
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
  //last request was more than 30 minutes age
  session_unset( );
  session_destroy( );
  header("location:login.php");

 }
 $_SESSION[ ' time ' ] = time( );

//----------------------auto reflesh after 602 seconds------------------------------------
  $url1=$_SERVER['REQUEST_URI'];
  header("Refresh: 602; URL=$url1");
 
$username1=explode("_", $username);

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NOC - Home</title>
    <link rel="shortcut icon" type="image/x-icon" href="image/calendar_icon.png">
    <script src="library/jquery.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        // declare login user name and icon
        var nocUsericon = "<?php echo $usericon;?>";
        /*logout button*/
        $('#logout').click(function() {
            location.replace("login.php");
        });
        // $("#nocUserName").html(nocUserName);
        // alert(nocUsericon);
        var x = document.getElementsByClassName("inner");
        x[0].setAttribute('style', 'background-image:url('+nocUsericon+');');

        /* data fetch from DB to show login user icon and name
        ---------------------------------------------------------------------*/
        $.ajax ({
            url: 'landinprocess.php',
            type: 'POST', // Send post data
            data: 'type=FetchModify',
            dataType: 'json',
            async: false,
            success: function(modtable){
                modTable = modtable;
            }
        });
        // alert(modTable[0].username + "\n" + modTable[0].moddate + "\n" + modTable[0].modtime);
        $("#ModNname").html(modTable[0].username);
        $("#ModDate").html(modTable[0].moddate);
        $("#ModTime").html(modTable[0].modtime);
        /* data fetch from DB to show on duty table
        ---------------------------------------------------------------------*/
        $.ajax ({
            url: 'landinprocess.php',
            type: 'POST', // Send post data
            data: 'type=FetchOnDuty',
            dataType: 'json',
            async: false,
            success: function(dutytable){
                dutyTable = dutytable;
            }
        });
        // alert(dutyTable[0].icon + "\n" + dutyTable[0].shift);
        var Ncount = 0;
        var Dcount = 0;
        var Scount = 0;
        for (var i = 0; i < dutyTable.length; i++) {
            var tempStr = dutyTable[i].shift.slice(0,1);
            switch (tempStr) {
                case "N":
                    // alert(tempStr + "OnDuty" + Ncount);
                    document.getElementById(tempStr + "OnDuty" + Ncount).src = dutyTable[i].icon;
                    Ncount++;
                    break;
                case "D":
                    // alert(tempStr + "OnDuty" + Dcount);
                    document.getElementById(tempStr + "OnDuty" + Dcount).src = dutyTable[i].icon;
                    Dcount++;
                    break;
                case "S":
                    // alert(tempStr + "OnDuty" + Scount);
                    document.getElementById(tempStr + "OnDuty" + Scount).src = dutyTable[i].icon;
                    Scount++;
                    break;
                default:
                    alert("triggered when shift not exists")
                    break;
            }
        }
        /* data fetch from DB to show off table
        ------------------------------------------------*/
        for (var j = 0; j < 10; j++) {
            $("#OffTime" + j).html("");
        }
        $.ajax ({
            url: 'landinprocess.php',
            type: 'POST', // Send post data
            data: 'type=FetchTakeoff',
            dataType: 'json',
            async: false,
            success: function(offtable){
                offTable = offtable;
            }
        });
        // alert(offTable[0].username + "\n" + offTable[0].date + "\n" + offTable[0].time);
        for (var i = 0; i < offTable.length; i++) {
            $("#OffDate" + i).html(offTable[i].date);
            $("#OffTime" + i).html(offTable[i].time);
            $("#OffName" + i).html(offTable[i].username);
        }
    });
    </script>
</head>
<style>
/* default settings */
    body {
        height: 115rem;
        margin: 0rem 1.25rem 0rem 1.25rem;
        padding: 0rem;
        font-size: 0.75rem;
        font-family: 'Trebuchet MS', Helvetica, sans-serif;
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
        height: 135px;
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
    .shift1 {
        color: #979a9b;
        font-size: 12px;
        display: inline;
        position: relative;
        left: 5rem;
        top: -0.2rem;
        text-decoration: none;
    }
        .shift1:hover {
            color: #10b9bc;
        }
/* 換頁button */
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
/* 表格範圍 */
    .formarea {
        position: relative;
        width: 72.5rem;
        height: 17.5rem;
        left: 2rem;
        top: 2rem;
    }
/* 單一表格 */
    .form {
        width: 18.76rem;
        height: 17.5rem;
        font-size: 15px;
        color: #7e7c78;
        border: 1px solid #f2f2f2;
        text-align: center;
        box-shadow: 1px 1px 1px #c4c4c4;
        float: left;
        margin-left: 65px;
    }
/* head background */
    .bg {
        background-color: #f2f2f2
    }
/* On Duty_td */
    .odtd1 {
        width: 73px;
        height: 73px;
        font-size: 13px;
    }
    .odtd2 {
        width: 73px;
        height: 73px;
    }
    
    .imgform {
        border:0;
        width:40px;
    }
/* Day Off_td */
    .dotd1 {
        width: 80px;
        font-size: 14px;
        padding: 10px 0px;
    }
    .dotd2 {
        width: 80px;
        font-size: 14px;
        padding: 10px 0px;
    }
    .dotd3 {
        width: 80px;
        font-size: 14px;
        padding: 10px 0px;
        color: #09b19d;
    }
/* Last Modified_td */
    .lmtd1 {
        width: 55px;
        padding: 10px 0px;
        color: #09b19d;
    }
    .lmtd2 {
        width: 80px;
        padding: 10px 0px;
    }
/* 一排連結範圍 */
    .linkarea {
        position: relative;
        margin-bottom: 15px;
        height: 18.76rem;
        left: 2rem;
        top: 5rem;
        width: 72.5rem;
    }
    .linkarea2 {
        position: relative;
        margin-bottom: 15px;
        height: 2rem;
        left: 2rem;
        top: 6rem;
        width: 72.5rem;
        text-align: center;
        line-height: 2rem;
    }
/* 每一類型連結區塊 */
    .linkform {
        font-size: 14px;
        color: #7e7c78;
        border-bottom: 1px solid #ccc;
        text-align: center;
        width:18.76rem;
        height:50px;
        float: left;
        margin: 0px 0px 0px 65px;
    }
/* 連結-標題 方塊logo */
    h4 {
        position: relative;
        font-size: 18px;
        line-height: 1;
        margin: 10px 0px 0px 0px;
        padding: 5px 15px 40px 10px;
    }
        h4:before {
            content: "";
            position: absolute;
            background: #10b9bc;/*#999  #10b9bc */
            top: 0rem;
            left: 1rem;
            height: 12px;
            width: 12px;
            -moz-transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
            -o-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }
        h4:after {
            content: "";
            position: absolute;
            background:#fd7a7e;/*#777  #fd7a7e*/
            top: 1rem;
            left: 0.6rem;
            height: 8px;
            width: 8px;
            -moz-transform: rotate(15deg);
            -webkit-transform: rotate(15deg);
            -o-transform: rotate(15deg);
            -ms-transform: rotate(15deg);
            transform: rotate(15deg);
        }
/* 連結-標題 個別logo */
    h3 {
        position: relative;
        font-size: 18px;
        line-height: 1;
        margin: 10px 0px 0px 0px;
        padding: 5px 15px 40px 10px;
    }
    .linklogo {
        width: 30px;
        position: relative;
        float: left;
        margin: -5px -30px 0px 0px;
        background-color: aliceblue;
    }
/* 連結圖形的框 */
    .linkbox {
        width: 45px;
        position: relative;
        float: left;
        margin: 0px 0px 15px 25px;
        background-color: aliceblue;
    }
/* 連結文字 */
    .linktext {
        color: #5169ce;
        font-size: 15px;
        text-decoration: none;
        list-style-type: circle;
    }
        .linktext:hover {
            position: relative;
            color: #fc3a63;
            left: 0.05rem;
        }
</style>
<body>
<!-- 登入者圖像popup -->
    <div class="avgrund-cover"></div>
    <aside class="avgrund-popup">
        <div class="popuptitle">PROFILE</div>
        <button id="" onclick="location.href='member_update.php'">Change Password</button><br>
        <button id="logout">Logout</button><br>
    </aside>
    <script type="text/javascript" src="popwindow.js"></script>
<!-- noc logo -->
    <div class="logoform">
        <a href="landingpage.php">
            <img src="image/logo_gray.png" class="noc-logo">
        </a>
    </div>
<!--    <img src="image/logo_gray.png" class="noc-logo">-->
            <!-- <p class="hi"> Hi, </p> -->
             <!-- <p id="nocUserName" class="login-name"></p> -->
<!-- 路徑 -->
    <a href="landingpage.php" class="home1">HOME</a>
<!--    <p class="next">></p>-->
<!--    <a href="nocShiftSystem.php" class="shift1">SHIFT</a>-->
<!-- shift button -->
    <a href="nocShiftSystem.php" class="shift2">SHIFT</a>
<!-- 登入者圖像 -->
    <div class="circle" onclick="avgrund.activate( 'stack' );">
        <!-- onclick="avgrund.activate();" 另一種popup-->
        <div class="inner"></div>
    </div>
<!-- banner line-->
    <div class="bannerline"></div>
<!-- three table -->
    <div class="formarea">
<!-- On Duty -->
        <div class="form">
                <table width="300px">
                   <thead>
                       <tr>
                           <th colspan="4" class="bg">
                               <p>On Duty</p>
                           </th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr>
                          <td class="odtd1"><strong>Night</strong></td>
                          <td class="odtd2"><img id="NOnDuty0" class="imgform"></td>
                          <td class="odtd2"><img id="NOnDuty1" class="imgform"></td>
                          <td class="odtd2"><img id="NOnDuty2" class="imgform"></td>
                       </tr>
                       <tr>
                          <td class="odtd1"><strong>Day</strong></td>
                          <td class="odtd2"><img id="DOnDuty0" class="imgform"></td>
                          <td class="odtd2"><img id="DOnDuty1" class="imgform"></td>
                          <td class="odtd2"><img id="DOnDuty2" class="imgform"></td>
                       </tr>
                       <tr>
                          <td class="odtd1"><strong>Swing</strong></td>
                          <td class="odtd2"><img id="SOnDuty0" class="imgform"></td>
                          <td class="odtd2"><img id="SOnDuty1" class="imgform"></td>
                          <td class="odtd2"><img id="SOnDuty2" class="imgform"></td>
                       </tr>
                   </tbody>
                </table>
            </div>
<!-- Day Off -->
        <div class="form" style="height: 275px;">

                <table width="300px">
                    <thead>
                        <tr>
                            <th colspan="3" class="bg">
                                <p>Day Off</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="dotd1" id="OffDate0"></td>
                            <td class="dotd2" id="OffTime0"></td>
                            <td class="dotd3" id="OffName0"></td>
                        </tr>
                        <tr>
                            <td class="dotd1" id="OffDate1"></td>
                            <td class="dotd2" id="OffTime1"></td>
                            <td class="dotd3" id="OffName1"></td>
                        </tr>
                        <tr>
                            <td class="dotd1" id="OffDate2"></td>
                            <td class="dotd2" id="OffTime2"></td>
                            <td class="dotd3" id="OffName2"></td>
                        </tr>
                        <tr>
                            <td class="dotd1" id="OffDate3"></td>
                            <td class="dotd2" id="OffTime3"></td>
                            <td class="dotd3" id="OffName3"></td>
                        </tr>
                        <tr>
                            <td class="dotd1" id="OffDate4"></td>
                            <td class="dotd2" id="OffTime4"></td>
                            <td class="dotd3" id="OffName4"></td>
                        </tr>
                        <tr>
                            <td class="dotd1" id="OffDate5"></td>
                            <td class="dotd2" id="OffTime5"></td>
                            <td class="dotd3" id="OffName5"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
<!-- Last Modified -->
        <div class="form">
                <table width="300px">
                    <thead>
                        <tr>
                            <th colspan="3" class="bg">
                                <p>Last Modified</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="lmtd1" id="ModNname"></td>
                            <td class="lmtd2" id="ModDate"></td>
                            <td class="lmtd2" id="ModTime"></td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
    </div>
<!-- link第一行 -->
    <div class="linkarea">
<!-- JIRA -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th>
                            <h3><img src="image/link/jira.png" class="linklogo">JIRA</h3>
                        </th>
                   </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul style="text-align:left;line-height:1.9rem;margin-top:-0rem;">
                                <li>
                                    <a href="https://htcsense.jira.com/" target="_blank" class="linktext">JIRA</a>
                                </li>
                                <li>
                                    <a href="https://htcsense.jira.com/issues/?filter=23911" target="_blank" class="linktext">Shift-Handoff</a>
                                </li>
                                <li>
                                    <a href="https://htcsense.jira.com/wiki/display/NOC/NOC+Home" target="_blank" class="linktext">NOC Home</a>
                                </li>
                                <li>
                                    <a href="https://htcsense.jira.com/wiki/display/NOC/Standard Operation Procedure (SOP)" target="_blank" class="linktext">SOP</a>
                                </li>
                                <li>
                                    <a href="https://htcsense.jira.com/wiki/display/NOC/Severity-to-Escalation" target="_blank" class="linktext">Severity-to-Escalation</a>
                                </li>
                                <li>
                                    <a href="https://htcsense.jira.com/wiki/display/NOC/Knowledge Base (KM)" target="_blank" class="linktext">Knowledge Base</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<!-- Amazon -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th>
                            <h3><img src="image/link/amazon.png" class="linklogo">AWS</h3>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul style="text-align:left;line-height:1.9rem;margin-top:-0rem;">
                                <li>
                                    <a href="https://htccsprod.signin.aws.amazon.com/console" target="_blank" class="linktext">htccsprod.signin.aws.amazon.com</a>
                                </li>
                                <li>
                                    <a href="https://htccsvr.signin.aws.amazon.com/console" target="_blank" class="linktext">htccsvr.signin.aws.amazon.com</a>
                                </li>
                                <li>
                                    <a href="https://htccsdev.signin.aws.amazon.com/console" target="_blank" class="linktext">htccsdev.signin.aws.amazon.com</a>
                                </li>
                                <li>
                                    <a href="https://htccscms.signin.aws.amazon.com/console" target="_blank" class="linktext">htccscms.signin.aws.amazon.com</a>
                                </li>
                                <li>
                                    <a href="https://htccsprod.signin.amazonaws.cn/console" target="_blank" class="linktext">htccsprod.signin.amazonaws.cn</a>
                                </li>
                                <li>
                                    <a href="https://masdvrstore.signin.amazonaws.cn/console" target="_blank" class="linktext">masdvrstore.signin.amazonaws.cn</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<!-- Nagios -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th colspan="3">
                            <h3><img src="image/link/nagios.png" class="linklogo">Nagios</h3>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul style="text-align:left;line-height:1.9rem;margin-top:-0rem;">
                                <li>
                                    <a href="http://nagios.cnn1.csh.tc/nagiosxi/login.php" target="_blank" class="linktext">nagios.cnn1.csh.tc</a>
                                </li>
                                <li>
                                    <a href="http://nagios.cne2.csh.tc/nagiosxi/login.php" target="_blank" class="linktext">nagios.cne2.csh.tc</a>
                                </li>
                                <li>
                                    <a href="http://nagios.usw2.cs-htc.co/nagiosxi/index.php" target="_blank" class="linktext">nagios.usw2.cs-htc.co</a>
                                </li>
                                <li>
                                    <a href="http://theme-global-production.monitor.masd-cloud.com/nagios3/" target="_blank" class="linktext">theme-global</a>
                                </li>
                                <li>
                                    <a href="http://theme-china-production.monitor.masd-cloud.com:4000/nagios3/" target="_blank" class="linktext">theme-china</a>
                                </li>
                                <li>
                                    <a href="http://azure-monitor.chinacloudapp.cn:4000/nagios3/" target="_blank" class="linktext">theme-china-azure</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<!-- link第二行 -->
    <div class="linkarea">
<!-- Jenkins -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th>
                            <h3><img src="image/link/jenkins.png" class="linklogo">Jenkins</h3>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul style="text-align:left;line-height:1.9rem;margin-top:-0rem;">
                                <li>
                                    <a href="http://sdet-jenkins-01.dev.usw2.cs-htc.co:8080/login?from=%2F" target="_blank" class="linktext">sdet-jenkins-01.dev.usw2.cs-htc.co</a>
                                </li>
                                <li>
                                    <a href="http://sdet-jenkins-02.dev.usw2.cs-htc.co:8080/login?from=%2F" target="_blank" class="linktext">sdet-jenkins-02.dev.usw2.cs-htc.co</a>
                                </li>
                                <li>
                                    <a href="http://sdet-jenkins-03.dev.usw2.cs-htc.co:8080/login?from=%2F" target="_blank" class="linktext">sdet-jenkins-03.dev.usw2.cs-htc.co</a>
                                </li>
                                <li>
                                    <a href="http://sdet-jenkins-04.dev.usw2.cs-htc.co:8080/login?from=%2F" target="_blank" class="linktext">sdet-jenkins-04.dev.usw2.cs-htc.co</a>
                                </li>
                                <li>
                                    <a href="http://sdet-jenkins-05.dev.usw2.cs-htc.co:8080/login?from=%2F" target="_blank" class="linktext">sdet-jenkins-05.dev.usw2.cs-htc.co</a>
                                </li>
                                <li>
                                    <a href="https://vr-sdet-jenkins02-aps1.cshtc-vr.com" target="_blank" class="linktext">vr-sdet-jenkins02-aps1.cshtc-vr.com</a>
                                </li>
                                <li>
                                    <a href="https://vr-sdet-jenkins04-cnn1.cshtc-vr.com:7443/" target="_blank" class="linktext">vr-sdet-jenkins04-cnn1.cshtc-vr.com</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<!-- Xolla -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th>
                            <h3><img src="image/link/xsolla.png" class="linklogo">Xolla</h3>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul style="text-align:left;line-height:1.9rem;margin-top:-0rem;">
                                <li>
                                    <a href="http://status.xsolla.com/" target="_blank" class="linktext">status.xsolla.com</a>
                                </li>
                                <li>
                                    <a href="http://china-status.xsolla.com/" target="_blank" class="linktext">china-status.xsolla.com</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<!-- Contact -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th>
                            <h4>Contact</h4>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul style="text-align:left;line-height:1.9rem;margin-top:-0rem;">
                                <li>
                                    <a href="https://htcsense.jira.com/wiki/display/NOC/Contact+Windows" target="_blank" class="linktext">Contact Windows</a>
                                </li>
                                <li>
                                    <a href="https://htcsense.jira.com/wiki/pages/viewpage.action?pageId=142868653" target="_blank" class="linktext">Nagios Host Contact List</a>
                                </li>
                                <li>
                                    <a href="https://htcsense.jira.com/wiki/display/NEOSTORE/SDET+Monitor" target="_blank" class="linktext">Jenkins VR</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<!-- link第三行 -->
    <div class="linkarea">
<!--Monitoring-->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th colspan="3">
                            <h4>Monitoring</h4>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width=100px>
                            <a href="https://synthetics.newrelic.com/accounts/559516/synthetics" target="_blank">
                                <img src="image/link/newrelic.png" class="linkbox">
                            </a>
                            <p style="margin-bottom:20px">New Relic</p>
                        </td>
                        <td width=100px>
                            <a href="http://d1s3w2thmgnksj.cloudfront.net/swagger#!/Notification/POST_controllers_NotificationController_createTransactionNotification" target="_blank">
                                <img src="image/link/swagger.png" class="linkbox">
                            </a>
                            <p style="margin-bottom:20px">Swagger</p>
                        </td>
                        <td width=100px>
                            <a href="http://sugarcrm.htc.com/#" target="_blank">
                                <img src="image/link/sugarcrm.png" class="linkbox">
                            </a>
                            <p style="margin-bottom:20px">SugarCRM</p>
                        </td>
                    </tr>
                    <tr style="margin-top:90px;">
                        <td width=100px>
                            <a href="https://account.ucloud.cn/cas/login?service=https%253A%252F%252Fconsolev3.ucloud.cn%252Fdashboard" target="_blank">
                                <img src="image/link/ucloud.png" class="linkbox">
                            </a>
                              <p style="margin-bottom:20px">UCloud</p>
                        </td>
                        <td width=100px>
                            <a href="http://10.23.199.178/munin/PROD/comparison-day.html" target="_blank">
                                <img src="image/link/munin.png" class="linkbox">
                            </a>
                            <p style="margin-bottom:20px">Munin</p>
                        </td>
                        <td width=100px>
                            <a href="https://vr-prod-logs-aps1.cshtc-vr.com/app/kibana#/dashboard/OPS-Dashboard-1" target="_blank">
                                <img src="image/link/kibana.png" class="linkbox">
                            </a>
                            <p style="margin-bottom:20px">Kibana</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<!-- Miscellaneous -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th colspan="3">
                            <h4>Miscellaneous</h4>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width=100px>
                            <a href="https://my.keynote.com/newmykeynote/start.aspx" target="_blank">
                                <img src="image/link/keynote.png" class="linkbox" style="margin-top:-3px;">
                            </a>
                            <p style="margin:61px 0px 26px 0px;">Keynote</p>
                        </td>
                        <td width=100px>
                            <a href="https://cloud.mongodb.com/v2/56ef6af7e4b0b1e3e80513c1#deployment/topology" target="_blank">
                                <img src="image/link/mongodb.png" class="linkbox" style="margin-top:-6px;">
                            </a>
                            <p style="margin:61px 0px 26px 0px;">MongoDB</p>
                        </td>
                        <td width=100px>
                            <a href="http://rpc.networkbench.com/rpc/home.do" target="_blank">
                                <img src="image/link/networkbench.png" class="linkbox" style="margin-top:2px;">
                            </a>
                            <p style="margin:61px 0px 26px 0px;">NetworkBench</p>
                        </td>
                    </tr>
                    <tr style="margin-top:90px;">
                        <td width=100px>
                            <a href="https://ops-prod-pwvault1.usw2.cs-htc.co/WebPasswordSafe/" target="_blank">
                                <img src="image/link/pwvault.png" class="linkbox" style="margin-top:-7px;">
                            </a>
                            <p style="margin:58px 0px 24px 0px;">PWvault</p>
                        </td>
                        <td width=100px>
                            <a href="https://portal.acdn.att.com/EdgeAuth/login.jsp" target="_blank">
                                <img src="image/link/akamai.png" class="linkbox" style="margin-top:-2px;">
                            </a>
                            <p style="margin:58px 0px 24px 0px;">Akamai</p>
                        </td>
                        <td width=100px>
                            <a href="http://sugarcrm.htc.com/#" target="_blank">
                                <img src="image/link/sugarcrm.png" class="linkbox">
                            </a>
                            <p style="margin-bottom:20px">SugarCRM</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<!-- Chat -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th colspan="3">
                            <h4>Chat</h4>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width=100px>
                            <a href="https://www.hipchat.com/sign_in" target="_blank">
                                <img src="image/link/hipchat.png" class="linkbox" style="margin-top:-3px;">
                            </a>
                            <p style="margin:61px 0px 22px 0px;">HipChat</p>
                        </td>
                        <td width=100px>
                            <a href="https://masd.slack.com/messages/uc-nocs/whats_new/" target="_blank">
                                <img src="image/link/slack.png" class="linkbox" style="margin-top:-3px;">
                            </a>
                            <p style="margin:61px 0px 22px 0px;">Slack</p>
                        </td>
                        <td width=100px>
                            <a href="" target="_blank">
<!--                                <img src="" class="linkbox" style="margin-top:-3px;">-->
                            </a>
                            <p style="margin:61px 0px 22px 0px;"></p>
                        </td>
                    </tr>
                    <tr style="margin-top:90px;">
                        <td width=100px>
                            <a href="" target="_blank">
<!--                                <img src="" class="linkbox" style="margin-top:-3px;">-->
                            </a>
                            <p style="margin:61px 0px 22px 0px;"></p>
                        </td>
                        <td width=100px>
                            <a href="" target="_blank">
<!--                                <img src="" class="linkbox" style="margin-top:-3px;">-->
                            </a>
                            <p style="margin:61px 0px 22px 0px;"></p>
                        </td>
                        <td width=100px>
                            <a href="" target="_blank">
<!--                                <img src="" class="linkbox" style="margin-top:-3px;">-->
                            </a>
                            <p style="margin:61px 0px 22px 0px;"></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<!-- link第四行 -->
    <div class="linkarea" style="height:25rem">
<!-- HTC -->
        <div class="linkform">
            <table width="300px">
                <thead>
                    <tr>
                        <th>
                            <h3><img src="image/link/htc.png" class="linklogo">HTC System</h3>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul style="text-align:left;line-height:1.9rem;margin-top:-0rem;">
                                <li>
                                    <a href="http://myhtc/Pages/Default.aspx" target="_blank" class="linktext">myHTC</a>
                                </li>
                                <li>
                                    <a href="http://lms.htc.com/" target="_blank" class="linktext">LMS</a>
                                </li>
                                <li>
                                    <a href="https://htcspam.htc.com/reporter/main_x2.php" target="_blank" class="linktext">Spam system</a>
                                </li>
                                <li>
                                    <a href="https://tpemail.htc.com/owa/auth/logon.aspx?replaceCurrent=1&url=https%3a%2f%2ftpemail.htc.com%2fowa%2f" target="_blank" class="linktext">Outlook Web App</a>
                                </li>
                                <li>
                                    <a href="http://hreform.htc.com/hreform/servlet/ServiceProxy?displayUnit=DYDBYGCV6DX5GMMUG12B0MDNYCB8IQN7" target="_blank" class="linktext">人力資源管理 (請假)</a>
                                </li>
                                <li>
                                    <a href="https://eaw.htc.com/eaw//servlet/ServiceProxy?displayUnit=EXO8599R1QC6GJ0Q7GSE4JQ6EGU24F7A&reference=menu" target="_blank" class="linktext">採購系統 (誤餐費、車資)</a>
                                </li>
                                <li>
                                    <a href="https://sso.htc.com/sso/login?service=https%3A%2F%2Fwelfare.htc.com%2Fwelfare%2Fmain.zul%3Fnull#home" target="_blank" class="linktext">員工福委會</a>
                                </li>
                                <li>
                                    <a href="https://sso.htc.com/sso/login?service=http%3A%2F%2F10%2E8%2E8%2E11%2Fsso_login%2Easp" target="_blank" class="linktext">餐飲資訊網頁</a>
                                </li>
                                <li>
                                    <a href="http://eaw.htc.com/eaw/asset" target="_blank" class="linktext">資產管理</a>
                                </li>
                                <li>
                                    <a href="http://employeemobile.htc.com/" target="_blank" class="linktext">員購系統</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<!-- HTC Corporation All rights reserved -->  
    <div class="linkarea2">
        <p>&copy; 2016 HTC Corporation All rights reserved</p>
    </div>
</body>
</html>
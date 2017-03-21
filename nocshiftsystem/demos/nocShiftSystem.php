<!-- version 20 -->
<!DOCTYPE html>
<!-- output login users data -->
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
        $userid=$_SESSION["uid"];
        $color=$_SESSION["color"];
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
?>
<html>
    <head>
        <title>NOC - Shift System</title>
        <link rel="shortcut icon" type="image/x-icon" href="library/calendar_icon.png">
        <meta charset='utf-8' />
        <!-- Ask not to save cache in browser -->
        <meta http-equiv='cache-control' content='no-cache'>
        <meta http-equiv='expires' content='0'>
        <meta http-equiv='pragma' content='no-cache'>
        <!-- For alertify -->
        <script type="text/javascript" src="library/bundle.js" charset="utf-8"></script>
        <script src="library/jquery.js"></script>
        <script type="text/javascript" src="library/alertify.js"></script>
        <link rel="stylesheet" type="text/css" href="library/css/alertify.css">
        <link rel="stylesheet" type="text/css" href="library/css/themes/default.css">
        <link rel="stylesheet" href="library/css/font-awesome-4.5.0/css/font-awesome.min.css">
        <!-- For datetimepicker -->
        <script src="library/jquery.datetimepicker.full.js"></script>
        <link rel="stylesheet" type="text/css" href="library/jquery.datetimepicker.css"/>
        <!-- For calendar. jQuery and Moment must be loaded before FullCalendar's Javascript.-->
        <link href='library/fullcalendar.css' rel='stylesheet' />
        <link href='library/fullcalendar.print.css' rel='stylesheet' media='print' />
        <script src='library/lib/moment.min.js'></script>
        <script src='library/fullcalendar.min.js'></script>
        <!-- selectmenu -->
        <link rel="stylesheet" href="library/jquery-ui.css">
        <script src="library/jquery-ui.js"></script>
        
        <script type="text/javascript">
            $(document).ready(function() {
                /* deputy variable
                -------------------------------------------------------*/
                var deputyName = "me";
                $('#deputyName').html(deputyName + ".");
                var nocUsericon = "<?php echo $usericon;?>";
                var x = document.getElementsByClassName("inner");
                x[0].setAttribute('style', 'background-image:url('+nocUsericon+');');
                /* declare variables for login user
                -------------------------------------------------------*/
                var nocUserName = "<?php echo $username; ?>";
                var nocUserID = "<?php echo $userid;?>";
                var nocUserColor = "<?php echo $color;?>";
                var loginUserName = nocUserName;
                var loginUserID = nocUserID;
                var loginUserColor = nocUserColor;
                /* declare variables for digits on table
                -------------------------------------------------------*/
                var initExp = 0;
                var initAct = 0;
                var initOff = 0;
                var initExp1 = 0;
                var initAct1 = 0;
                var initOff1 = 0;
                /* log in and fetch data from login.php
                -----------------------------------------------------*/
                // $("#nocUserName").html(nocUserName + ".");
                // $("#nocUserColor").html(nocUserColor);
                // $("#nocUserID").html(nocUserID);
                // alert(nocUserName + "\n" + nocUserID + "\n" + nocUserColor);
                /* data fetch from offhour and shift to show on ALL EMPLOYEE DATA on featuretable at first
                ---------------------------------------------------------------------*/
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yyyy = today.getFullYear();
                var currentYear = yyyy;
                var currentMonth = mm;
                $.ajax ({
                    url: 'process1.php',
                    type: 'POST', // Send post data
                    data: 'type=workdayandtakeoffday&year=' + currentYear + '&month=' + currentMonth,
                    dataType: 'json',
                    async: false,
                    success: function(NOC_sum){
                        summarizedData = NOC_sum;
                    }
                });
                var tempSumArr = [];
                for (var x in summarizedData) {
                    tempSumArr.push(summarizedData[x]);
                }

                /* Checkbox feature
                --------------------------------------------------------*/
                $("#all_cb").attr("checked", true);
                $("input[name='nocCheckBox']").prop("checked", true);
                // select all and unselect all
                $("#all_cb").click(function() {
                    $('#calendar').fullCalendar('removeEvents');    
                    if($("#all_cb").prop("checked")) {
                        $("input[name='nocCheckBox']").prop("checked", true);
                        $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                    }
                    else {
                        $("input[name='nocCheckBox']").prop("checked", false);
                        $('#calendar').fullCalendar('removeEvents');    
                    }
                });
                // select single user
                $("input[name='nocCheckBox']").click(function() {
                    $('#calendar').fullCalendar('removeEvents');        
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });
                });
                // reload selected user events
                function checkedMembers(user){
                    var tempCheckArr = [];
                    for (var k = 0; k < eventArr.length; k++){
                        if (eventArr[k].eventIdentity.split("\n")[0] === user) {
                            tempCheckArr.push(eventArr[k]);
                        }
                    }
                    $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(tempCheckArr)));
                }

                /* swap image if eventArrDelta.length is above 0 and swap back if its 0
                -------------------------------------------------------*/
                var swapImage = function() {
                    document.getElementById("submitToDB").src = "library/save_as.png";
                    document.getElementById("submitToDB").style.cursor = "pointer";
                }
                var swapImage2 = function() {
                    document.getElementById("submitToDB").src = "library/save.png";
                    document.getElementById("submitToDB").style.cursor = "";
                }
		        /* global event to show on calendar
                -----------------------------------------------------*/
                var eventArr = [];
                var eventArrDelta = [];

                /* logout button
                ------------------------------------------------------*/
                $('#logout').click(function() {
                    if (eventArrDelta.length !== 0) {
                        alertify.confirm("CONFIRMATION", // title
                                    "All unsaved changes will be lost, are you sure?", // description
                                    function(){ // do something when clicking ok
                                        eventArr.splice(0,eventArr.length);
                                        eventArrDelta.splice(0,eventArrDelta.length);
                                        employeeArr.splice(0,employeeArr.length);
                                        tempSumArr.splice(0,tempSumArr.length);
                                        location.replace("login.php");
                                    },
                                    function(){ // do something when clicking cancel
                                        alertify.error('You clicked cancel.');
                                    }
                                ).show();
                    }
                    else {
                        eventArr.splice(0,eventArr.length);
                        eventArrDelta.splice(0,eventArrDelta.length);
                        employeeArr.splice(0,employeeArr.length);
                        tempSumArr.splice(0,tempSumArr.length);
                        location.replace("login.php");
                    }
                });

                /* submit button
                -----------------------------------------------------*/
                $('#submitToDB').click(function() {

                    if(eventArrDelta.length === 0)
                        return;

                    var tempUserID = "";
                    for (var i = 0; i < eventArrDelta.length; i++) {
                        for (var j = 0; j < employeeArr.length; j++) {
                            if (employeeArr[j].employeeName === eventArrDelta[i].userName) {
                                tempUserID = employeeArr[j].employeeID;
                                break;
                            }
                        }
                        switch (eventArrDelta[i].operation) {
                            case "insert":
                                $.ajax ({
                                    url: 'process1.php',
                                    data: 'type=insert&NOC_members_userid=' + tempUserID
                                          + '&shift_date=' + eventArrDelta[i].shiftDate
                                          + '&shift=' + eventArrDelta[i].shiftType
                                          + '&required_id=' + nocUserID,
                                    type: 'POST',
                                    dataType: 'json',
                                    async: false
                                });
                                break;
                            case "delete":
                                $.ajax ({
                                    url: 'process1.php',
                                    data: 'type=delete&NOC_members_userid=' + tempUserID
                                          + '&shift_date=' + eventArrDelta[i].shiftDate,
                                    type: 'POST',
                                    dataType: 'json',
                                    async: false
                                });
                                break;
                            case "leave":
                                $.ajax ({
                                    url: 'process1.php',
                                    data: 'type=inserttakeoff&NOC_members_userid=' + tempUserID
                                          + '&takeoff_sdate=' + eventArrDelta[i].offHours.split("-")[0]
                                          + '&takeoff_edate=' + eventArrDelta[i].offHours.split("-")[1]
                                          + '&required_id=' + nocUserID,
                                    type: 'POST',
                                    dataType: 'json',
                                });
                                break;
                            case "leaveRmv":
                                var tempMonth = "";
                                var tempDay = "";
                                if (parseInt(eventArrDelta[i].shiftDate.split("/")[1]) < 10) {
                                    tempMonth = "0" + eventArrDelta[i].shiftDate.split("/")[1];
                                }
                                else {
                                    tempMonth = eventArrDelta[i].shiftDate.split("/")[1];
                                }
                                if (parseInt(eventArrDelta[i].shiftDate.split("/")[2]) < 10) {
                                    tempDay = "0" + eventArrDelta[i].shiftDate.split("/")[2];
                                }
                                else {
                                    tempDay = eventArrDelta[i].shiftDate.split("/")[2];
                                }
                                $.ajax ({
                                    url: 'process1.php',
                                    data: 'type=deletetakeoff&NOC_members_userid=' + tempUserID
                                          + '&takeoff_sdate=' + eventArrDelta[i].shiftDate.split("/")[0] + "-" + tempMonth + "-" + tempDay,
                                    type: 'POST',
                                    dataType: 'json',
                                });
                                break;
                        }
                    }
                    $.ajax ({
                        url: 'process1.php',
                        data: 'type=insertMod&NOC_members_userid=' + loginUserID,
                        type: 'POST',
                        dataType: 'json',
                    });
                    eventArr.splice(0,eventArr.length);
                    eventArrDelta.splice(0,eventArrDelta.length);
                    location.reload();
                });

                /* data fetch from database_members and store into an array named employeeArr
                -----------------------------------------------------------------------*/
                $.ajax ({
                    url: 'process1.php',
                    type: 'POST', // Send post data
                    data: 'type=employeefetch',
                    dataType: 'json',
                    async: false,
                    success: function(NOC_members){
                        employeeFetch = NOC_members;
                    }
                });
                var employeeArr = [];
                for (var z in employeeFetch) {
                    employeeArr.push(employeeFetch[z]);
                }
                /* declare number of all day off for each employee
                -----------------------------------------------------------------------*/
                var kevinOffNum = 0;
                var liyuanOffNum = 0;
                var juliaOffNum = 0;
                var jackyOffNum = 0;
                var megOffNum = 0;
                var forrestOffNum = 0;
                var brianOffNum = 0;

                /* initialization
                --------------------------------------------------------------*/
                initialization = function() {
                    var moment = $('#calendar').fullCalendar('getDate');
                    currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                    currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);
                    /* datetimepicker initialized
                    ------------------------------------------------------------*/
                    $("#datetimepicker1").datetimepicker( {
                        startDate: currentYear + "/" + currentMonth + "/1",
                        timepicker: false,
                        format:'Y/m/d',
                        minDate: 'today',
                    });
                    $("#datetimepicker2").datetimepicker( {
                        startDate: currentYear + "/" + currentMonth + "/1",
                        timepicker: false,
                        format: 'Y/m/d',
                    });
                    $("#datetimepicker3").datetimepicker( {
                        startDate: currentYear + "/" + currentMonth + "/1",
                        step: 30,
                        minDate: 'today'
                    });
                    $("#datetimepicker4").datetimepicker( {
                        startDate: currentYear + "/" + currentMonth + "/1",
                        step: 30,
                    });
                    /* data fetch from database_shift to calendar
                    ---------------------------------------------------------------------*/
                    $.ajax ({
                        url: 'process1.php',
                        type: 'POST', // Send post data
                        data: 'type=shiftfetch&userid=' + nocUserID + '&year=' + currentYear + '&month=' + currentMonth,
                        dataType: 'json',
                        async: false,
                        success: function(NOC_shift) {
                            shiftFetch = NOC_shift;
                        }
                    });
                    // alert(shiftFetch);

                    /* data fetch from database_offhour to calendar
                    ---------------------------------------------------------------------*/
                    $.ajax ({
                        url: 'process1.php',
                        type: 'POST', // Send post data
                        data: 'type=fetchtakeoff&year=' + currentYear + '&month=' + currentMonth,
                        dataType: 'json',
                        async: false,
                        success: function(NOC_takeoff){
                            fetchtakeoff = NOC_takeoff;
                        }
                    });
                    // data fetch from NOC_modify to show mod time and user
                    $.ajax ({
                        url: 'process1.php',
                        type: 'POST', // Send post data
                        data: 'type=fetchMod',
                        dataType: 'json',
                        async: false,
                        success: function(NOC_modify) {
                            modFetch = NOC_modify;
                        }
                    });
                    $("#modFetchName").html(modFetch[0].username);
                    // to offset the timedifference for 8 hours jet lag between Taiwan and location of AWS
                    var awstime = modFetch[0].modifytime;
                    // var localtime = new Date(awstime.split(" ")[0] + "T" + awstime.split(" ")[1]);
                    // localtime = (localtime + "").split("GMT")[0];
                    $("#modFetchTime").html(awstime);
                    // for NOC_shift from database
                    var tempArr = [];
                    for (var x in shiftFetch) {
                        tempArr.push(shiftFetch[x]);
                    }
                    for (var i = 0; i < tempArr.length; i++) {
                        var shiftsFromDatabase = {
                            eventIdentity: tempArr[i].eventIdentity,
                            shift: tempArr[i].shift,
                            color: tempArr[i].color,
                            start: tempArr[i].start,
                            end: tempArr[i].end,
                            description: tempArr[i].description
                        };
                        // $('#calendar').fullCalendar('renderEvent', shiftsFromDatabase, true);
                        if (tempArr[i].editable === true) {
                            shiftsFromDatabase.editable = true;
                        }
                        if (tempArr[i].eventIdentity.split('\n')[0] === loginUserName && (today <= new Date(shiftsFromDatabase.start))) {
                            shiftsFromDatabase.editable = true;
                        }
                        eventArr.push(shiftsFromDatabase);
                    }
                    tempArr.splice(0, tempArr.length);
                    // for NOC_takeoff from database
                    var tempOffArr = [];
                    for (var y in fetchtakeoff) {
                        tempOffArr.push(fetchtakeoff[y]);
                    }
                    for (var j = 0; j < tempOffArr.length; j++) {
                        var offFromDatabase = {
                            eventIdentity: tempOffArr[j].tempID,
                            title: tempOffArr[j].title,
                            textColor: "black",
                            description: tempOffArr[j].description
                        };
                        for (var k = 0; k < eventArr.length; k++) {
                            if (eventArr[k].eventIdentity === offFromDatabase.eventIdentity) {
                                eventArr[k].title = offFromDatabase.title;
                                eventArr[k].textColor = offFromDatabase.textColor;
                                eventArr[k].description = offFromDatabase.description;
                                break;
                            }
                        }
                    }
                    $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                    // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });
                    tempOffArr.splice(0, tempOffArr.length);
                    // to get actual duty day number
                    for (var j = 0; j < eventArr.length; j++) {
                        if (eventArr[j].title === "All day" && eventArr[j].eventIdentity.split("\n")[0] === "kevin_cy_hsu") {
                            kevinOffNum++;
                        }
                        if (eventArr[j].title === "All day" && eventArr[j].eventIdentity.split("\n")[0] === "liyuan_chang") {
                            liyuanOffNum++;
                        }
                        if (eventArr[j].title === "All day" && eventArr[j].eventIdentity.split("\n")[0] === "julia_tsai") {
                            juliaOffNum++;
                        }
                        if (eventArr[j].title === "All day" && eventArr[j].eventIdentity.split("\n")[0] === "jacky_bp_lee") {
                            jackyOffNum++;
                        }
                        if (eventArr[j].title === "All day" && eventArr[j].eventIdentity.split("\n")[0] === "meg_li") {
                            megOffNum++;
                        }
                        if (eventArr[j].title === "All day" && eventArr[j].eventIdentity.split("\n")[0] === "forrest_lin") {
                            forrestOffNum++;
                        }
                        if (eventArr[j].title === "All day" && eventArr[j].eventIdentity.split("\n")[0] === "brian_kj_huang") {
                            brianOffNum++;
                        }
                    }
                    // to update all data in summarized table
                    for (var c = 0; c < employeeArr.length; c++) {
                        employeeArr[c].dutyDays = 0;
                        employeeArr[c].actDays = 0;
                        employeeArr[c].offHours = 0;
                        if (employeeArr[c].employeeName === "kevin_cy_hsu") {
                            $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                            $("#kevinActDays").html(employeeArr[c].actDays);
                            $("#kevinOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - kevinOffNum;
                                    $("#kevinActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#kevinOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "liyuan_chang") {
                            $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                            $("#liyuanActDays").html(employeeArr[c].actDays);
                            $("#liyuanOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - liyuanOffNum;
                                    $("#liyuanActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#liyuanOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "julia_tsai") {
                            $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                            $('#juliaActDays').html(employeeArr[c].actDays);
                            $("#juliaOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - juliaOffNum;
                                    $('#juliaActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#juliaOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "jacky_bp_lee") {
                            $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                            $('#jackyActDays').html(employeeArr[c].actDays);
                            $("#jackyOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - jackyOffNum;
                                    $('#jackyActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#jackyOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "meg_li") {
                            $("#megDutyDays").html(employeeArr[c].dutyDays);
                            $('#megActDays').html(employeeArr[c].actDays);
                            $("#megOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#megDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - megOffNum;
                                    $('#megActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#megOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "forrest_lin") {
                            $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                            $('#forrestActDays').html(employeeArr[c].actDays);
                            $("#forrestOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - forrestOffNum;
                                    $('#forrestActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#forrestOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "brian_kj_huang") {
                            $("#brianDutyDays").html(employeeArr[c].dutyDays);
                            $('#brianActDays').html(employeeArr[c].actDays);
                            $("#brianOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#brianDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - brianOffNum;
                                    $('#brianActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#brianOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                    }
                    if (nocUserName === "jack_hsia") {
                        initExp = 0;
                        initAct = 0;
                        initOff = 0;
                        initExp1 = 0;
                        initAct1 = 0;
                        initOff1 = 0;
                    }
                    else {
                        initExp = parseInt(document.getElementById(nocUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct = parseInt(document.getElementById(nocUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff = parseFloat(document.getElementById(nocUserName.split("_")[0] + "OffHours").innerHTML);
                        initExp1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff1 = parseFloat(document.getElementById(loginUserName.split("_")[0] + "OffHours").innerHTML);
                    }
                }

                /* add changes to eventArr from eventArrDelta
                ----------------------------------------------------------------------*/
                updateEventArr = function() {
                    for (var i = 0; i < eventArrDelta.length; i++) {
                        switch (eventArrDelta[i].operation) {
                            case "insert":
                                switch (eventArrDelta[i].shiftType) {
                                    case "Night":
                                        var eventByDelta = {
                                            eventIdentity: eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate,
                                            shift: 'Night',
                                            color: nocUserColor,
                                            start: eventArrDelta[i].shiftDate + " 00:00",
                                            end: eventArrDelta[i].shiftDate + " 09:30",
                                            description: "On: night shift",
                                            editable: true
                                        };
                                        if (dateValidation(eventByDelta.eventIdentity)) return;
                                        $('#calendar').fullCalendar('renderEvent', eventByDelta, true); // true = the event permanetly fixed
                                        eventArr.push(eventByDelta);
                                        break;
                                    case "Day":
                                        var eventByDelta = {
                                            eventIdentity: eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate,
                                            shift: 'Day',
                                            color: nocUserColor,
                                            start: eventArrDelta[i].shiftDate + " 09:00",
                                            end: eventArrDelta[i].shiftDate + " 18:30",
                                            description: "On: day shift",
                                            editable: true
                                        };
                                        if (dateValidation(eventByDelta.eventIdentity)) return;
                                        $('#calendar').fullCalendar('renderEvent', eventByDelta, true); // true = the event permanetly fixed
                                        eventArr.push(eventByDelta);
                                        break;
                                    case "Swing":
                                        var tempDay = parseInt(eventArrDelta[i].shiftDate.split("/")[2]) + 1;
                                        var eventByDelta = {
                                            eventIdentity: eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate,
                                            shift: 'Swing',
                                            color: nocUserColor,
                                            start: eventArrDelta[i].shiftDate + " 15:00",
                                            end: eventArrDelta[i].shiftDate.split("/")[0] + "/" + eventArrDelta[i].shiftDate.split("/")[1] + "/" + tempDay + " 00:30",
                                            description: "On: swing shift",
                                            editable: true
                                        };
                                        if (dateValidation(eventByDelta.eventIdentity)) return;
                                        $('#calendar').fullCalendar('renderEvent', eventByDelta, true); // true = the event permanetly fixed
                                        eventArr.push(eventByDelta);
                                        break;
                                }
                                break;
                            case "delete":
                                for (var j = 0; j < eventArr.length; j++) {
                                    if (eventArr[j].eventIdentity === (eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate)) {
                                        eventArr.splice(j,1);
                                        break;
                                    }
                                }
                                $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                                // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                                $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                    checkedMembers($(this).val());
                                });
                                break;
                            case "leave":
                                switch (eventArrDelta[i].shiftType) {
                                    case "Night":
                                        for (var k = 0; k < eventArr.length; k++) {
                                            if (eventArr[k].eventIdentity === (eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate)) {
                                                if ((Date.parse('1970-01-01T' + eventArrDelta[i].offHours.split("-")[1].split(" ")[1])/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[i].offHours.split("-")[0].split(" ")[1])/1000/3600) === 9.5) {
                                                    eventArr[k].title = "All day";
                                                    eventArr[k].textColor = "black";
                                                    eventArr[k].description = "Off: " + eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                }
                                                else {
                                                    eventArr[k].title = eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                    eventArr[k].textColor = "black";
                                                    eventArr[k].description = "Off: " + eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                }
                                                break;
                                            }
                                        }
                                        break;
                                    case "Day":
                                        for (var k = 0; k < eventArr.length; k++) {
                                            if (eventArr[k].eventIdentity === (eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate)) {
                                                if ((Date.parse('1970-01-01T' + eventArrDelta[i].offHours.split("-")[1].split(" ")[1])/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[i].offHours.split("-")[0].split(" ")[1])/1000/3600) === 9.5) {
                                                    eventArr[k].title = "All day";
                                                    eventArr[k].textColor = "black";
                                                    eventArr[k].description = "Off: " + eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                }
                                                else {
                                                    eventArr[k].title = eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                    eventArr[k].textColor = "black";
                                                    eventArr[k].description = "Off: " + eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                }
                                                break;
                                            }
                                        }
                                        break;
                                    case "Swing":
                                        for (var k = 0; k < eventArr.length; k++) {
                                            if (eventArr[k].eventIdentity === (eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate)) {
                                                if (eventArrDelta[i].offHours.split("-")[0].split(" ")[0].split("/")[2] !== eventArrDelta[i].offHours.split("-")[1].split(" ")[0].split("/")[2]) { // cross date events
                                                    if ((Date.parse('1970-01-01T' + eventArrDelta[i].offHours.split("-")[1].split(" ")[1])/1000/3600 + Date.parse('1970-01-01T24:00')/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[i].offHours.split("-")[0].split(" ")[1])/1000/3600) === 9.5) { // all day events
                                                        eventArr[k].title = "All day";
                                                        eventArr[k].textColor = "black";
                                                        eventArr[k].description = "Off: " + eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1] + "*";
                                                    }
                                                    else { // non-all day events
                                                        eventArr[k].title = eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1] + "*";
                                                        eventArr[k].textColor = "black";
                                                        eventArr[k].description = "Off: " + eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1] + "*";
                                                    }
                                                }
                                                else { // non-cross date events
                                                    eventArr[k].title = eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                    eventArr[k].textColor = "black";
                                                    eventArr[k].description = "Off: " + eventArrDelta[i].offHours.split("-")[0].split(" ")[1] + "-" + eventArrDelta[i].offHours.split("-")[1].split(" ")[1];
                                                }
                                                break;
                                            }
                                        }
                                        break;
                                }
                                $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                                // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                                $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                    checkedMembers($(this).val());
                                });
                                break;
                            case "leaveRmv":
                                for (var k = 0; k < eventArr.length; k++) {
                                    if (eventArr[k].eventIdentity === (eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate)) {
                                        eventArr[k].title = " ";
                                        eventArr[k].textColor = "";
                                        eventArr[k].description = "On: " + eventArr[k].shift + " shift";
                                        break;
                                    }
                                }
                                break;
                        }
                    }
                }
                /* initialize when first login to show on calendar
                -----------------------------------------------------------------------*/
                window.onload = function() {
                    initialization();
                    // alert(initExp+"\n"+initAct+"\n"+initOff);
                };
                /* to return a new array with events in current month only
                ---------------------------------------------------------------------*/
                // function calendarMonthArr (eventArr) {
                //     var moment = $('#calendar').fullCalendar('getDate');
                //     currentYear = parseInt(moment.format().split("T")[0].split("-")[0])+"";
                //     currentMonth = parseInt(moment.format().split("T")[0].split("-")[1])+"";

                //     var calendarMonthArr = [];
                //     for (var i = 0; i < eventArr.length; i++) {
                //         if (eventArr[i].start.split("/")[0] === currentYear && eventArr[i].start.split("/")[1] === currentMonth) {
                //             calendarMonthArr.push(eventArr[i]);
                //         }
                //     }
                //     return calendarMonthArr;
                // }
                /* debug button
                --------------------------------------------------------------------*/
                $('#debugButton').click(function() { 
                    alertForDebug();
                });
                /* alert function for debug
                --------------------------------------------------------------------*/
                var alertForDebug = function () {
                    for (var i = 0; i < eventArrDelta.length; i++) {
                        alert(eventArrDelta[i].userName + "\n" + eventArrDelta[i].shiftDate + "\n" + eventArrDelta[i].shiftType + "\n" + eventArrDelta[i].operation + "\n" + eventArrDelta[i].offHours);
                    }
                };
                /* create new object to store data of eventArrDelta
                --------------------------------------------------------------------*/
                var shiftToGo = function (username, shiftdate, shifttype, operation, offhours) {
                    this.userName = username;
                    this.shiftDate = shiftdate;
                    this.shiftType = shifttype;
                    this.operation = operation;
                    this.offHours = offhours;
                };
                /* function to check operation and index and shift (for add/delete/move events and not for leave/leaveRmv)
                -------------------------------------------------------------------*/
                function checkOperationAndIndex1(eventID, eventShift) {
                    var tempIndex = -1;
                    for (var j = 0; j < eventArrDelta.length; j++) {
                        if (((eventArrDelta[j].userName + "\n" + eventArrDelta[j].shiftDate) === eventID) && (eventArrDelta[j].shiftType === eventShift) && (eventArrDelta[j].operation !== "leave") && (eventArrDelta[j].operation !== "leaveRmv")) {
                            tempIndex = j;
                            break;
                        }
                    }

                    if (tempIndex === -1) {
                        return "none@none";
                    }
                    else {
                        return (eventArrDelta[tempIndex].operation + "@" + tempIndex);
                    }
                }
                /* function to check operation and index (for leave/leaveRmv)
                -------------------------------------------------------------------*/
                function checkOperationAndIndex2(eventID, eventShift) {
                    var tempIndex = -1;
                    for (var k = 0; k < eventArrDelta.length; k++) {
                        if (((eventArrDelta[k].userName + "\n" + eventArrDelta[k].shiftDate) === eventID) && (eventArrDelta[k].shiftType === eventShift)&& (eventArrDelta[k].operation !== "insert") && (eventArrDelta[k].operation !== "delete")) {
                            tempIndex = k;
                            break;
                        }
                    }
                    if (tempIndex === -1) {
                        return "none@none";
                    }
                    else {
                        return (eventArrDelta[tempIndex].operation + "@" + tempIndex);
                    }
                }
                /* function triggered when one changed input to restrict end datetimepicker
                --------------------------------------------------------------------*/
                $("input").change(function() {
                    var picker1Pieces = $("#datetimepicker1").val().split(" ")[0].split("/");
                    var picker1Year = picker1Pieces[0];
                    var picker1Month = picker1Pieces[1];
                    $("#datetimepicker2").datetimepicker( {
                        timepicker: false,
                        format: 'Y/m/d',
                        minDate: $("#datetimepicker1").val(),
                        maxDate: picker1Year + "/" + picker1Month + "/31"
                    });
                    var picker3Pieces = $("#datetimepicker3").val().split(" ")[0].split("/");
                    var picker3Year = picker3Pieces[0];
                    var picker3Month = picker3Pieces[1];
                    $("#datetimepicker4").datetimepicker( {
                        step:30,
                        minDate: $("#datetimepicker3").val(),
                        maxDate: picker3Year + "/" + picker3Month + "/31"
                    });
                });
                /* check if event is added twice in a day
                -------------------------------------------------------------------*/
                function dateValidation (eventCheck) {       
                    var invalid = false;
                    for (var i = 0; i < eventArr.length; i++) {
                        if (eventArr[i].eventIdentity === eventCheck) { // use (name + date) as id
                            invalid = true;
                            alertify.showFailure('You could not add double shifts in a day.');
                        }
                    }
                    return invalid;
                }
                /* update one employee's Exp duty/ Act work/ Off hour
                --------------------------------------------------------------------*/
                // function updateExpActOff (emplName, updateExp, updateAct, updateOff) {
                //     var tempExpduty = parseInt(document.getElementById(emplName + "DutyDays").innerHTML);
                //     var tempActwork = parseInt(document.getElementById(emplName + "ActDays").innerHTML);
                //     var tempOffhour = parseFloat(document.getElementById(emplName + "OffHours").innerHTML);

                //     if (0 !== updateExp) {
                //         document.getElementById(emplName + "DutyDays").innerHTML = tempExpduty + updateExp;
                //     }
                //     if (0 !== updateAct) {
                //         document.getElementById(emplName + "ActDays").innerHTML = tempActwork + updateAct;
                //     }
                //     if (0 !== updateOff) {
                //         document.getElementById(emplName + "OffHours").innerHTML = tempOffhour + updateOff;
                //     }
                // }
                /* update when clicking prev/ next
                ----------------------------------------------------------------------*/
                function updateDigitNColor (emplName, initExp, initAct, initOff) {
                    var moment = $('#calendar').fullCalendar('getDate');
                    currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                    currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);

                    var turnExpToRed = false;
                    var expCount = 0;
                    var actCount = 0;
                    var offCount = 0;
                    for (var w = 0; w < eventArrDelta.length; w++) {
                        // filter for eventArrDelta data which exists in current year/ month
                        if (eventArrDelta[w].userName.split("_")[0] === emplName && parseInt(eventArrDelta[w].shiftDate.split("/")[0]) === currentYear && parseInt(eventArrDelta[w].shiftDate.split("/")[1]) === currentMonth) {
                            // check for data's operation
                            switch (eventArrDelta[w].operation) {
                                case "insert":
                                    expCount = expCount + 1;
                                    actCount = actCount + 1;
                                    break;
                                case "delete":
                                    expCount = expCount - 1;
                                    actCount = actCount - 1;
                                    break;
                                case "leave": // have to check for all day off event cause it will affect actCount too
                                    if (eventArrDelta[w].offHours.split("-")[0].split(" ")[0].split("/")[2] !== eventArrDelta[w].offHours.split("-")[1].split(" ")[0].split("/")[2]) { // check for cross day data
                                        offCount = offCount + (Date.parse('1970-01-01T24:00')/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600 + Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600);
                                        if ((Date.parse('1970-01-01T24:00')/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600 + Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600) === 9.5) {
                                            actCount = actCount - 1;
                                        }
                                    }
                                    else { // not cross day data
                                        offCount = offCount + (Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600);
                                        if ((Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600) === 9.5) {
                                            actCount = actCount - 1;
                                        }
                                    }
                                    break;
                                case "leaveRmv": // have to check for all day off event cause it will affect actCount too
                                    if (eventArrDelta[w].offHours.indexOf("*") !== -1) { // check for cross day data
                                        var tempDelta = eventArrDelta[w].offHours.replace("*", ""); // get rid of *
                                        offCount = offCount - (Date.parse('1970-01-01T24:00')/1000/3600 - Date.parse('1970-01-01T' + tempDelta.split("-")[0])/1000/3600 + Date.parse('1970-01-01T' + tempDelta.split("-")[1])/1000/3600);
                                    }
                                    else if (eventArrDelta[w].offHours === "All day") {
                                        offCount = offCount - 9.5;
                                        actCount = actCount + 1;
                                    }
                                    else { // not cross day data
                                        var tempDelta = eventArrDelta[w].offHours.replace("*", "");
                                        offCount = offCount - (Date.parse('1970-01-01T' + tempDelta.split("-")[1])/1000/3600 - Date.parse('1970-01-01T' + tempDelta.split("-")[0])/1000/3600);
                                    }
                                    break;
                            }
                        }
                    }
                    if (expCount === 0) {
                        document.getElementById(emplName + "DutyDays").innerHTML = initExp + expCount; // update digit of Exp
                        $('#' + emplName + "DutyDays").css("color", "black"); // OFF
                    }
                    else {
                        document.getElementById(emplName + "DutyDays").innerHTML = initExp + expCount; // update digit of Exp
                        $('#' + emplName + "DutyDays").css("color", "red"); // ON
                    }
                    if (actCount === 0) {
                        document.getElementById(emplName + "ActDays").innerHTML = initAct + actCount; // update digit of Act
                        $('#' + emplName + "ActDays").css("color", "black"); // OFF
                    }
                    else {
                        document.getElementById(emplName + "ActDays").innerHTML = initAct + actCount; // update digit of Act
                        $('#' + emplName + "ActDays").css("color", "red"); // ON
                    }
                    if (offCount === 0) {
                        document.getElementById(emplName + "OffHours").innerHTML = initOff + offCount; // update digit of Act
                        $('#' + emplName + "OffHours").css("color", "black"); // OFF
                    }
                    else {
                        document.getElementById(emplName + "OffHours").innerHTML = initOff + offCount; // update digit of Act
                        $('#' + emplName + "OffHours").css("color", "red"); // ON
                    }
                }
                /* update one employee's Exp duty/ Act work/ Off hour digit color
                ---------------------------------------------------------------------*/
                // function updateColor (emplName) {
                //     var moment = $('#calendar').fullCalendar('getDate');
                //     currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                //     currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);

                //     var turnExpToRed = false;
                //     var expCount = 0;
                //     var actCount = 0;
                //     var offCount = 0;
                //     for (var w = 0; w < eventArrDelta.length; w++) {
                //         // filter for eventArrDelta data which exists in current year/ month
                //         if (parseInt(eventArrDelta[w].shiftDate.split("/")[0]) === currentYear && parseInt(eventArrDelta[w].shiftDate.split("/")[1]) === currentMonth) {
                //             // check for data's operation
                //             switch (eventArrDelta[w].operation) {
                //                 case "insert":
                //                     expCount = expCount + 1;
                //                     actCount = actCount + 1;
                //                     break;
                //                 case "delete":
                //                     expCount = expCount - 1;
                //                     actCount = actCount - 1;
                //                     break;
                //                 case "leave": // have to check for all day off event cause it will affect actCount too
                //                     if (eventArrDelta[w].offHours.split("-")[0].split(" ")[0].split("/")[2] !== eventArrDelta[w].offHours.split("-")[1].split(" ")[0].split("/")[2]) { // check for cross day data
                //                         offCount = offCount + (Date.parse('1970-01-01T24:00')/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600 + Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600);
                //                         if ((Date.parse('1970-01-01T24:00')/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600 + Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600) === 9.5) {
                //                             actCount = actCount - 1;
                //                         }
                //                     }
                //                     else { // not cross day data
                //                         offCount = offCount + (Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600);
                //                         if ((Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[1].split(" ")[1])/1000/3600 - Date.parse('1970-01-01T' + eventArrDelta[w].offHours.split("-")[0].split(" ")[1])/1000/3600) === 9.5) {
                //                             actCount = actCount - 1;
                //                         }
                //                     }
                //                     break;
                //                 case "leaveRmv": // have to check for all day off event cause it will affect actCount too
                //                     if (eventArrDelta[w].offHours.split("-")[0].split(" ")[0].split("/")[2] !== eventArrDelta[w].offHours.split("-")[1].split(" ")[0].split("/")[2]) { // check for cross day data
                //                         var tempDelta = eventArrDelta[w].offHours.replace("*", ""); // get rid of *
                //                         offCount = offCount - (Date.parse('1970-01-01T24:00')/1000/3600 - Date.parse('1970-01-01T' + tempDelta.split("-")[0])/1000/3600 + Date.parse('1970-01-01T' + tempDelta.split("-")[1])/1000/3600);
                //                     }
                //                     else if (eventArrDelta[w].offHours === "All day") {
                //                         offCount = offCount - 9.5;
                //                         actCount = actCount + 1;
                //                     }
                //                     else { // not cross day data
                //                         var tempDelta = eventArrDelta[w].offHours.replace("*", "");
                //                         offCount = offCount - (Date.parse('1970-01-01T' + tempDelta.split("-")[1])/1000/3600 - Date.parse('1970-01-01T' + tempDelta.split("-")[0])/1000/3600);
                //                     }
                //                     break;
                //             }
                //         }
                //     }
                //     if (expCount === 0) {
                //         $('#' + emplName + "DutyDays").css("color", "black"); // OFF
                //     }
                //     else {
                //         $('#' + emplName + "DutyDays").css("color", "red"); // ON
                //     }
                //     if (actCount === 0) {
                //         $('#' + emplName + "ActDays").css("color", "black"); // OFF
                //     }
                //     else {
                //         $('#' + emplName + "ActDays").css("color", "red"); // ON
                //     }
                //     if (offCount === 0) {
                //         $('#' + emplName + "OffHours").css("color", "black"); // OFF
                //     }
                //     else {
                //         $('#' + emplName + "OffHours").css("color", "red"); // ON
                //     }
                // }
                /* input workdays to calendar
                -------------------------------------------------------------------*/
                $("#addWorkDay").click(function() {
                    var moment = $('#calendar').fullCalendar('getDate');
                    currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                    currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);

                    var startDate = $("#datetimepicker1").val();
                    var endDate = $("#datetimepicker2").val();
                    var shiftType = $('input:radio[name=shiftType]:checked').val();
                    var startPieces = startDate.split("/");
                    var startYear = parseInt(startPieces[0]);
                    var startMonth = parseInt(startPieces[1]);
                    var startDay = parseInt(startPieces[2]);
                    var endPieces = endDate.split("/");
                    var endYear = parseInt(endPieces[0]);
                    var endMonth = parseInt(endPieces[1]);
                    var endDay = parseInt(endPieces[2]);
                    var eventByAddWorkDay = {};
                    
                    if (startYear !== endYear || startMonth !== endMonth) { // make sure start/end year/month have same value
                        alertify.showFailure('You could only input values with same year/month.');
                    }
                    else if (startDate > endDate) {
                        alertify.showFailure("End date must be equal/greater than start date.");
                    }
                    else if (startMonth !== currentMonth || startYear !== currentYear) {
                        alertify.showFailure("Shift month could not be different from current month.");
                    }
                    else {
                        switch (shiftType) {
                            case  "Night":
                                if (returnShift2(nocUserName + "\n" + startYear + "/" + startMonth + "/" + startDay) === "Swing") {
                                    alertify.showFailure("Night shift could not be added right after swing shift");
                                }
                                else {
                                    for (var i = startDay; i <= endDay; i++) {
                                        eventByAddWorkDay = {
                                            eventIdentity: nocUserName + "\n" + startYear + "/" + startMonth + "/" + i,
                                            shift: 'Night',
                                            color: nocUserColor,
                                            start: startYear + "/" + startMonth + "/" + i + " 00:00",
                                            end: endYear + "/" + endMonth + "/" + i + " 09:30",
                                            description: "On: night shift",
                                            editable: true
                                        };
                                        if (dateValidation(eventByAddWorkDay.eventIdentity)) return;
                                        $('#calendar').fullCalendar('renderEvent', eventByAddWorkDay, true); // true = the event permanetly fixed
                                        eventArr.push(eventByAddWorkDay);

                                        // update digits and colours of Exp duty/ Act work
                                        // updateExpActOff(nocUserName.split("_")[0], 1, 1, 0);
                                        // before pushing into eventArrDelta, check if there's any redundancy
                                        var operationNindex = checkOperationAndIndex1(eventByAddWorkDay.eventIdentity, eventByAddWorkDay.shift);
                                        var operationCheck = operationNindex.split("@")[0];
                                        var indexCheck = operationNindex.split("@")[1];
                                        switch (operationCheck) {
                                            case "none":
                                                eventArrDelta.push(new shiftToGo(eventByAddWorkDay.eventIdentity.split("\n")[0], eventByAddWorkDay.eventIdentity.split("\n")[1], eventByAddWorkDay.shift, "insert", "none")); // saving data to eventArrDelta before sending to database
                                                break;
                                            case "insert":
                                                alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                break;
                                            case "delete":
                                                eventArrDelta.splice(indexCheck, 1);
                                                break;
                                            default:
                                                alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert/delete");
                                                break;
                                        }
                                    }
                                }
                                break;
                            case "Day":
                                for (var i = startDay; i <= endDay; i++) {
                                    eventByAddWorkDay = {
                                        eventIdentity: nocUserName + "\n" + startYear + "/" + startMonth + "/" + i,
                                        shift: 'Day',
                                        color: nocUserColor,
                                        start: startYear + "/" + startMonth + "/" + i + " 09:00",
                                        end: endYear + "/" + endMonth + "/" + i + " 18:30",
                                        description: "On: day shift",
                                        editable: true
                                    };
                                    if (dateValidation(eventByAddWorkDay.eventIdentity)) return;
                                    $('#calendar').fullCalendar('renderEvent', eventByAddWorkDay, true); // true = the event permanetly fixed
                                    eventArr.push(eventByAddWorkDay);
                                    
                                    // update digits of Exp duty/ Act work
                                    // updateExpActOff(nocUserName.split("_")[0], 1, 1, 0);
                                    // before pushing into eventArrDelta, check if there's any redundancy
                                    var operationNindex = checkOperationAndIndex1(eventByAddWorkDay.eventIdentity, eventByAddWorkDay.shift);
                                    var operationCheck = operationNindex.split("@")[0];
                                    var indexCheck = operationNindex.split("@")[1];
                                    switch (operationCheck) {
                                        case "none":
                                            eventArrDelta.push(new shiftToGo(eventByAddWorkDay.eventIdentity.split("\n")[0], eventByAddWorkDay.eventIdentity.split("\n")[1], eventByAddWorkDay.shift, "insert", "none")); // saving data to eventArrDelta before sending to database
                                            break;
                                        case "insert":
                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                            break;
                                        case "delete":
                                            eventArrDelta.splice(indexCheck, 1);
                                            break;
                                        default:
                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert/delete");
                                            break;
                                    }
                                }
                                break;
                            case "Swing":
                                for (var i = startDay; i <= endDay; i++) {
                                    eventByAddWorkDay = {
                                        eventIdentity: nocUserName + "\n" + startYear + "/" + startMonth + "/" + i,
                                        shift: 'Swing',
                                        color: nocUserColor,
                                        start: startYear + "/" + startMonth + "/" + i + " 15:00",
                                        end: endYear + "/" + endMonth + "/" + (i+1) + " 00:30",
                                        description: "On: swing shift",
                                        editable: true
                                    };
                                    if (dateValidation(eventByAddWorkDay.eventIdentity)) return;
                                    $('#calendar').fullCalendar('renderEvent', eventByAddWorkDay, true); // true = the event permanetly fixed
                                    eventArr.push(eventByAddWorkDay);

                                    // update digits of Exp duty/ Act work
                                    // updateExpActOff(nocUserName.split("_")[0], 1, 1, 0);
                                    // before pushing into eventArrDelta, check if there's any redundancy
                                    var operationNindex = checkOperationAndIndex1(eventByAddWorkDay.eventIdentity, eventByAddWorkDay.shift);
                                    var operationCheck = operationNindex.split("@")[0];
                                    var indexCheck = operationNindex.split("@")[1];
                                    switch (operationCheck) {
                                        case "none":
                                            eventArrDelta.push(new shiftToGo(eventByAddWorkDay.eventIdentity.split("\n")[0], eventByAddWorkDay.eventIdentity.split("\n")[1], eventByAddWorkDay.shift, "insert", "none")); // saving data to eventArrDelta before sending to database
                                            break;
                                        case "insert":
                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                            break;
                                        case "delete":
                                            eventArrDelta.splice(indexCheck, 1);
                                            break;
                                        default:
                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/delete");
                                            break;
                                    }
                                }
                                break;
                            default:
                                alertify.showWarning("Please choose your shift type.");
                                break;
                        }
                    }
                    // swap image of submit button
                    if (eventArrDelta.length !== 0) {
                        swapImage();
                        $( "#nocUser" ).selectmenu({
                            disabled: true
                        });
                        
                    }
                    else {
                        swapImage2();
                        $( "#nocUser" ).selectmenu({
                            disabled: false
                        });
                    }
                    // updateColor(nocUserName.split("_")[0]);
                    updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                });
                /* check if there's any off-hour event existed in between
                --------------------------------------------------------------------*/
                function offEventValidation (userName, startPoint, endPoint) {
                    var offEventExists = false;
                    for (var i = 0; i < eventArr.length; i++) {
                        var tempPieces = eventArr[i].eventIdentity.split("\n");
                        var tempPiece = tempPieces[1].split("/");
                        var tempDay = tempPiece[2];
                        if (tempPieces[0] === userName && startPoint <= tempDay && tempDay <= endPoint && eventArr[i].textColor === "black") {
                            offEventExists = true;
                            break;
                        }
                    }
                    return offEventExists;
                }
                /* check if there's any off-hour event existed in between
                --------------------------------------------------------------------*/
                function offEventValidation2 (userName, startPoint, endPoint) {
                    var leaveRmvExists = false;
                    for (var i = 0; i < eventArrDelta.length; i++) {
                        var tempDay = eventArrDelta[i].shiftDate.split("/")[2];
                        if (eventArrDelta[i].userName === userName && startPoint <= tempDay && tempDay <= endPoint && eventArrDelta[i].operation === "leaveRmv") {
                            leaveRmvExists = true;
                            break;
                        }
                    }
                    return leaveRmvExists;
                }
                /* return shift type of the day that is going to add an off-hour event
                --------------------------------------------------------------------*/
                var returnShift = function (NameAndDayGoingTo) {
                    for (var i = 0; i < eventArr.length; i++) {
                        if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                            return eventArr[i].shift;
                        }
                    }
                }
                /* return shift type of the previous day of the day that is going to add an off-hour event
                --------------------------------------------------------------------*/
                var returnShift2 = function (NameAndDayGoingTo) {
                    var year = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[0]);
                    var month = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[1]);
                    var day = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[2]);
                    var NameAndDayGoingTo2 = NameAndDayGoingTo.split("\n")[0] + "\n" + year + "/" + month + "/" + (day-1)
                    for (var i = 0; i < eventArr.length; i++) {
                        if (eventArr[i].eventIdentity === NameAndDayGoingTo2) {
                            return eventArr[i].shift;
                        }
                    }
                }
                /* adding leave for all day events
                --------------------------------------------------------------------*/
                var allDayOff = function (ShiftType, NameAndDayGoingTo) {
                    switch (ShiftType) {
                        case "Night":
                            for (var i = 0; i < eventArr.length; i++) {
                                if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                                    eventArr[i].title = "All day";
                                    eventArr[i].textColor = "black";
                                    eventArr[i].description = "Off: 00:00-09:30";
                                    break;
                                }
                            }
                            // before pushing into eventArrDelta, check if there's any redundancy
                            var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo, "Night");
                            var operationCheck = operationNindex.split("@")[0];
                            var indexCheck = operationNindex.split("@")[1];
                            switch (operationCheck) {
                                case "none":
                                    eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " 00:00-" + NameAndDayGoingTo.split("\n")[1] + " 09:30")); // saving data to eventArrDelta before sending to database
                                    break;
                                case "leave":
                                    alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                    break;
                                case "leaveRmv":
                                    eventArrDelta.splice(indexCheck, 1);
                                    break;
                                default:
                                    alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                                    break;
                            }
                            break;
                        case "Day":
                            for (var i = 0; i < eventArr.length; i++) {
                                if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                                    eventArr[i].title = "All day";
                                    eventArr[i].textColor = "black";
                                    eventArr[i].description = "Off: 09:00-18:30";
                                    break;
                                }
                            }
                            // before pushing into eventArrDelta, check if there's any redundancy
                            var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo, "Day");
                            var operationCheck = operationNindex.split("@")[0];
                            var indexCheck = operationNindex.split("@")[1];
                            switch (operationCheck) {
                                case "none":
                                    eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " 09:00-" + NameAndDayGoingTo.split("\n")[1] + " 18:30")); // saving data to eventArrDelta before sending to database
                                    break;
                                case "leave":
                                    alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                    break;
                                case "leaveRmv":
                                    eventArrDelta.splice(indexCheck, 1);
                                    break;
                                default:
                                    alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                                    break;
                            }
                            break;
                        case "Swing":
                            var year = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[0]);
                            var month = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[1]);
                            var day = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[2]);
                            for (var i = 0; i < eventArr.length; i++) {
                                if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                                    eventArr[i].title = "All day";
                                    eventArr[i].textColor = "black";
                                    eventArr[i].description = "Off: 15:00-00:30*";
                                    break;
                                }
                            }
                            // before pushing into eventArrDelta, check if there's any redundancy
                            var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo, "Swing");
                            var operationCheck = operationNindex.split("@")[0];
                            var indexCheck = operationNindex.split("@")[1];
                            switch (operationCheck) {
                                case "none":
                                    eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " 15:00-" + year + "/" + month + "/" + (day+1) + " 00:30")); // saving data to eventArrDelta before sending to database
                                    break;
                                case "leave":
                                    alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                    break;
                                case "leaveRmv":
                                    eventArrDelta.splice(indexCheck, 1);
                                    break;
                                default:
                                    alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                                    break;
                            }
                            break;
                    }
                }
                /* adding leave for non-all day events
                --------------------------------------------------------------------*/
                var offHourUpdate = function (ShiftType, NameAndDayGoingTo, StartTime, EndTime) {
                    switch (ShiftType) {
                        case "Night":
                            for (var i = 0; i < eventArr.length; i++) {
                                if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                                    eventArr[i].title = StartTime + "-" + EndTime;
                                    eventArr[i].textColor = "black";
                                    eventArr[i].description = "Off: " + StartTime + " - " + EndTime;
                                    break;
                                }
                            }
                            // before pushing into eventArrDelta, check if there's any redundancy
                            var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo, "Night");
                            var operationCheck = operationNindex.split("@")[0];
                            var indexCheck = operationNindex.split("@")[1];
                            switch (operationCheck) {
                                case "none":
                                    eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " " + StartTime + "-" + NameAndDayGoingTo.split("\n")[1] + " " + EndTime)); // saving data to eventArrDelta before sending to database
                                    break;
                                case "leave":
                                    alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                    break;
                                case "leaveRmv":
                                    eventArrDelta.splice(indexCheck, 1);
                                    break;
                                default:
                                    alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                                    break;
                            }
                            break;
                        case "Day":
                            for (var i = 0; i < eventArr.length; i++) {
                                if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                                    eventArr[i].title = StartTime + "-" + EndTime;
                                    eventArr[i].textColor = "black";
                                    eventArr[i].description = "Off: " + StartTime + " - " + EndTime;
                                    break;
                                }
                            }
                            // before pushing into eventArrDelta, check if there's any redundancy
                            var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo, "Day");
                            var operationCheck = operationNindex.split("@")[0];
                            var indexCheck = operationNindex.split("@")[1];
                            switch (operationCheck) {
                                case "none":
                                    eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " " + StartTime + "-" + NameAndDayGoingTo.split("\n")[1] + " " + EndTime)); // saving data to eventArrDelta before sending to database
                                    break;
                                case "leave":
                                    alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                    break;
                                case "leaveRmv":
                                    eventArrDelta.splice(indexCheck, 1);
                                    break;
                                default:
                                    alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                                    break;
                            }
                            break;
                        case "Swing":
                            var year = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[0]);
                            var month = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[1]);
                            var day = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[2]);
                            for (var i = 0; i < eventArr.length; i++) {
                                if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                                    eventArr[i].title = StartTime + "-" + EndTime + "*";
                                    eventArr[i].textColor = "black";
                                    eventArr[i].description = "Off: " + StartTime + " - " + EndTime + "*";
                                    break;
                                }
                            }
                            // before pushing into eventArrDelta, check if there's any redundancy
                            var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo, "Swing");
                            var operationCheck = operationNindex.split("@")[0];
                            var indexCheck = operationNindex.split("@")[1];
                            switch (operationCheck) {
                                case "none":
                                    eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " " + StartTime + "-" + year + "/" + month + "/" + (day+1) + " " + EndTime)); // saving data to eventArrDelta before sending to database
                                    break;
                                case "leave":
                                    alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                    break;
                                case "leaveRmv":
                                    eventArrDelta.splice(indexCheck, 1);
                                    break;
                                default:
                                    alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                                    break;
                            }
                            break;
                    }
                }
                /* adding off hour event to swing shift on first "SINGLE" day
                --------------------------------------------------------------------*/
                var offHourUpdateSwing = function (ShiftType, NameAndDayGoingTo, StartTime, EndTime) {
                    switch (ShiftType) {
                        case "Swing":
                            var year = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[0]);
                            var month = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[1]);
                            var day = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[2]);
                            for (var i = 0; i < eventArr.length; i++) {
                                if (eventArr[i].eventIdentity === NameAndDayGoingTo) {
                                    eventArr[i].title = StartTime + "-" + EndTime;
                                    eventArr[i].textColor = "black";
                                    eventArr[i].description = "Off: " + StartTime + " - " + EndTime;
                                    break;
                                }
                            }
                            // before pushing into eventArrDelta, check if there's any redundancy
                            var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo, "Swing");
                            var operationCheck = operationNindex.split("@")[0];
                            var indexCheck = operationNindex.split("@")[1];
                            switch (operationCheck) {
                                case "none":
                                    eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " " + StartTime + "-" + NameAndDayGoingTo.split("\n")[1] + " " + EndTime)); // saving data to eventArrDelta before sending to database
                                    break;
                                case "leave":
                                    alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                    break;
                                case "leaveRmv":
                                    eventArrDelta.splice(indexCheck, 1);
                                    break;
                                default:
                                    alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                                    break;
                            }
                            break;
                    }
                }
                /* adding off hour event to swing shift on second "SINGLE" day
                --------------------------------------------------------------------*/
                // var offHourUpdateSwing2 = function (ShiftType, NameAndDayGoingTo, StartTime, EndTime) {
                //     switch (ShiftType) {
                //         case "Swing":
                //             var year = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[0]);
                //             var month = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[1]);
                //             var day = parseInt(NameAndDayGoingTo.split("\n")[1].split("/")[2]);
                //             var NameAndDayGoingTo2 = NameAndDayGoingTo.split("\n")[0] + "\n" + year + "/" + month + "/" + (day-1);
                //             for (var i = 0; i < eventArr.length; i++) {
                //                 if (eventArr[i].eventIdentity === NameAndDayGoingTo2) {
                //                     eventArr[i].title = StartTime + "*-" + EndTime + "*";
                //                     eventArr[i].textColor = "black";
                //                     eventArr[i].description = "Ask to leave from\n" + NameAndDayGoingTo.split("\n")[1] + " " + StartTime + "* to\n"
                //                                             + NameAndDayGoingTo.split("\n")[1] + " " + EndTime + "*";
                //                     break;
                //                 }
                //             }
                //             // before pushing into eventArrDelta, check if there's any redundancy
                //             var operationNindex = checkOperationAndIndex2(NameAndDayGoingTo);
                //             var operationCheck = operationNindex.split("@")[0];
                //             var indexCheck = operationNindex.split("@")[1];
                //             switch (operationCheck) {
                //                 case "none":
                //                     eventArrDelta.push(new shiftToGo(NameAndDayGoingTo.split("\n")[0], NameAndDayGoingTo.split("\n")[1], ShiftType, "leave", NameAndDayGoingTo.split("\n")[1] + " " + StartTime + "-" + NameAndDayGoingTo.split("\n")[1] + " " + EndTime)); // saving data to eventArrDelta before sending to database
                //                     break;
                //                 case "leave":
                //                     alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                //                     break;
                //                 case "leaveRmv":
                //                     eventArrDelta.splice(indexCheck, 1);
                //                     break;
                //                 default:
                //                     alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leaveRmv");
                //                     break;
                //             }
                //             break;
                //     }
                // }
                /* input offhour to calendar
                --------------------------------------------------------------------*/
                $("#addOffHour").click(function() {
                    var moment = $('#calendar').fullCalendar('getDate');
                    currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                    currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);

                    var startOffDate = $("#datetimepicker3").val();
                    var startOffPiece = startOffDate.split(" ");
                    var startOffPieces = startOffPiece[0].split("/");
                    var startOffYear = parseInt(startOffPieces[0]);
                    var startOffMonth = parseInt(startOffPieces[1]);
                    var startOffDay = parseInt(startOffPieces[2]);
                    var startOffTime = startOffPiece[1];
                    var endOffDate = $("#datetimepicker4").val();
                    var endOffPiece = endOffDate.split(" ");
                    var endOffPieces = endOffPiece[0].split("/");
                    var endOffYear = parseInt(endOffPieces[0]);
                    var endOffMonth = parseInt(endOffPieces[1]);
                    var endOffDay = parseInt(endOffPieces[2]);
                    var endOffTime = endOffPiece[1];
                    if (startOffYear !== endOffYear || startOffMonth !== endOffMonth) {
                        alertify.showFailure("You could only input values with same year/month.");
                    }
                    else if (startOffYear !== currentYear || startOffMonth !== currentMonth) {
                        alertify.showFailure("Shift month could not be different from current month.");
                    }
                    else if (startOffDate >= endOffDate) {
                        alertify.showFailure("End date/time must be greater than start date/time."); // alert if end date is less than start date
                    }
                    else if (offEventValidation(nocUserName, startOffDay, endOffDay)) { // alert if there's any off-hour day between startOffDate and endOffDate
                        alertify.showFailure("You could not ask to leave twice in a day.");
                    }
                    else if (offEventValidation2(nocUserName, 1, 31)) {
                        alertify.showWarning("Please save your changes first.");
                    }
                    else {
                        var recordOff = 0;
                        for (var i = startOffDay; i <= endOffDay; i++) {
                            var offEventID = nocUserName + "\n" + startOffYear + "/" + startOffMonth + "/" + i;
                            switch (returnShift(offEventID)) { // check shift type first
                                case "Night":
                                    if (startOffDay === endOffDay) { // night shift and input start/end date are the same
                                        if (startOffTime <= "00:00" && "09:30" <= endOffTime) {
                                            allDayOff("Night", offEventID);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            if ("09:30" <= startOffTime) {
                                                alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                            }
                                            else {
                                                if ("09:30" < endOffTime) {
                                                    offHourUpdate("Night", offEventID, startOffTime, "09:30");
                                                    // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T09:30:00')/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                    // updateColor(nocUserName.split("_")[0]);
                                                    updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                    recordOff++;
                                                }
                                                else {
                                                    offHourUpdate("Night", offEventID, startOffTime, endOffTime);
                                                    // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                    // updateColor(nocUserName.split("_")[0]);
                                                    updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                    recordOff++;
                                                }
                                            }
                                        }
                                    }
                                    else if (i === startOffDay) { // for the first day of night shift and input start/end date are different (end time = 09:30)
                                        if (startOffTime <= "00:00") {
                                            allDayOff("Night", offEventID);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            if ("09:30" < startOffTime) {
                                                alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                            }
                                            else {
                                                offHourUpdate("Night", offEventID, startOffTime, "09:30");
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T09:30:00')/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                        }
                                    }
                                    else if (i === endOffDay) { // for the last day of night shift and input start/end date are different (start time = 00:00)
                                        if ("09:30" <= endOffTime) {
                                            allDayOff("Night", offEventID);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            if (endOffTime <= "00:00") {
                                                alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                            }
                                            else {
                                                offHourUpdate("Night", offEventID, "00:00", endOffTime);
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T00:00:00')/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                        }
                                    }
                                    else {
                                        allDayOff("Night", offEventID);
                                        // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                        // updateColor(nocUserName.split("_")[0]);
                                        updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                        recordOff++;
                                    }
                                    break;
                                case "Day":
                                    if (startOffDay === endOffDay) { // day shift and input start/end date are the same
                                        if (startOffTime <= "09:00" && "18:30" <= endOffTime) {
                                            allDayOff("Day", offEventID);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            if (startOffTime <= "09:00" && endOffTime <= "09:00") { // logically I could ignore startOffTime <= "09:00"
                                                alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                            }
                                            else if (startOffTime <= "09:00" && "09:00" <= endOffTime && endOffTime < "18:30") {
                                                offHourUpdate("Day", offEventID, "09:00", endOffTime);
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T09:00:00')/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                            else if ("09:00" < startOffTime && endOffTime <= "18:30") { // I logically ingored startOffTime <= "18:30" && "09:00" <= endOffTime
                                                offHourUpdate("Day", offEventID, startOffTime, endOffTime);
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                            else if ("09:00" < startOffTime && "18:30" <= endOffTime) { // I logically ingored startOffTime <= "18:30"
                                                offHourUpdate("Day", offEventID, startOffTime, "18:30");
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T18:30:00')/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                            else { // means every situation when "18:30" <= startOffTime
                                                alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                            }
                                        }
                                    }
                                    else if (i === startOffDay) { // for the first day of day shift and input start/end date are different (end time = 18:30)
                                        if (startOffTime <= "09:00") {
                                            allDayOff("Day", offEventID);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            if ("09:00" < startOffTime && startOffTime < "18:30") {
                                                offHourUpdate("Day", offEventID, startOffTime, "18:30");
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T18:30:00')/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                            else { // means every situation when "18:30" <= startOffTime
                                                alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                            }
                                        }
                                    }
                                    else if (i === endOffDay) { // for the last day of day shift and input start/end date are different (start time = 09:00)
                                        if ("18:30" <= endOffTime) {
                                            allDayOff("Day", offEventID);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            if (endOffTime <= "09:00") {
                                                alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                            }
                                            else { // means every situation when "09:00" < endOffTime && endOffTime < "18:30"
                                                offHourUpdate("Day", offEventID, "09:00", endOffTime);
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T09:00:00')/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                        }
                                    }
                                    else {
                                        allDayOff("Day", offEventID);
                                        // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                        // updateColor(nocUserName.split("_")[0]);
                                        updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                        recordOff++;
                                    }
                                    break;
                                case "Swing":
                                    if (startOffDay === endOffDay) { // no all day off event
                                        if (startOffTime <= "15:00" && endOffTime <= "15:00") {
                                            alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                        }
                                        else if (startOffTime < "15:00" && "15:00" < endOffTime) {
                                            offHourUpdateSwing("Swing", offEventID, "15:00", endOffTime);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T15:00:00')/1000/3600);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else if ("15:00" <= startOffTime && "15:00" < endOffTime) {
                                            offHourUpdateSwing("Swing", offEventID, startOffTime, endOffTime);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                    }
                                    else if (i === startOffDay) { // for the first day of swing shift and input start/end date are different (end time could not be 00:30)
                                        if (startOffTime <= "15:00") {
                                            if ((i+1) === endOffDay && endOffTime < "00:30") {
                                                offHourUpdate("Swing", offEventID, "15:00", endOffTime);
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 + Date.parse('1970-01-01T09:00:00')/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                            else {
                                                allDayOff("Swing", offEventID);
                                                // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                        }
                                        else { // means all situation when "15:00" < startOffTime
                                            if ((i+1) === endOffDay && endOffTime <= "00:30") {
                                                offHourUpdate("Swing", offEventID, startOffTime, endOffTime);
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 + Date.parse('1970-01-01T24:00:00')/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                            else { // means all situations after (i+1) === endOffDay && "00:30" < endOffTime
                                                offHourUpdate("Swing", offEventID, startOffTime, "00:30");
                                                // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T24:00:00')/1000/3600 + Date.parse('1970-01-01T00:30:00')/1000/3600 - Date.parse('1970-01-01T' + startOffTime)/1000/3600);
                                                // updateColor(nocUserName.split("_")[0]);
                                                updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                                recordOff++;
                                            }
                                        }
                                    }
                                    else if (i === endOffDay) { // for the last day of swing shift, and no all day event could be on the input end date
                                        if ("15:00" < endOffTime) { // startOffTime regards as "15:00"
                                            offHourUpdateSwing("Swing", offEventID, "15:00", endOffTime);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T15:00:00')/1000/3600);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            alertify.showWarning("You will not add off hour event on " + offEventID.split("\n")[1]);
                                        }
                                    }
                                    else { // could not be all day off if input end date/time does not fit
                                        if ((i+1) === endOffDay && endOffTime < "00:30") { // startOffTime regards as "00:30"
                                            offHourUpdate("Swing", offEventID, "15:00", endOffTime);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, 0, Date.parse('1970-01-01T' + endOffTime)/1000/3600 - Date.parse('1970-01-01T15:00:00')/1000/3600);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                        else {
                                            allDayOff("Swing", offEventID);
                                            // updateExpActOff(nocUserName.split("_")[0], 0, -1, 9.5);
                                            // updateColor(nocUserName.split("_")[0]);
                                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                            recordOff++;
                                        }
                                    }
                                    break;
                                default:
                                    // if (returnShift2(offEventID) === "Swing") {
                                    //     if (startOffDay === endOffDay) {
                                    //         if (startOffTime <= "00:30" && endOffTime <= "00:30") {
                                    //             offHourUpdateSwing2("Swing", offEventID, startOffTime, endOffTime);
                                    //             recordOff++;
                                    //         }
                                    //         else if (startOffTime < "00:30" && "00:30" <= endOffTime) {
                                    //             offHourUpdateSwing2("Swing", offEventID, startOffTime, "00:30");
                                    //             recordOff++;
                                    //         }
                                    //         else {
                                    //             alertify.showFailure("You will not add off hour event on " + offEventID.split("\n")[1]);
                                    //         }
                                    //     }
                                    //     else if (i === startOffDay && startOffTime < "00:30") { // different input days, so endOffDay regards as startOffDay && end time regards as "00:30"
                                    //         offHourUpdateSwing2("Swing", offEventID, startOffTime, "00:30");
                                    //         recordOff++;
                                    //     }
                                    //     else {
                                    //         alertify.showFailure("You will not add off hour event on " + offEventID.split("\n")[1]);
                                    //     }
                                    // }
                                    // else {
                                    alertify.showFailure("Please add shift on " + offEventID.split("\n")[1] + " first.");
                                    // }
                                    break;
                            }
                        }
                        if (recordOff !== 0) {
                            $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                            // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                            $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                checkedMembers($(this).val());
                            });
                            recordOff = 0;
                        }
                    }
                    if (eventArrDelta.length !== 0) {
                        swapImage();
                        $( "#nocUser" ).selectmenu({
                            disabled: true
                        });
                    }
                    else {
                        swapImage2();
                        $( "#nocUser" ).selectmenu({
                            disabled: false
                        });
                    }
                });
                /* initiate calendar
                --------------------------------------------------------------------*/
                $('#calendar').fullCalendar({
                    editable: false,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    /* click to delete event 
                    --------------------------------------------------------------------*/
                    eventClick: function (calEvent, jsEvent, view) {
                        if (calEvent.editable == undefined) return;
                        if (calEvent.eventIdentity.split("\n")[0] === nocUserName) {
                            if (calEvent.textColor === "black") { // for off hour events
                                alertify.confirm("CONFIRMATION", // title
                                    "Remove off hours on <font style='color: red'>" + calEvent.eventIdentity.split("\n")[1] + "</font> ?", // description
                                    function(){ // do something when clicking ok 
                                        for (var i = 0; i < eventArr.length; i++) {
                                            if (eventArr[i].eventIdentity === calEvent.eventIdentity) {
                                                eventArr[i].title = " ";
                                                eventArr[i].textColor = "";
                                                eventArr[i].description = "On: " + eventArr[i].shift + " shift";
                                                break;
                                            }
                                        }
                                        // before pushing into eventArrDelta, check if there's any redundancy
                                        var operationNindex = checkOperationAndIndex2(calEvent.eventIdentity, calEvent.shift);
                                        var operationCheck = operationNindex.split("@")[0];
                                        var indexCheck = operationNindex.split("@")[1];
                                        switch (operationCheck) {
                                            case "none":
                                                eventArrDelta.push(new shiftToGo(calEvent.eventIdentity.split("\n")[0], calEvent.eventIdentity.split("\n")[1], calEvent.shift, "leaveRmv", calEvent.title)); // saving data to eventArrDelta before sending to database
                                                break;
                                            case "leave":
                                                eventArrDelta.splice(indexCheck, 1);
                                                break;
                                            case "leaveRmv":
                                                alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                break;
                                            default:
                                                alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leave");
                                                break;
                                        }
                                        if (eventArrDelta.length !== 0) {
                                            swapImage();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: true
                                            });
                                        }
                                        else {
                                            swapImage2();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: false
                                            });
                                        }
                                        $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                                        // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                                        $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                            checkedMembers($(this).val());
                                        });
                                        // to update digits on Off hour
                                        // if (calEvent.title === "All day") {
                                        //     updateExpActOff(nocUserName.split("_")[0], 0, 1, -9.5);
                                        //     updateColor(nocUserName.split("_")[0]);
                                        // }
                                        // else if (calEvent.title.indexOf("*") !== -1) { // check for cross day data
                                        //     var tempTitle1 = calEvent.title.replace("*", "");
                                        //     updateExpActOff(nocUserName.split("_")[0], 0, 0, -(Date.parse('1970-01-01T' + tempTitle1.split("-")[1])/1000/3600 + Date.parse('1970-01-01T24:00:00')/1000/3600 - Date.parse('1970-01-01T' + tempTitle1.split("-")[0])/1000/3600));
                                        //     updateColor(nocUserName.split("_")[0]);
                                        // }
                                        // else {
                                        //     var tempTitle2 = calEvent.title.replace("*", "");
                                        //     updateExpActOff(nocUserName.split("_")[0], 0, 0, -(Date.parse('1970-01-01T' + tempTitle2.split("-")[1])/1000/3600 - Date.parse('1970-01-01T' + tempTitle2.split("-")[0])/1000/3600));
                                        //     updateColor(nocUserName.split("_")[0]);
                                        // }
                                        updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff); //
                                        alertify.success('Off hours removed.');
                                    },
                                    function(){ // do something when clicking cancel
                                        alertify.error('You clicked cancel.');
                                    }
                                ).show();
                            }
                            else { // for duty events
                                alertify.confirm("CONFIRMATION", // title
                                    "Delete <font style='color: red'>" + calEvent.eventIdentity.split("\n")[1] + "</font> ?", // description
                                    function(){ // do something when clicking ok
                                        // $('#calendar').fullCalendar('removeEvents', calEvent);
                                        for (var i = 0; i < eventArr.length; i++) {
                                            if (eventArr[i].eventIdentity === calEvent.eventIdentity) {
                                                eventArr.splice(i,1);
                                                break;
                                            }
                                        }
                                        $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                                        // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                                        $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                            checkedMembers($(this).val());
                                        });

                                        // before pushing into eventArrDelta, check if there's any redundancy
                                        var operationNindex = checkOperationAndIndex1(calEvent.eventIdentity, calEvent.shift);
                                        var operationCheck = operationNindex.split("@")[0];
                                        var indexCheck = operationNindex.split("@")[1];
                                        switch (operationCheck) {
                                            case "none":
                                                eventArrDelta.push(new shiftToGo(calEvent.eventIdentity.split("\n")[0], calEvent.eventIdentity.split("\n")[1], calEvent.shift, "delete", "none")); // saving data to eventArrDelta before sending to database
                                                break;
                                            case "insert":
                                                eventArrDelta.splice(indexCheck, 1);
                                                break;
                                            case "delete":
                                                alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                break;
                                            default:
                                                alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert");
                                                break;
                                        }
                                        if (eventArrDelta.length !== 0) {
                                            swapImage();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: true
                                            });
                                        }
                                        else {
                                            swapImage2();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: false
                                            });

                                        }
                                        // update digits of Exp duty/ Act work
                                        // updateExpActOff(nocUserName.split("_")[0], -1, -1, 0);
                                        // updateColor(nocUserName.split("_")[0]);
                                        updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                                        alertify.success('Event deleted.');
                                    },
                                    function(){ // do something when clicking cancel
                                        alertify.error('You clicked cancel.');
                                    }
                                ).show();
                            }
                        }
                        else if (calEvent.eventIdentity.split("\n")[0] === loginUserName) {
                            if (calEvent.textColor === "black") { // for off hour events
                                alertify.confirm("CONFIRMATION", // title
                                    "Remove off hours on <font style='color: red'>" + calEvent.eventIdentity.split("\n")[1] + "</font> ?", // description
                                    function(){ // do something when clicking ok 
                                        for (var i = 0; i < eventArr.length; i++) {
                                            if (eventArr[i].eventIdentity === calEvent.eventIdentity) {
                                                eventArr[i].title = " ";
                                                eventArr[i].textColor = "";
                                                eventArr[i].description = "On: " + eventArr[i].shift + " shift";
                                                break;
                                            }
                                        }
                                        // before pushing into eventArrDelta, check if there's any redundancy
                                        var operationNindex = checkOperationAndIndex2(calEvent.eventIdentity, calEvent.shift);
                                        var operationCheck = operationNindex.split("@")[0];
                                        var indexCheck = operationNindex.split("@")[1];
                                        switch (operationCheck) {
                                            case "none":
                                                eventArrDelta.push(new shiftToGo(calEvent.eventIdentity.split("\n")[0], calEvent.eventIdentity.split("\n")[1], calEvent.shift, "leaveRmv", calEvent.title)); // saving data to eventArrDelta before sending to database
                                                break;
                                            case "leave":
                                                eventArrDelta.splice(indexCheck, 1);
                                                break;
                                            case "leaveRmv":
                                                alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                break;
                                            default:
                                                alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/leave");
                                                break;
                                        }
                                        if (eventArrDelta.length !== 0) {
                                            swapImage();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: true
                                            });
                                        }
                                        else {
                                            swapImage2();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: false
                                            });
                                        }
                                        $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                                        // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                                        $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                            checkedMembers($(this).val());
                                        });
                                        // to update digits on Off hour
                                        // if (calEvent.title === "All day") {
                                        //     updateExpActOff(nocUserName.split("_")[0], 0, 1, -9.5);
                                        //     updateColor(nocUserName.split("_")[0]);
                                        // }
                                        // else if (calEvent.title.indexOf("*") !== -1) { // check for cross day data
                                        //     var tempTitle1 = calEvent.title.replace("*", "");
                                        //     updateExpActOff(nocUserName.split("_")[0], 0, 0, -(Date.parse('1970-01-01T' + tempTitle1.split("-")[1])/1000/3600 + Date.parse('1970-01-01T24:00:00')/1000/3600 - Date.parse('1970-01-01T' + tempTitle1.split("-")[0])/1000/3600));
                                        //     updateColor(nocUserName.split("_")[0]);
                                        // }
                                        // else {
                                        //     var tempTitle2 = calEvent.title.replace("*", "");
                                        //     updateExpActOff(nocUserName.split("_")[0], 0, 0, -(Date.parse('1970-01-01T' + tempTitle2.split("-")[1])/1000/3600 - Date.parse('1970-01-01T' + tempTitle2.split("-")[0])/1000/3600));
                                        //     updateColor(nocUserName.split("_")[0]);
                                        // }
                                        updateDigitNColor(loginUserName.split("_")[0], initExp1, initAct1, initOff1); //
                                        alertify.success('Off hours removed.');
                                    },
                                    function(){ // do something when clicking cancel
                                        alertify.error('You clicked cancel.');
                                    }
                                ).show();
                            }
                            else { // for duty events
                                alertify.confirm("CONFIRMATION", // title
                                    "Delete <font style='color: red'>" + calEvent.eventIdentity.split("\n")[1] + " ?", // description
                                    function(){ // do something when clicking ok
                                        // $('#calendar').fullCalendar('removeEvents', calEvent);
                                        for (var i = 0; i < eventArr.length; i++) {
                                            if (eventArr[i].eventIdentity === calEvent.eventIdentity) {
                                                eventArr.splice(i,1);
                                                break;
                                            }
                                        }
                                        $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                                        // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                                        $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                            checkedMembers($(this).val());
                                        });

                                        // before pushing into eventArrDelta, check if there's any redundancy
                                        var operationNindex = checkOperationAndIndex1(calEvent.eventIdentity, calEvent.shift);
                                        var operationCheck = operationNindex.split("@")[0];
                                        var indexCheck = operationNindex.split("@")[1];
                                        switch (operationCheck) {
                                            case "none":
                                                eventArrDelta.push(new shiftToGo(calEvent.eventIdentity.split("\n")[0], calEvent.eventIdentity.split("\n")[1], calEvent.shift, "delete", "none")); // saving data to eventArrDelta before sending to database
                                                break;
                                            case "insert":
                                                eventArrDelta.splice(indexCheck, 1);
                                                break;
                                            case "delete":
                                                alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                break;
                                            default:
                                                alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert");
                                                break;
                                        }
                                        if (eventArrDelta.length !== 0) {
                                            swapImage();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: true
                                            });
                                        }
                                        else {
                                            swapImage2();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: false
                                            });
                                        }
                                        // update digits of Exp duty/ Act work
                                        // updateExpActOff(nocUserName.split("_")[0], -1, -1, 0);
                                        // updateColor(nocUserName.split("_")[0]);
                                        updateDigitNColor(loginUserName.split("_")[0], initExp1, initAct1, initOff1);
                                        alertify.success('Event deleted.');
                                    },
                                    function(){ // do something when clicking cancel
                                        alertify.error('You clicked cancel.');
                                    }
                                ).show();
                            }
                        }
                    },
                    /* triggered when dragging stops and the event has moved to a different day
                    ---------------------------------------------------------------------*/
                    eventDrop: function(event, daydelta, revertFunc) {
                        var dropDateTime = event.start.format(); // original format in calendar is as 2016-02-08T00:00:00
                        var dropDateTimePieces = dropDateTime.split("T");
                        var dropDatePieces = dropDateTimePieces[0].split("-");
                        var dropYear = parseInt(dropDatePieces[0]);
                        var dropMonth = parseInt(dropDatePieces[1]);
                        var dropDay = parseInt(dropDatePieces[2]);
                        if (event.textColor === "black") {
                            alertify.showFailure("Off hour events could not be moved.");
                            revertFunc(); // revertFunc reverts the event's start/end date to the values before the drag
                        }
                        else {
                            if (new Date(dropYear + "/" + dropMonth + "/" + dropDay) < new Date(yyyy + "/" + mm + "/" + dd)) {
                                alertify.showFailure("You could not move it to the date before today.");
                                revertFunc();
                            }
                            else {
                                alertify.confirm("CONFIRMATION", // title
                                    "Move " + event.eventIdentity.split("\n")[1] + " to <font style='color:red'>" + dropYear + "/" + dropMonth + "/" + dropDay + "</font> ?", // description
                                    function(){ // do something when clicking ok 
                                        switch (returnShift(event.eventIdentity)) {
                                            case "Night":
                                                if (dateValidation(event.eventIdentity.split("\n")[0] + "\n" + dropYear + "/" + dropMonth + "/" + dropDay)) {
                                                    revertFunc();
                                                    return;
                                                }
                                                else {
                                                    var dropEvent = {
                                                        eventIdentity: event.eventIdentity.split("\n")[0] + "\n" + dropYear + "/" + dropMonth + "/" + dropDay,
                                                        shift: event.shift,
                                                        color: event.color,
                                                        start: dropYear + "/" + dropMonth + "/" + dropDay + " 00:00",
                                                        end: dropYear + "/" + dropMonth + "/" + dropDay + " 09:30",
                                                        description: event.description,
                                                        editable: true
                                                    };
                                                    for (var i = 0; i < eventArr.length; i++) {
                                                        if (eventArr[i].eventIdentity === event.eventIdentity) {
                                                            eventArr.splice(i,1,dropEvent);
                                                            break;
                                                        }
                                                    }
                                                    // before pushing into eventArrDelta, check if there's any redundancy
                                                    var operationNindex = checkOperationAndIndex1(event.eventIdentity, event.shift);
                                                    var operationCheck = operationNindex.split("@")[0];
                                                    var indexCheck = operationNindex.split("@")[1];
                                                    switch (operationCheck) {
                                                        case "none":
                                                            eventArrDelta.push(new shiftToGo(event.eventIdentity.split("\n")[0], event.eventIdentity.split("\n")[1], event.shift, "delete", "none")); // saving data to eventArrDelta before sending to database
                                                            break;
                                                        case "insert":
                                                            eventArrDelta.splice(indexCheck, 1);
                                                            break;
                                                        case "delete":
                                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                            break;
                                                        default:
                                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert");
                                                            break;
                                                    }
                                                    // before pushing into eventArrDelta, check if there's any redundancy
                                                    var operationNindex = checkOperationAndIndex1(dropEvent.eventIdentity, dropEvent.shift);
                                                    var operationCheck = operationNindex.split("@")[0];
                                                    var indexCheck = operationNindex.split("@")[1];
                                                    switch (operationCheck) {
                                                        case "none":
                                                            eventArrDelta.push(new shiftToGo(event.eventIdentity.split("\n")[0], dropYear + "/" + dropMonth + "/" + dropDay, event.shift, "insert", "none")); // saving data to eventArrDelta before sending to database
                                                            break;
                                                        case "insert":
                                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                            break;
                                                        case "delete":
                                                            eventArrDelta.splice(indexCheck, 1);
                                                            break;
                                                        default:
                                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert/delete");
                                                            break;
                                                    }
                                                    break;
                                                }
                                            case "Day":
                                                if (dateValidation(event.eventIdentity.split("\n")[0] + "\n" + dropYear + "/" + dropMonth + "/" + dropDay)) {
                                                    revertFunc();
                                                    return;
                                                }
                                                else {
                                                    var dropEvent = {
                                                        eventIdentity: event.eventIdentity.split("\n")[0] + "\n" + dropYear + "/" + dropMonth + "/" + dropDay,
                                                        shift: event.shift,
                                                        color: event.color,
                                                        start: dropYear + "/" + dropMonth + "/" + dropDay + " 09:00",
                                                        end: dropYear + "/" + dropMonth + "/" + dropDay + " 18:30",
                                                        description: event.description,
                                                        editable: true
                                                    };
                                                    for (var i = 0; i < eventArr.length; i++) {
                                                        if (eventArr[i].eventIdentity === event.eventIdentity) {
                                                            eventArr.splice(i,1,dropEvent);
                                                            break;
                                                        }
                                                    }
                                                    // before pushing into eventArrDelta, check if there's any redundancy
                                                    var operationNindex = checkOperationAndIndex1(event.eventIdentity, event.shift);
                                                    var operationCheck = operationNindex.split("@")[0];
                                                    var indexCheck = operationNindex.split("@")[1];
                                                    switch (operationCheck) {
                                                        case "none":
                                                            eventArrDelta.push(new shiftToGo(event.eventIdentity.split("\n")[0], event.eventIdentity.split("\n")[1], event.shift, "delete", "none")); // saving data to eventArrDelta before sending to database
                                                            break;
                                                        case "insert":
                                                            eventArrDelta.splice(indexCheck, 1);
                                                            break;
                                                        case "delete":
                                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                            break;
                                                        default:
                                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert");
                                                            break;
                                                    }
                                                    // before pushing into eventArrDelta, check if there's any redundancy
                                                    var operationNindex = checkOperationAndIndex1(dropEvent.eventIdentity, dropEvent.shift);
                                                    var operationCheck = operationNindex.split("@")[0];
                                                    var indexCheck = operationNindex.split("@")[1];
                                                    switch (operationCheck) {
                                                        case "none":
                                                            eventArrDelta.push(new shiftToGo(event.eventIdentity.split("\n")[0], dropYear + "/" + dropMonth + "/" + dropDay, event.shift, "insert", "none")); // saving data to eventArrDelta before sending to database
                                                            break;
                                                        case "insert":
                                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                            break;
                                                        case "delete":
                                                            eventArrDelta.splice(indexCheck, 1);
                                                            break;
                                                        default:
                                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert/delete");
                                                            break;
                                                    }
                                                    break;
                                                }
                                            case "Swing":
                                                if (dateValidation(event.eventIdentity.split("\n")[0] + "\n" + dropYear + "/" + dropMonth + "/" + dropDay)) {
                                                    revertFunc();
                                                    return;
                                                }
                                                else {
                                                    var dropEvent = {
                                                        eventIdentity: event.eventIdentity.split("\n")[0] + "\n" + dropYear + "/" + dropMonth + "/" + dropDay,
                                                        shift: event.shift,
                                                        color: event.color,
                                                        start: dropYear + "/" + dropMonth + "/" + dropDay + " 15:00",
                                                        end: dropYear + "/" + dropMonth + "/" + (dropDay+1) + " 00:30",
                                                        description: event.description,
                                                        editable: true
                                                    };
                                                    for (var i = 0; i < eventArr.length; i++) {
                                                        if (eventArr[i].eventIdentity === event.eventIdentity) {
                                                            eventArr.splice(i,1,dropEvent);
                                                            break;
                                                        }
                                                    }
                                                    // before pushing into eventArrDelta, check if there's any redundancy
                                                    var operationNindex = checkOperationAndIndex1(event.eventIdentity, event.shift);
                                                    var operationCheck = operationNindex.split("@")[0];
                                                    var indexCheck = operationNindex.split("@")[1];
                                                    switch (operationCheck) {
                                                        case "none":
                                                            eventArrDelta.push(new shiftToGo(event.eventIdentity.split("\n")[0], event.eventIdentity.split("\n")[1], event.shift, "delete", "none")); // saving data to eventArrDelta before sending to database
                                                            break;
                                                        case "insert":
                                                            eventArrDelta.splice(indexCheck, 1);
                                                            break;
                                                        case "delete":
                                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                            break;
                                                        default:
                                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert");
                                                            break;
                                                    }
                                                    // before pushing into eventArrDelta, check if there's any redundancy
                                                    var operationNindex = checkOperationAndIndex1(dropEvent.eventIdentity, dropEvent.shift);
                                                    var operationCheck = operationNindex.split("@")[0];
                                                    var indexCheck = operationNindex.split("@")[1];
                                                    switch (operationCheck) {
                                                        case "none":
                                                            eventArrDelta.push(new shiftToGo(event.eventIdentity.split("\n")[0], dropYear + "/" + dropMonth + "/" + dropDay, event.shift, "insert", "none")); // saving data to eventArrDelta before sending to database
                                                            break;
                                                        case "insert":
                                                            alertify.showFailure("This alert should not be triggered cause there should not be two same shifts in a day");
                                                            break;
                                                        case "delete":
                                                            eventArrDelta.splice(indexCheck, 1);
                                                            break;
                                                        default:
                                                            alertify.showFailure("This alert should not be triggered cause operationCheck should always return none/insert/delete");
                                                            break;
                                                    }
                                                    break;
                                                }
                                        }
                                        $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                                        // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                                        $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                                            checkedMembers($(this).val());
                                        });
                                        // swap image
                                        if (eventArrDelta.length !== 0) {
                                            swapImage();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: true
                                            });
                                        }
                                        else {
                                            swapImage2();
                                            $( "#nocUser" ).selectmenu({
                                                disabled: false
                                            });
                                        }
                                        alertify.success(event.eventIdentity.split("\n")[1] + " moved to " + dropYear + "/" + dropMonth + "/" + dropDay);
                                    },
                                    function(){ // do something when clicking cancel
                                        alertify.error('You clicked cancel.');
                                        revertFunc();
                                    }
                                ).show();
                            }
                        }
                    },
                    /* uppercase H for 24-hour clock
                    ----------------------------------------------------------------*/
                    timeFormat: 'H(:mm)',
                    /* show description when hover event
                    ----------------------------------------------------------------*/
                    selectable: true,
                    eventRender: function(event, element, view) {
                        element.attr('title', event.eventIdentity + "\n" + event.description);
                    },
                    
                    /* to make day names uppercase
                    -----------------------------------------------------------------*/
                    dayNamesShort: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
                });
                /* triggered when prev button was clicked to update data in featuretable
                ----------------------------------------------------------------*/
                $('body').on('click', 'button.fc-prev-button', function() {
                    kevinOffNum = 0;
                    liyuanOffNum = 0;
                    juliaOffNum = 0;
                    jackyOffNum = 0;
                    megOffNum = 0;
                    forrestOffNum = 0;
                    brianOffNum = 0;

                    eventArr.splice(0,eventArr.length);
                    // eventArrDelta.splice(0,eventArrDelta.length);

                    initialization();
                    updateEventArr();

                    var moment = $('#calendar').fullCalendar('getDate');
                    currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                    currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);
                    $.ajax ({
                        url: 'process1.php',
                        type: 'POST', // Send post data
                        data: 'type=workdayandtakeoffday&year=' + currentYear + '&month=' + currentMonth,
                        dataType: 'json',
                        async: false,
                        success: function(NOC_sum){
                            summarizedData = NOC_sum;
                        }
                    });
                    var tempSumArr = [];
                    for (var x in summarizedData) {
                        tempSumArr.push(summarizedData[x]);
                    }
                    // to update all data in summarized table
                    for (var c = 0; c < employeeArr.length; c++) {
                        employeeArr[c].dutyDays = 0;
                        employeeArr[c].actDays = 0;
                        employeeArr[c].offHours = 0;
                        if (employeeArr[c].employeeName === "kevin_cy_hsu") {
                            $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                            $("#kevinActDays").html(employeeArr[c].actDays);
                            $("#kevinOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - kevinOffNum;
                                    $("#kevinActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#kevinOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "liyuan_chang") {
                            $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                            $("#liyuanActDays").html(employeeArr[c].actDays);
                            $("#liyuanOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - liyuanOffNum;
                                    $("#liyuanActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#liyuanOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "julia_tsai") {
                            $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                            $('#juliaActDays').html(employeeArr[c].actDays);
                            $("#juliaOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - juliaOffNum;
                                    $('#juliaActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#juliaOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "jacky_bp_lee") {
                            $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                            $('#jackyActDays').html(employeeArr[c].actDays);
                            $("#jackyOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - jackyOffNum;
                                    $('#jackyActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#jackyOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "meg_li") {
                            $("#megDutyDays").html(employeeArr[c].dutyDays);
                            $('#megActDays').html(employeeArr[c].actDays);
                            $("#megOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#megDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - megOffNum;
                                    $('#megActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#megOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "forrest_lin") {
                            $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                            $('#forrestActDays').html(employeeArr[c].actDays);
                            $("#forrestOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - forrestOffNum;
                                    $('#forrestActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#forrestOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "brian_kj_huang") {
                            $("#brianDutyDays").html(employeeArr[c].dutyDays);
                            $('#brianActDays').html(employeeArr[c].actDays);
                            $("#brianOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#brianDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - brianOffNum;
                                    $('#brianActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#brianOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                    }
                    if (nocUserName === "jack_hsia") {
                        initExp = 0;
                        initAct = 0;
                        initOff = 0;
                        initExp1 = 0;
                        initAct1 = 0;
                        initOff1 = 0;
                    }
                    else {
                        initExp = parseInt(document.getElementById(nocUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct = parseInt(document.getElementById(nocUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff = parseFloat(document.getElementById(nocUserName.split("_")[0] + "OffHours").innerHTML);
                        initExp1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff1 = parseFloat(document.getElementById(loginUserName.split("_")[0] + "OffHours").innerHTML);    
                        // alert(initExp+"\n"+initAct+"\n"+initOff);
                        updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                        updateDigitNColor(loginUserName.split("_")[0], initExp1, initAct1, initOff1);
                    }
                    $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                    // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });

                    // select single user
                    $('#calendar').fullCalendar('removeEvents');        
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });
                });
                /* triggered when next button was clicked to update data in featuretable
                ----------------------------------------------------------------*/
                $('body').on('click', 'button.fc-next-button', function() {
                    kevinOffNum = 0;
                    liyuanOffNum = 0;
                    juliaOffNum = 0;
                    jackyOffNum = 0;
                    megOffNum = 0;
                    forrestOffNum = 0;
                    brianOffNum = 0;

                    eventArr.splice(0,eventArr.length);
                    // eventArrDelta.splice(0,eventArrDelta.length);

                    initialization();
                    updateEventArr();

                    var moment = $('#calendar').fullCalendar('getDate');
                    currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                    currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);
                    $.ajax ({
                        url: 'process1.php',
                        type: 'POST', // Send post data
                        data: 'type=workdayandtakeoffday&year=' + currentYear + '&month=' + currentMonth,
                        dataType: 'json',
                        async: false,
                        success: function(NOC_sum){
                            summarizedData = NOC_sum;
                        }
                    });
                    var tempSumArr = [];
                    for (var x in summarizedData) {
                        tempSumArr.push(summarizedData[x]);
                    }
                    // to update all data in summarized table
                    for (var c = 0; c < employeeArr.length; c++) {
                        employeeArr[c].dutyDays = 0;
                        employeeArr[c].actDays = 0;
                        employeeArr[c].offHours = 0;
                        if (employeeArr[c].employeeName === "kevin_cy_hsu") {
                            $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                            $("#kevinActDays").html(employeeArr[c].actDays);
                            $("#kevinOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - kevinOffNum;
                                    $("#kevinActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#kevinOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "liyuan_chang") {
                            $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                            $("#liyuanActDays").html(employeeArr[c].actDays);
                            $("#liyuanOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - liyuanOffNum;
                                    $("#liyuanActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#liyuanOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "julia_tsai") {
                            $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                            $('#juliaActDays').html(employeeArr[c].actDays);
                            $("#juliaOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - juliaOffNum;
                                    $('#juliaActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#juliaOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "jacky_bp_lee") {
                            $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                            $('#jackyActDays').html(employeeArr[c].actDays);
                            $("#jackyOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - jackyOffNum;
                                    $('#jackyActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#jackyOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "meg_li") {
                            $("#megDutyDays").html(employeeArr[c].dutyDays);
                            $('#megActDays').html(employeeArr[c].actDays);
                            $("#megOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#megDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - megOffNum;
                                    $('#megActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#megOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "forrest_lin") {
                            $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                            $('#forrestActDays').html(employeeArr[c].actDays);
                            $("#forrestOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - forrestOffNum;
                                    $('#forrestActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#forrestOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "brian_kj_huang") {
                            $("#brianDutyDays").html(employeeArr[c].dutyDays);
                            $('#brianActDays').html(employeeArr[c].actDays);
                            $("#brianOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#brianDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - brianOffNum;
                                    $('#brianActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#brianOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                    }
                    if (nocUserName === "jack_hsia") {
                        initExp = 0;
                        initAct = 0;
                        initOff = 0;
                        initExp1 = 0;
                        initAct1 = 0;
                        initOff1 = 0;
                    }
                    else {
                        initExp = parseInt(document.getElementById(nocUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct = parseInt(document.getElementById(nocUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff = parseFloat(document.getElementById(nocUserName.split("_")[0] + "OffHours").innerHTML);
                        initExp1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff1 = parseFloat(document.getElementById(loginUserName.split("_")[0] + "OffHours").innerHTML);    
                        // alert(initExp+"\n"+initAct+"\n"+initOff);
                        updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                        updateDigitNColor(loginUserName.split("_")[0], initExp1, initAct1, initOff1);
                    }
                    $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                    // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });

                    // select single user
                    $('#calendar').fullCalendar('removeEvents');        
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });
                });
                /* triggered when clicking today
                -----------------------------------------------------------------*/
                $(".fc-today-button").click(function() {
                    kevinOffNum = 0;
                    liyuanOffNum = 0;
                    juliaOffNum = 0;
                    jackyOffNum = 0;
                    megOffNum = 0;
                    forrestOffNum = 0;
                    brianOffNum = 0;

                    eventArr.splice(0,eventArr.length);
                    // eventArrDelta.splice(0,eventArrDelta.length);

                    initialization();
                    updateEventArr();

                    var moment = $('#calendar').fullCalendar('getDate');
                    currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                    currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);
                    $.ajax ({
                        url: 'process1.php',
                        type: 'POST', // Send post data
                        data: 'type=workdayandtakeoffday&year=' + currentYear + '&month=' + currentMonth,
                        dataType: 'json',
                        async: false,
                        success: function(NOC_sum){
                            summarizedData = NOC_sum;
                        }
                    });
                    var tempSumArr = [];
                    for (var x in summarizedData) {
                        tempSumArr.push(summarizedData[x]);
                    }
                    // to update all data in summarized table
                    for (var c = 0; c < employeeArr.length; c++) {
                        employeeArr[c].dutyDays = 0;
                        employeeArr[c].actDays = 0;
                        employeeArr[c].offHours = 0;
                        if (employeeArr[c].employeeName === "kevin_cy_hsu") {
                            $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                            $("#kevinActDays").html(employeeArr[c].actDays);
                            $("#kevinOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - kevinOffNum;
                                    $("#kevinActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#kevinOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "liyuan_chang") {
                            $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                            $("#liyuanActDays").html(employeeArr[c].actDays);
                            $("#liyuanOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - liyuanOffNum;
                                    $("#liyuanActDays").html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#liyuanOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "julia_tsai") {
                            $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                            $('#juliaActDays').html(employeeArr[c].actDays);
                            $("#juliaOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - juliaOffNum;
                                    $('#juliaActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#juliaOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "jacky_bp_lee") {
                            $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                            $('#jackyActDays').html(employeeArr[c].actDays);
                            $("#jackyOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - jackyOffNum;
                                    $('#jackyActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#jackyOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "meg_li") {
                            $("#megDutyDays").html(employeeArr[c].dutyDays);
                            $('#megActDays').html(employeeArr[c].actDays);
                            $("#megOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#megDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - megOffNum;
                                    $('#megActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#megOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "forrest_lin") {
                            $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                            $('#forrestActDays').html(employeeArr[c].actDays);
                            $("#forrestOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - forrestOffNum;
                                    $('#forrestActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#forrestOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                        else if (employeeArr[c].employeeName === "brian_kj_huang") {
                            $("#brianDutyDays").html(employeeArr[c].dutyDays);
                            $('#brianActDays').html(employeeArr[c].actDays);
                            $("#brianOffHours").html(employeeArr[c].offHours);
                            for (var i = 0; i < tempSumArr.length; i++) {
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                    employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                    $("#brianDutyDays").html(employeeArr[c].dutyDays);
                                    employeeArr[c].actDays = employeeArr[c].dutyDays - brianOffNum;
                                    $('#brianActDays').html(employeeArr[c].actDays);
                                }
                                if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                    employeeArr[c].offHours = tempSumArr[i].OffHour;
                                    $("#brianOffHours").html(employeeArr[c].offHours);
                                }
                            }
                        }
                    }
                    if (nocUserName === "jack_hsia") {
                        initExp = 0;
                        initAct = 0;
                        initOff = 0;
                        initExp1 = 0;
                        initAct1 = 0;
                        initOff1 = 0;
                    }
                    else {
                        initExp = parseInt(document.getElementById(nocUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct = parseInt(document.getElementById(nocUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff = parseFloat(document.getElementById(nocUserName.split("_")[0] + "OffHours").innerHTML);
                        initExp1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "DutyDays").innerHTML);
                        initAct1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "ActDays").innerHTML);
                        initOff1 = parseFloat(document.getElementById(loginUserName.split("_")[0] + "OffHours").innerHTML);    
                        // alert(initExp+"\n"+initAct+"\n"+initOff);
                        updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                        updateDigitNColor(loginUserName.split("_")[0], initExp1, initAct1, initOff1);
                    }
                    $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                    // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });

                    // select single user
                    $('#calendar').fullCalendar('removeEvents');        
                    $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                        checkedMembers($(this).val());
                    });
                });
                /* alertify functions
                --------------------------------------------------------------*/
                // define success modal using dialog factory
                if (!alertify.showWarning) {
                    alertify.dialog('showWarning', function factory(){
                        return {
                            build: function() {
                                var html = '<i class="fa fa-cog fa-spin" style="color: red; vertical-align: middle; margin-right: 20px; font-size: 20px;"></i>';
                                html += ' ATTENTION';
                                this.setHeader(html);
                            }
                        };
                    }, false, 'alert');
                }
                // define failure modal using dialog factory
                if (!alertify.showFailure) {
                    alertify.dialog('showFailure', function factory(){
                        return {
                            build: function() {
                                var html = '<i class="fa fa-exclamation-triangle" style="color: red; vertical-align: middle; margin-right: 20px; font-size: 20px;"></i>';
                                html += ' WARNING';
                                this.setHeader(html);
                            }
                        };
                    }, false, 'alert');
                }
                /* select and switch nocUserName
                ------------------------------------------------------*/
                $("#nocUser").selectmenu({
                  change: function(event, data) {
                    // alert(data.item.value);
                    if (eventArrDelta.length !== 0) {
                        alertify.showFailure("Please click 'save' now, then select your deputy again.");
                        return;
                    }
                    else {
                        eventArr.splice(0,eventArr.length);

                        deputyName = data.item.value;
                        // $('#deputyName').html(deputyName + ".");
                        if (deputyName === "me") {
                            nocUserName = loginUserName;
                            nocUserID = loginUserID;
                            nocUserColor = loginUserColor;
                        }
                        else if (deputyName === loginUserName) {
                            // $('#deputyName').html("me.");
                            nocUserName = loginUserName;
                            nocUserID = loginUserID;
                            nocUserColor = loginUserColor;
                        }
                        else {
                            // $('#deputyName').html(deputyName + ".");
                            nocUserName = deputyName;
                            for (var i = 0; i < employeeArr.length; i++) {
                                if (employeeArr[i].employeeName === deputyName) {
                                    nocUserID = employeeArr[i].employeeID;
                                    nocUserColor = employeeArr[i].employeeColor;
                                    break;
                                }
                            }
                        }

                        kevinOffNum = 0;
                        liyuanOffNum = 0;
                        juliaOffNum = 0;
                        jackyOffNum = 0;
                        megOffNum = 0;
                        forrestOffNum = 0;
                        brianOffNum = 0;

                        eventArr.splice(0,eventArr.length);
                        // eventArrDelta.splice(0,eventArrDelta.length);

                        if (loginUserName === "jack_hsia") {
                            alertify.showFailure("Bug: events in current month will be gone and we're working on it. You can fix it temporarily by clicking prev/next month.");
                        }
                        else {
                            initialization();
                            updateEventArr();
                        }

                        var moment = $('#calendar').fullCalendar('getDate');
                        currentYear = parseInt(moment.format().split("T")[0].split("-")[0]);
                        currentMonth = parseInt(moment.format().split("T")[0].split("-")[1]);
                        $.ajax ({
                            url: 'process1.php',
                            type: 'POST', // Send post data
                            data: 'type=workdayandtakeoffday&year=' + currentYear + '&month=' + currentMonth,
                            dataType: 'json',
                            async: false,
                            success: function(NOC_sum){
                                summarizedData = NOC_sum;
                            }
                        });
                        var tempSumArr = [];
                        for (var x in summarizedData) {
                            tempSumArr.push(summarizedData[x]);
                        }
                        // to update all data in summarized table
                        for (var c = 0; c < employeeArr.length; c++) {
                            employeeArr[c].dutyDays = 0;
                            employeeArr[c].actDays = 0;
                            employeeArr[c].offHours = 0;
                            if (employeeArr[c].employeeName === "kevin_cy_hsu") {
                                $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                                $("#kevinActDays").html(employeeArr[c].actDays);
                                $("#kevinOffHours").html(employeeArr[c].offHours);
                                for (var i = 0; i < tempSumArr.length; i++) {
                                    if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('workdays')) {
                                        employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                        $("#kevinDutyDays").html(employeeArr[c].dutyDays);
                                        employeeArr[c].actDays = employeeArr[c].dutyDays - kevinOffNum;
                                        $("#kevinActDays").html(employeeArr[c].actDays);
                                    }
                                    if (tempSumArr[i].username === "kevin_cy_hsu" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                        employeeArr[c].offHours = tempSumArr[i].OffHour;
                                        $("#kevinOffHours").html(employeeArr[c].offHours);
                                    }
                                }
                            }
                            else if (employeeArr[c].employeeName === "liyuan_chang") {
                                $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                                $("#liyuanActDays").html(employeeArr[c].actDays);
                                $("#liyuanOffHours").html(employeeArr[c].offHours);
                                for (var i = 0; i < tempSumArr.length; i++) {
                                    if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                        employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                        $("#liyuanDutyDays").html(employeeArr[c].dutyDays);
                                        employeeArr[c].actDays = employeeArr[c].dutyDays - liyuanOffNum;
                                        $("#liyuanActDays").html(employeeArr[c].actDays);
                                    }
                                    if (tempSumArr[i].username === "liyuan_chang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                        employeeArr[c].offHours = tempSumArr[i].OffHour;
                                        $("#liyuanOffHours").html(employeeArr[c].offHours);
                                    }
                                }
                            }
                            else if (employeeArr[c].employeeName === "julia_tsai") {
                                $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                                $('#juliaActDays').html(employeeArr[c].actDays);
                                $("#juliaOffHours").html(employeeArr[c].offHours);
                                for (var i = 0; i < tempSumArr.length; i++) {
                                    if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('workdays')) {
                                        employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                        $("#juliaDutyDays").html(employeeArr[c].dutyDays);
                                        employeeArr[c].actDays = employeeArr[c].dutyDays - juliaOffNum;
                                        $('#juliaActDays').html(employeeArr[c].actDays);
                                    }
                                    if (tempSumArr[i].username === "julia_tsai" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                        employeeArr[c].offHours = tempSumArr[i].OffHour;
                                        $("#juliaOffHours").html(employeeArr[c].offHours);
                                    }
                                }
                            }
                            else if (employeeArr[c].employeeName === "jacky_bp_lee") {
                                $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                                $('#jackyActDays').html(employeeArr[c].actDays);
                                $("#jackyOffHours").html(employeeArr[c].offHours);
                                for (var i = 0; i < tempSumArr.length; i++) {
                                    if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('workdays')) {
                                        employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                        $("#jackyDutyDays").html(employeeArr[c].dutyDays);
                                        employeeArr[c].actDays = employeeArr[c].dutyDays - jackyOffNum;
                                        $('#jackyActDays').html(employeeArr[c].actDays);
                                    }
                                    if (tempSumArr[i].username === "jacky_bp_lee" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                        employeeArr[c].offHours = tempSumArr[i].OffHour;
                                        $("#jackyOffHours").html(employeeArr[c].offHours);
                                    }
                                }
                            }
                            else if (employeeArr[c].employeeName === "meg_li") {
                                $("#megDutyDays").html(employeeArr[c].dutyDays);
                                $('#megActDays').html(employeeArr[c].actDays);
                                $("#megOffHours").html(employeeArr[c].offHours);
                                for (var i = 0; i < tempSumArr.length; i++) {
                                    if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('workdays')) {
                                        employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                        $("#megDutyDays").html(employeeArr[c].dutyDays);
                                        employeeArr[c].actDays = employeeArr[c].dutyDays - megOffNum;
                                        $('#megActDays').html(employeeArr[c].actDays);
                                    }
                                    if (tempSumArr[i].username === "meg_li" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                        employeeArr[c].offHours = tempSumArr[i].OffHour;
                                        $("#megOffHours").html(employeeArr[c].offHours);
                                    }
                                }
                            }
                            else if (employeeArr[c].employeeName === "forrest_lin") {
                                $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                                $('#forrestActDays').html(employeeArr[c].actDays);
                                $("#forrestOffHours").html(employeeArr[c].offHours);
                                for (var i = 0; i < tempSumArr.length; i++) {
                                    if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('workdays')) {
                                        employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                        $("#forrestDutyDays").html(employeeArr[c].dutyDays);
                                        employeeArr[c].actDays = employeeArr[c].dutyDays - forrestOffNum;
                                        $('#forrestActDays').html(employeeArr[c].actDays);
                                    }
                                    if (tempSumArr[i].username === "forrest_lin" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                        employeeArr[c].offHours = tempSumArr[i].OffHour;
                                        $("#forrestOffHours").html(employeeArr[c].offHours);
                                    }
                                }
                            }
                            else if (employeeArr[c].employeeName === "brian_kj_huang") {
                                $("#brianDutyDays").html(employeeArr[c].dutyDays);
                                $('#brianActDays').html(employeeArr[c].actDays);
                                $("#brianOffHours").html(employeeArr[c].offHours);
                                for (var i = 0; i < tempSumArr.length; i++) {
                                    if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('workdays')) {
                                        employeeArr[c].dutyDays = tempSumArr[i].workdays;
                                        $("#brianDutyDays").html(employeeArr[c].dutyDays);
                                        employeeArr[c].actDays = employeeArr[c].dutyDays - brianOffNum;
                                        $('#brianActDays').html(employeeArr[c].actDays);
                                    }
                                    if (tempSumArr[i].username === "brian_kj_huang" && tempSumArr[i].hasOwnProperty('OffHour')) {
                                        employeeArr[c].offHours = tempSumArr[i].OffHour;
                                        $("#brianOffHours").html(employeeArr[c].offHours);
                                    }
                                }
                            }
                        }

                        if (loginUserName === "jack_hsia") {
                            initExp = 0;
                            initAct = 0;
                            initOff = 0;
                            initExp1 = 0;
                            initAct1 = 0;
                            initOff1 = 0;
                        }

                        else {
                            initExp = parseInt(document.getElementById(nocUserName.split("_")[0] + "DutyDays").innerHTML);
                            initAct = parseInt(document.getElementById(nocUserName.split("_")[0] + "ActDays").innerHTML);
                            initOff = parseFloat(document.getElementById(nocUserName.split("_")[0] + "OffHours").innerHTML);
                            initExp1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "DutyDays").innerHTML);
                            initAct1 = parseInt(document.getElementById(loginUserName.split("_")[0] + "ActDays").innerHTML);
                            initOff1 = parseFloat(document.getElementById(loginUserName.split("_")[0] + "OffHours").innerHTML);    
                            // alert(initExp+"\n"+initAct+"\n"+initOff);
                            updateDigitNColor(nocUserName.split("_")[0], initExp, initAct, initOff);
                            updateDigitNColor(loginUserName.split("_")[0], initExp1, initAct1, initOff1);
                        }

                        $('#calendar').fullCalendar('removeEvents'); // remove all and add them back from eventArr to show up on calendar
                        // $('#calendar').fullCalendar('addEventSource', JSON.parse(JSON.stringify(eventArr)));
                        $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                            checkedMembers($(this).val());
                        });

                        // select single user
                        $('#calendar').fullCalendar('removeEvents');        
                        $("input[name='nocCheckBox']:checkbox:checked").each(function(){
                            checkedMembers($(this).val());
                        });
                    }
                  }
                });
            });
        </script>
        <style>
            /* default settings
            ----------------------------------------------------------------------------------*/
            body {
                margin: 0rem 1.25rem 0rem 1.25rem;
                padding: 0rem;
                font-size: 0.75rem;
                font-family: 'Trebuchet MS', Helvetica, sans-serif;
            }
            /* duty day/ off hour/ summarized table
            ----------------------------------------------------------------------------------*/
            .featuretable {
                font-size: 0.6875rem;
                border: 1px solid #e7e7e7;
                text-align: center;
                margin-bottom: 0.3rem;
                box-shadow: 1px 1px 1px #888888;
            }
            .featuretable td {
                line-height: 0.375rem;
            }
            /* tooltip for comment on summarized table
            ----------------------------------------------------------------------------------*/
            p.tooltip {
                outline: none;
            }
            p.tooltip strong {
                line-height: 30px;
            }
            p.tooltip:hover {
                text-decoration:none;
            } 
            p.tooltip span {
                z-index: 10;
                display: none; 
                padding: 14px 20px;
                margin-top: -30px; 
                margin-left: 28px;
                width: 300px; 
                line-height: 16px;
            }
            p.tooltip:hover span {
                display: inline; 
                position: absolute; 
                color: #111;
                border: 1px solid #DCA; 
                background: #fffAF0;}
            .callout {
                z-index: 20;
                position: absolute;
                top: 30px;
                border: 0;
                left: -12px;
            }
            /*CSS3 extras*/
            p.tooltip span {
                border-radius: 4px;
                box-shadow: 5px 5px 8px #CCC;
            }
            /* button attribute
            -----------------------------------------*/
            .button {
                border: none;
                padding: 2px 4px;
                text-align: center;
                text-decoration: none;
                font-weight: bold;
                display: inline-block;
                margin: 2px 1px;
                -webkit-transition-duration: 0.4s;
                transition-duration: 0.4s;
                cursor: pointer;
            }
            .button:focus {
                outline: none;
            }
            .button4 {
                background-color: #f2f2f2;
                color: #36DBCA;
                border: 2px solid #36DBCA;
            }
            .button4:hover {
                color: #f2f2f2;
                background-color: #36DBCA;
                box-shadow: inset 0 0 0 0px #27496d,0 5px 15px #193047;
            }
            .button4:active {
                box-shadow: inset 0 0 0 0px #27496d,inset 0 5px 30px #193047;
            }
            /* line attribute
            -----------------------------------*/
            /*.hr {
                display: block;
                height: 0.0625rem;
                border: 0rem;
                border-top: 1px solid #ccc;
                margin: 0.4375rem 0rem;
                padding: 0;
            }
            .featuretable tr {
                line-height: 0.75rem;
            }*/
            /* select menu
            -------------------------------------*/
            .selectMenu {
                position: absolute;
                display: inline;
                color: #6E6E6E;
                font-family: 'Trebuchet MS', Helvetica, sans-serif;
                font-size: 0.8rem;
                left: 53rem;
                top: 0.7rem;
            }
            .selectMenu select {
                width: 7.5rem;
            }
            label {
                position: absolute;
                display: inline;
                color: #6E6E6E;
                font-family: 'Trebuchet MS', Helvetica, sans-serif;
                font-size: 0.875rem;
                left: 45rem;
                top: 1rem;
            }

            /* From Landing page */
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
                height: 130px;
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
            .profilebutton {
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
            .profilebutton:hover {
                background: #645791;
            }
            .profilebutton:active {
                background: #433573;
            }
            .profilebutton+button {
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
            .home2 {
                color: #5169ce;
                font-size: 16px;
                display: inline;
                position: absolute;
                top: 1rem;
                left: 65rem;
                text-decoration: none;
            }
                .home2:hover {
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
    </style>
    </head>
    <body>
    <!-- header -->
        <!-- 登入者圖像popup -->
        <div class="avgrund-cover"></div>
        <aside class="avgrund-popup">
            <div class="popuptitle">PROFILE</div>
            <button id="" onclick="location.href='member_update.php'" class="profilebutton">Change Password</button><br>
            <button id="logout" class="profilebutton">Logout</button><br>
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
        <a href="nocShiftSystem.php" class="shift1">SHIFT</a>
            <label for="deputy">Now representing:</label>
            <div class="selectMenu">
                <select name="nocUser" id="nocUser">
                    <option selected="selected">me</option>
                    <option>kevin_cy_hsu</option>
                    <!-- <option>liyuan_chang</option> -->
                    <option>julia_tsai</option>
                    <option>jacky_bp_lee</option>
                    <option>meg_li</option>
                    <option>forrest_lin</option>
                    <option>brian_kj_huang</option>
                </select>
            </div>
        <!-- shift button -->
        <a href="landingpage.php" class="home2">HOME</a>
        <!-- 登入者圖像 -->
        <div class="circle" onclick="avgrund.activate( 'stack' );">
            <!-- onclick="avgrund.activate();" 另一種popup-->
            <div class="inner"></div>
        </div>
    <!-- banner line-->
        <div class="bannerline"></div>
    <!-- left body -->
        <div style="position: relative; width: 22rem">
            <!-- duty day table -->
            <div class="featuretable">
                <table width="100%">
                    <thead>
                        <tr>
                            <th colspan="3" style="background-color: #f2f2f2">
                                <img src="library/1_on_duty.png" style="float: left; margin-top: 0.25rem" height="24px" width="24px" />
                                <p style="float: left; margin-left: 0.5rem">Schedule Your Shifts</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td><strong>Start</strong></td>
                            <td><strong>End</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td><input id="datetimepicker1" /></td>
                            <td><input id="datetimepicker2" /></td>
                        </tr>
                        <tr>
                            <td><strong>Shift: </strong></td>
                            <td style="text-align: left" colspan="2">
                                <input type="radio" name="shiftType" value="Night"> Night </input>
                                <input type="radio" name="shiftType" value="Day"> Day </input>
                                <input type="radio" name="shiftType" value="Swing"> Swing </input>
                                <button id="addWorkDay" style="float: right" class="button button4">ADD</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- off hour table -->
            <div class="featuretable">
                <table width="100%">
                    <thead>
                        <tr>
                            <th colspan="3" style="background-color: #f2f2f2">
                                <img src="library/2_off_hour.png" style="float: left; margin-top: 0.25rem" height="24px" width="24px" />
                                <p style="float: left; margin-left: 0.5rem">Schedule Your Offs</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td><strong>Start</strong></td>
                            <td><strong>End</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Date/Time:</strong></td>
                            <td><input id="datetimepicker3" /></td>
                            <td><input id="datetimepicker4" /></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><button id="addOffHour" style="float: right" class="button button4">ADD</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- summarized table -->
            <div class="featuretable">
                <table width="100%" style="text-align: center">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th style="text-align: left"><p><input type="checkbox" id="all_cb"> Employee</p></th>
                            <th><p>Planned</p></th>
                            <th><p>Actual</p></th>
                            <th><p>Off Hours</p></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="background-color: #ECEC84; text-align: left;">
                                <p class="tooltip">
                                    <input name="nocCheckBox" type="checkbox" value="kevin_cy_hsu"> Kevin
                                    <span>
                                        <img class="callout" src="library/callout.gif" />
                                        <strong>some information</strong>
                                    </span>
                                </p>
                            </td>
                            <td id="kevinDutyDays">0</td>
                            <td id="kevinActDays">0</td>
                            <td id="kevinOffHours">0</td>
                        </tr>
                        <!-- <tr>
                            <td style="background-color: #A8A8A8; text-align: left">
                                <p class="tooltip">    
                                    <input name="nocCheckBox" type="checkbox" value="liyuan_chang"> Liyuan
                                    <span>
                                        <img class="callout" src="library/callout.gif" />
                                        <strong>some information</strong>
                                    </span>
                                </p>
                            </td>
                            <td id="liyuanDutyDays">0</td>
                            <td id="liyuanActDays">0</td>
                            <td id="liyuanOffHours">0</td>
                        </tr> -->
                        <tr>
                            <td style="background-color: #FFC59E; text-align: left">
                                <p class="tooltip">
                                    <input name="nocCheckBox" type="checkbox" value="julia_tsai"> Julia
                                    <span>
                                        <img class="callout" src="library/callout.gif" />
                                        <strong>some information</strong>
                                    </span>
                                </p>
                            </td>
                            <td id="juliaDutyDays">0</td>
                            <td id="juliaActDays">0</td>
                            <td id="juliaOffHours">0</td>
                        </tr>
                        <tr>
                            <td style="background-color: #DDACFF; text-align: left">
                                <p class="tooltip">
                                    <input name="nocCheckBox" type="checkbox" value="jacky_bp_lee"> jacky
                                    <span>
                                        <img class="callout" src="library/callout.gif" />
                                        <strong>some information</strong>
                                    </span>
                                </p>
                            </td>
                            <td id="jackyDutyDays">0</td>
                            <td id="jackyActDays">0</td>
                            <td id="jackyOffHours">0</td>
                        </tr>
                        <tr>
                            <td style="background-color: #B3F4F4; text-align: left">
                                <p class="tooltip">
                                    <input name="nocCheckBox" type="checkbox" value="meg_li"> Meg
                                    <span>
                                        <img class="callout" src="library/callout.gif" />
                                        <strong>some information</strong>
                                    </span>
                                </p>
                            </td>
                            <td id="megDutyDays">0</td>
                            <td id="megActDays">0</td>
                            <td id="megOffHours">0</td>
                        </tr>
                        <tr>
                            <td style="background-color: #7EF0B5; text-align: left">
                                <p class="tooltip">
                                    <input name="nocCheckBox" type="checkbox" value="forrest_lin"> Forrest
                                    <span>
                                        <img class="callout" src="library/callout.gif" />
                                        <strong>some information</strong>
                                    </span>
                                </p>
                            </td>
                            <td id="forrestDutyDays">0</td>
                            <td id="forrestActDays">0</td>
                            <td id="forrestOffHours">0</td>
                        </tr>
                        <tr>
                            <td style="background-color: #5FB0CB; text-align: left">
                                <p class="tooltip">
                                    <input name="nocCheckBox" type="checkbox" value="brian_kj_huang"> Brian
                                    <span>
                                        <img class="callout" src="library/callout.gif" />
                                        <strong>some information</strong>
                                    </span>
                                </p>
                            </td>
                            <td id="brianDutyDays">0</td>
                            <td id="brianActDays">0</td>
                            <td id="brianOffHours">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- show up last modifier and time -->
            <div style="position: relative; color: #6E6E6E; font-family: 'Trebuchet MS', Helvetica, sans-serif; margin-top: 0.625rem; font-size: 0.75rem;">
                Last modified by <strong id="modFetchName" style="display: inline">none</strong></br>
                Last modified on <strong id="modFetchTime" style="display: inline">none</strong>
            </div>
        </div>
    <!-- right body -->
        <div id='calendar' style="position: absolute; top: 4rem; left: 24.5rem; width: 68%;"></div>
        <!-- submit button -->
        <img src="library/save.png" id="submitToDB" height="24px" width="24px" style="position: absolute; top: 3.8rem; left: 65.625rem; z-index: 1"/>
        <p style="position: absolute; top: 4.5rem; left: 65.625rem">save</p>
    <!-- footer -->
        <div style="position: relative; color: #6E6E6E; margin-top: 2rem; font-size: 0.75rem; width: 15rem; left: 8rem;">
            <p>&copy; 2016 HTC Corporation All rights reserved</p>
        </div>
    </body>
</html>

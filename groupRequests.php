<?php
require_once("includes/config.php");
//session_start();
if(!isset($_SESSION["usrnm"]))
{
    header('Location: '.'index.php');
}
if(isset($_POST["Accept"]))
{
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    else{
        $usr=$_POST["Accept"];
        $sqlget="SELECT groupId FROM group_requests WHERE studentId = '$usr'";
        $results = $conn->query($sqlget);
        if ($results->num_rows > 0) {
                // output data of each row
                $rows = $results->fetch_assoc();
                $groupsID=$rows["groupId"];
        }
        $sqlUpd="UPDATE student SET groupId = '$groupsID' WHERE studentId = '$usr'";
        $sqlDel="Delete FROM group_requests WHERE studentId = '$usr'";
        $conn->query($sqlUpd);
        $conn->query($sqlDel);
        echo $groupsID;
        echo $usr;
        $conn->close();
        header('Location: '.'home.php');
    }
}
else if(isset($_POST["Reject"]))
{
    if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
    }
    else{
            $usrR=$_POST["Reject"];
            $sqlDel="Delete FROM group_requests WHERE studentId = '$usrR'";
            $conn->query($sqlDel);
            $conn->close();
            header('Location: '.'home.php');
    }

    echo "Delete user request id= ".$_POST["Reject"];
}


?>			
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
<i class="fa fa-user"></i>
<?php
if($_SESSION["type"]=="Student")
{
        $groupid=0;
        $uID=$_SESSION["usrId"];
        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }
        else{
                $sql1="SELECT groupId FROM student WHERE studentId = '$uID'";
                $result1 = $conn->query($sql1);
                if ($result1->num_rows > 0) {
                        // output data of each row
                        $row1 = $result1->fetch_assoc();
                        $groupid=$row1["groupId"];

                }

                $sql = "SELECT student.studentName, student.studentId, student.studentCMS, group_requests.groupId, group_requests.groupId FROM student INNER JOIN group_requests ON student.studentId=group_requests.studentId WHERE group_requests.groupId = '$groupid' ORDER BY student.studentName"; 
                //"SELECT studentName, studentCMS, groupId FROM student WHERE isLeader = '1'";

                //$sql2 = "SELECT facultyId, designation, facultyName, facultyPhoneNo, facultyEmail, facultyPassword, currentLoad, isAdmin, isCoordinator FROM faculty";


                $result = $conn->query($sql);
            if($result->num_rows >= 0) {

                if($result->num_rows>0)
                {
                        echo "<span class=\"label label-danger\">".$result->num_rows."</span>
                        </a>";
                }	
                echo "<ul class=\"dropdown-menu\"><li class=\"header\"> You have ".$result->num_rows." Requests </li>
                        <li>";

                echo "<!-- inner menu: contains the actual data -->
                        <ul class=\"menu\">";
                }
                if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) {

                          echo "<li><!-- Task item -->


                                        ".$row["studentName"]."
                                        (".$row["studentCMS"].")
                                        <div class=\"btn-group btn-group-justified\" role=\"group\">
                                                <form class=\"btn-group btn-group-justified\" id=\"Accept\" action=\"requestsMenu.php\" method=\"post\">
                                                        <button class=\"btn btn-default btn-xs\" href=\"javascript:;\" ><div class=\"glyphicon glyphicon-ok\" >Accept</div></button>
                                                        <input type=\"hidden\" name=\"Accept\" value=\"".$row["studentId"]."\"/>
                                                </form>
                                                <form class=\"btn-group btn-group-justified\" id=\"Reject\" action=\"requestsMenu.php\" method=\"post\">
                                                        <button class=\"btn btn-default btn-xs\" href=\"javascript:;\" ><div class=\"glyphicon glyphicon-remove\" >Reject</div></button>
                                                        <input type=\"hidden\" name=\"Reject\" value=\"".$row["studentId"]."\"/>
                                                </form>
                                        </div>
                          </li>";

                        }

                }
                $conn->close();
        }
}
else{
        echo "

                        <span class=\"label label-danger\"></span>
                        </a>
                        <ul class=\"dropdown-menu\"><li class=\"header\"> You have these Requests </li>
                        <li>
                        <!-- inner menu: contains the actual data -->
                        <ul class=\"menu\">";
                        "<li><!-- Task item -->
                                <a href=\"#\">
                                  <h3>
                                        Teachers notifications
                                        <small class=\"pull-right\"> check </small>
                                  </h3>
                                </a>
                          </li>";
}
?>
    </ul>
  </li>
  <li class="footer">
    <a href="#">View all tasks</a>
  </li>
</ul>

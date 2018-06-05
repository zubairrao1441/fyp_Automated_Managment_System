<?php
require_once('includes/config.php');
require_once('includes/functions.php');


if (isset($_SESSION["usrId"])) {
    $studentId = $_SESSION['usrId'];

    //Check if user is leader of a group
    $sql = "SELECT * FROM student WHERE studentId = '$studentId' LIMIT 1 ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $isLeader = $row['isLeader'];
            $groupId = $row['groupId'];
        }

    }

    if ($isLeader == 1) {
        //Check if his group limit
        $sql = "SELECT * from student_group WHERE leaderId = '$studentId' LIMIT 1 ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $inGroup = $row['groupId'];
            $groupLimit = $row['groupLimit'];
        }
        if ($inGroup <= $groupLimit){
            //Check if he has group requests
            $sql = "SELECT * FROM student JOIN student_group ON student.studentId = student_group.leaderId JOIN student_group_request ON student_group_request.groupId = student_group.groupId WHERE leaderId = '$studentId' AND groupLimit > inGroup ";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $numOfRequests = $result->num_rows;
            }
        }




    }
}
//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['btnAcceptReq'])){
        //Getting data from POST and sanitizing
        $acceptId = filter_input(INPUT_POST,'requestId',FILTER_SANITIZE_NUMBER_INT);


        //Get group id
        $groupId = $conn->query("SELECT groupId FROM student_group_request WHERE requestId = '$acceptId'  LIMIT 1 ")->fetch_object()->groupId;

        //Get student id of person who sent the request
        $studentId = $conn->query("SELECT studentId FROM student_group_request WHERE requestId = '$acceptId'  LIMIT 1 ")->fetch_object()->studentId;

        // Set autocommit to off
        mysqli_autocommit($conn, FALSE);

        $sql = "UPDATE student SET groupId = '$groupId' WHERE studentId = '$studentId' LIMIT 1";

        if ($conn->query($sql) == TRUE) {

            //Increment group members
            $inc_group = $conn ->query("UPDATE student_group SET inGroup = inGroup + 1 WHERE groupId = '$groupId'");


            if ($inc_group  ){
                //Delete from request
                $delete_row = $conn->query("DELETE FROM student_group_request WHERE requestId=" . $acceptId);
                if ($delete_row){
                    // Commit transaction
                    mysqli_commit($conn);
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                }
                else{
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
                }
            }

        }



    }
    if (isset($_POST['btnDeleteReq'])){

        //Getting data from POST and sanitizing
        $deleteId = filter_input(INPUT_POST,'requestId',FILTER_SANITIZE_NUMBER_INT);

        //try deleting record using the record ID we received from POST
        $delete_row = $conn->query("DELETE FROM student_group_request WHERE requestId=" . $deleteId);

        if (!$delete_row) {
            //If mysql delete query was unsuccessful, output error
            //header('HTTP/1.1 500 Could not delete request!');
            exit();
        }else{
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
        }
    }

    if (isset($_POST["recordToDelete"]) && strlen($_POST["recordToDelete"]) > 0 && is_numeric($_POST["recordToDelete"])) {
        global $conn;
        //do we have a delete request? $_POST["recordToDelete"]

        //sanitize post value, PHP filter FILTER_SANITIZE_NUMBER_INT removes all characters except digits, plus and minus sign.
        $idToDelete = filter_var($_POST["recordToDelete"], FILTER_SANITIZE_NUMBER_INT);

        //try deleting record using the record ID we received from POST
        $delete_row = $conn->query("DELETE FROM group_requests WHERE requestId=" . $idToDelete);

        if (!$delete_row) {
            //If mysql delete query was unsuccessful, output error
            //header('HTTP/1.1 500 Could not delete request!');
            exit();
        }

        $conn->close(); //close db connection
    } else if (isset($_POST["recordToAccept"]) && strlen($_POST["recordToAccept"]) > 0 && is_numeric($_POST["recordToAccept"])) {
        global $conn;
        $requestId = filter_var($_POST["recordToAccept"], FILTER_SANITIZE_NUMBER_INT);

        //Get group id
        $groupId = $conn->query("SELECT groupId FROM group_requests WHERE requestId = '$requestId'  ")->fetch_object()->groupId;
        //if(!$groupId){header('HTTP/1.1 500 Error occurred, GroupId!');}
        //Get student id
        $studentId = $conn->query("SELECT studentId FROM group_requests WHERE requestId = '$requestId'   ")->fetch_object()->studentId;

        $sql = "UPDATE student SET groupId='$groupId' WHERE studentId='$studentId' ";

        if ($conn->query($sql) == TRUE) {
            //Delete from request
            $delete_row = $conn->query("DELETE FROM group_requests WHERE requestId=" . $requestId);
        }
        else{
            // header('HTTP/1.1 500 Error occurred, Could not accept request!');
            exit();
        }
        $conn->close(); //close db connection

    } else {
        //Output error
        //header('HTTP/1.1 500 Error occurred, Could not process request!');
        exit();
    }



}

?>


<li class="dropdown notifications-menu " id="requests-student">


    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-user"></i>
        <span class="label label-primary"><?php  if (isset($numOfRequests)){echo $numOfRequests;}else{echo "0";};  ?></span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have <?php if (isset($numOfRequests)){echo $numOfRequests;}else{echo "0";}; ?> request(s)</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
            <?php
            if (isset($numOfRequests)){


                $sql = "SELECT * from student_group JOIN student_group_request ON student_group.groupId = student_group_request.groupId WHERE student_group_request.groupId = '$groupId' ";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $requestFrom = getStudentData($row['studentId']); ?>

                        <form action="" method="post" data-toggle="validator">
                            <input type="hidden" name="requestId" value="<?php echo $row['requestId']?>">
                        <li>
                            <i class="fa fa-user text-aqua"></i><?php echo $requestFrom['name']." [".$requestFrom['cms']."] "; ?> has sent you group request
                            <div id="requestActions" class="text-right">
                                <button id="btnAcceptReq" name="btnAcceptReq" class="accept_button btn btn-primary btn-xs">Accept</button>
                                <button id="btnDelReq" name="btnDeleteReq" class="del_button btn btn-danger btn-xs ">Delete</button>
                            </div>
                        </li>
                        </form>
                   <?php }
                }

            }


            ?>

            </ul>
        </li>
        <?php if (isset($numOfRequests)){ ?>
            <li class="footer"><a href="#">View all</a></li>
         <?php  }
         ?>

    </ul>
</li>





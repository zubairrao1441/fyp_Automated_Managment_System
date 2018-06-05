<?php
require_once('includes/config.php');
require_once('includes/functions.php');

//Check if user is leader of a group
if (isset($_SESSION["facultyId"]) && $_SESSION['isAdmin'] != 1) {
    $supervisorId = $_SESSION['facultyId'];

    //Check if Supervisor can accept request
    //
    $sql = "SELECT * FROM faculty JOIN work_load ON faculty.facultyId = work_load.facultyId WHERE faculty.facultyId = '$supervisorId' LIMIT 1 ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $totalLoad = $row['totalLoad'];
            $currentLoad = $row['currentLoad'];
            $facultyName = $row['facultyName'];
        }
    }
    if ($currentLoad < $totalLoad){
        //Check if he has student requests

        $sql = "SELECT * from faculty_student_request WHERE faculty_student_request.facultyId = '$supervisorId' ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $numOfRequests = $result->num_rows;
        }
    }


}

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    //Function for delete Request
    if (isset($_GET["delete"]) and is_numeric($_GET["delete"]) ){

        $id = filter_input(INPUT_GET, 'delete');
         
        //Check if there is a request with this id
        $request = $conn->query("SELECT requestId FROM faculty_student_request WHERE requestId = '$id' ")->fetch_object()->requestId; 
        if ($request){
            // sql to delete a record
            $sql = "DELETE FROM faculty_student_request WHERE requestId='$id' ";
            if ($conn->query($sql) === TRUE) {
                
               header('Location:' . $_SERVER['PHP_SELF'] . '?status=t'); 
            } 
        }
        
    }
    
    //Function for accept Request
    if (isset($_GET["accept"]) AND is_numeric($_GET["accept"]) AND strlen($_GET['accept']) > 0 ){

        $id = filter_input(INPUT_GET, 'accept');
         
        //Check if there is a request with this id
        $request = $conn->query("SELECT requestId FROM faculty_student_request WHERE requestId = '$id' ")->fetch_object()->requestId; 
        if ($request){
            
            $groupId = $conn->query("SELECT groupId FROM faculty_student_request WHERE requestId = '$id' ")->fetch_object()->groupId; ;
            $facultyId= $_SESSION['facultyId'] ;
            // sql to accept a request
            $sql = "INSERT INTO faculty_student_group (groupId, facultyId)VALUES ('$groupId', '$facultyId')";

            if ($conn->query($sql) === TRUE) {
               //If request accepted delete request from record
                 $sql = "DELETE FROM faculty_student_request WHERE requestId='$id' ";
                if ($conn->query($sql) === TRUE) {
                    //Record also deleted
                    
                    //Increment value of currentload
                    $sql = "UPDATE work_load SET currentLoad = currentLoad +1 WHERE facultyId = '$facultyId'";
                    if ($conn->query($sql) === TRUE) {

                        /****
                         * Add this to timeline
                         *  Zeeshan Sabir is now supervising group (FYP Management System)
                         *  Faculty Name from facultyId (SESSION)
                         *  Group Name from groupId
                         *
                         */
                        //Add this info to the students and faculty timeline




                        //Get Batch id,projectName and SDP part from groupId
                        $sql = "SELECT * FROM student_group WHERE groupId = '$groupId' LIMIT 1";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while($row = $result->fetch_assoc()) {
                                $batchId = $row['batchId'];
                                $projectName = $row['projectName'];
                                $sdpPart = $row['sdpPart'];
                            }
                        }

                        $title = '<i class="fa fa-info-circle" aria-hidden="true"></i> Info';
                        $details = $facultyName." is now supervising group ".$projectName;

                        $sql = "INSERT INTO timeline_student (title, details, type, batchId, sdpPart) VALUES ('$title', '$details', 'info', '$batchId', '$spdPart')";

                        if ($conn->query($sql) === TRUE) {
                            $sql = "INSERT INTO timeline_faculty (title, details, type, batchId, sdpPart) VALUES ('$title', '$details', 'info', '$batchId', '$spdPart')";

                            if ($conn->query($sql) === TRUE) {
                                header('Location:' . $_SERVER['PHP_SELF'] );
                            }


                        }
                    }

                    
                }
            } 
            
            
        }
            
        }

}
?>

<li class="dropdown notifications-menu " id="requests-faculty">

    <script>
        $(document).ready(function () {



        });
    </script>
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

                    $supervisorId = $_SESSION['facultyId'];
                    $sql = "SELECT * from faculty_student_request JOIN student_group WHERE faculty_student_request.facultyId = '$supervisorId' AND student_group.groupId = faculty_student_request.groupId ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $requestFrom = getStudentData($row['leaderId']); ?>
                            <li>
                                <i class="fa fa-user text-aqua"></i><?php echo $requestFrom['name'] ." [".$row['projectName']." ]"; ?> sent you request
                                    <div id="requestActions" class="text-right">
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?accept=' . $row['requestId']; ?>"  class="btn btn-primary btn-xs">Accept</a>
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?delete=' . $row['requestId']; ?>"  class="btn btn-danger btn-xs">Delete</a>
                                    </div>
                            </li>
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





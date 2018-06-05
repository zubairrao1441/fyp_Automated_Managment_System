<?php
require_once('includes/config.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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


<li class="dropdown notifications-menu " id="requests-menu">
    <?php
    require_once('includes/functions.php');
    //Check if user i leader of a group
    if (isset($_SESSION["isLead"])){
    if ($_SESSION["isLead"] == 1){
    //Check if he has group requests
    $leaderId = $_SESSION['usrId'];
    $sql = "SELECT * from student_group JOIN group_requests WHERE leaderId = '$leaderId'  ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
    $numOfRequests = $result->num_rows;
    ?>
    <script>
        $(document).ready(function () {

            //Delete Button Action
            $("body").on("click", "#requestActions .del_button", function (e) {
                e.preventDefault();

                var clickedID = this.id.split('-'); //Split ID string (Split works as PHP explode)
                var DbNumberID = clickedID[1]; //and get number from array

                swal({
                    title: "Are you sure?",

                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Delete Request",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {

                        var myData = 'recordToDelete=' + DbNumberID; //build a post data structure
                        $.ajax({
                            type: "POST", // HTTP method POST or GET
                            url: "requests.php", //Where to make Ajax calls
                            dataType: "text", // Data type, HTML, json etc.
                            data: myData, //Form variables
                            success: function (response) {
                                swal({
                                    title: "Success!",
                                    text: "Request deleted",
                                    type: "success",
                                    confirmButtonColor: "#8CD4F5",
                                    confirmButtonText: "Okay",
                                    closeOnConfirm: false
                                }, function () {
                                    location.reload();
                                });

                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                //On error, we alert user
                                alert(thrownError);
                            }
                        });
                    } else {
                        swal("Cancelled", "Operation has been cancelled:)", "error");
                    }
                });


            });

            //Accept Button Action
            $("body").on("click", "#requestActions .accept_button", function (e) {
                e.preventDefault();

                var clickedID = this.id.split('-'); //Split ID string (Split works as PHP explode)
                var DbNumberID = clickedID[1]; //and get number from array
                var myData = 'recordToAccept=' + DbNumberID; //build a post data structure

                console.log("Accept Button is pressed");
                console.log("Clicked id: " + clickedID[1]);
                swal({
                    title: "Are you sure?",

                    type: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "Yes, Accept Request",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {

                        var myData = 'recordToAccept=' + DbNumberID; //build a post data structure
                        $.ajax({
                            type: "POST", // HTTP method POST or GET
                            url: "requests.php", //Where to make Ajax calls
                            dataType: "text", // Data type, HTML, json etc.
                            data: myData, //Form variables
                            success: function (response) {
                                swal({
                                    title: "Success!",
                                    text: "Request accepted",
                                    type: "success",
                                    confirmButtonColor: "#8CD4F5",
                                    confirmButtonText: "Okay",
                                    closeOnConfirm: false
                                }, function () {
                                    location.reload();
                                });

                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                //On error, we alert user
                                alert(thrownError);
                            }
                        });
                    } else {
                        swal("Cancelled", "Operation has been cancelled:)", "error");
                    }
                });


            });


        });
    </script>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-user"></i>
        <span class="label label-primary"><?php if (isset($numOfRequests)){echo $numOfRequests;}  ?></span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have <?php if (isset($numOfRequests)){echo $numOfRequests;} ?> request(s)</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <?php

                while ($row = $result->fetch_assoc()) {
                    $requestFrom = getStudentData($row['studentId']);
                    ?>

                    <li>
                        <i class="fa fa-user text-aqua"></i><?php echo $requestFrom['name']; ?> sent you group request
                        <div id="requestActions" class="text-right">
                            <button id="accept-<?php echo $row['requestId']; ?>"
                                    class="accept_button btn btn-primary btn-xs">Accept
                            </button>
                            <button id="del-<?php echo $row['requestId']; ?>" class="del_button btn btn-danger btn-xs ">
                                Delete
                            </button>
                        </div>
                    </li>

                <?php } ?>
            </ul>
        </li>

        <li class="footer"><a href="#">View all</a></li>
    </ul>
</li>

<?php
}
}
}
?>



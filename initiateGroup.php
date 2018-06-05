<?php
$title="FYPMS";
$subtitle="Initiate Group";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if Student is logged in is logged in else log out
if(!isset($_SESSION["usrCMS"]))
{
    header('Location: '.'index.php');
}

//Getting Values from SESSION
$batchId = $_SESSION['BatchID'];
$studentId = $_SESSION['usrId'];


    $check = true;
    /* Check if:
     * - User already initiated a group
     * - User is already in a group
     * - User sent request to group
     */

    //Check for request sent already
    $sql = "SELECT * FROM student_group_request WHERE studentId = '$studentId' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        //User sent request to group already
        $check = false;
    } else {
        //Check if group leader or in a group
        $sql = "SELECT * FROM student WHERE studentId = '$studentId' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $isLeader = $row['isLeader'];
                $groupId = $row['groupId'];
            }
        }
        if ($isLeader == 1 OR !is_null($groupId)){
            $check = false;
        }
        else{
            //$check = true;
        }

    }






//    $sql = "SELECT * FROM student WHERE batchId = '$batchId' AND studentId = '$studentId' LIMIT 1";
//    $result = $conn->query($sql);
//
//    if ($result->num_rows > 0) {
//        while($row = $result->fetch_assoc()) {
//            if ($row['isLeader'] == 1){
//
//                //User is already initiated a group
//                $check = false;
//            }
//            else if (!is_null($row['groupId'])){
//                //User is already in a group
//                $check = false;
//            }
//            else{
//                $check = true;
//
//                $sql = "SELECT studentId FROM student_group_request WHERE studentId = '$studentId' LIMIT 1";
//                $result = $conn->query($sql);
//
//                if ($result->num_rows > 0) {
//                    //User already sent request to group
//                    $check = false;
//                }else{
//                    $check = true;
//                }
//            }
//        }
//    }





//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['initiateGroupBtn'])){
        
        //Validations
        if (isset($_POST['projectName']) && $_POST['projectName'] != ""){
            
            //Getting Values from POST
            $projectName = filter_input(INPUT_POST,'projectName',FILTER_SANITIZE_SPECIAL_CHARS);

            // Set autocommit to off
            mysqli_autocommit($conn, FALSE);

            //Inserting Values in student_group table
            $sql = "INSERT INTO student_group (projectName, batchId, leaderId) VALUES ('$projectName', '$batchId', '$studentId')";

            if ($conn->query($sql) === TRUE) {

                //Get last insert_id = groupId
                $groupId = $conn->insert_id;

                //Set groupId and isLeader in student table
                $sql = "UPDATE student SET groupId='$groupId' , isLeader = '1' WHERE studentId = '$studentId' ";

                if ($conn->query($sql) === TRUE) {

                    // Commit transaction
                    mysqli_commit($conn);

                    //Close connection
                    $conn->close();

                    //Redirect with success message
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
                } else {
                    //Redirect with error message
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
                }

            }

        }//End of validations

    }


}



?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper" >
        <?php require_once("includes/content-header.php"); ?>

        <section class="content" style="min-height: 700px">
            <div class="row">


                <div class="col-md-2"></div>

                <div class="col-md-8">

                    <?php
                    if (isset($_GET['status'])){
                        if ($_GET['status'] == 't'){ ?>
                            <div style="text-align:center;" class="alert alert-success" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                <p>Changes saved successfully!</p>
                                <a href="./groupDetails.php"><i class="fa fa-chevron-right" aria-hidden="true"></i> Show Group Details</a>
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                        else  if ($_GET['status'] == 'f'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! Something Went Wrong
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                        else if ($_GET['status'] == 'req'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! Please fill all required fields
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                        else if ($_GET['status'] == 'e'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error!
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                    }

                    ?>

                    <?php if(isset($check)){
                        if ($check == true){ ?>
                            <!-- general form elements -->
                            <div class="box no-border">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Initiate Group</h3>
                                </div>
                                <!-- /.box-header -->
                                <!-- form start -->
                                <form id="initiateGroup" name="initiateGroup" method="post" data-toggle="validator">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label >Set Group Name</label>
                                            <input type="text" class="form-control" id="projectName" name="projectName" placeholder="Set Project Name" required>
                                            <p class="text-muted">You can change project name later</p>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->

                                    <div class="box-footer">
                                        <button type="submit" name="initiateGroupBtn" form="initiateGroup" class="btn btn-primary pull-right">Initiate Group</button>
                                        <a href="<?php echo siteroot ;?>" class="btn btn-default">Back</a>
                                    </div>
                                </form>
                            </div>
                            <!-- /.box -->
                        <?php
                        }
                        else if ($check == false){ ?>
                            <!-- general form elements -->
                            <div class="box no-border">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Initiate Group</h3>
                                </div>
                                <!-- /.box-header -->

                                    <div class="box-body">
                                        <h3>You can not initiate a group</h3>
                                        <ul>
                                            <li>You are either part of a Project group</li>
                                            <li>You have sent request to a group</li>
                                            <li>You initiated a group already</li>
                                        </ul>

                                    </div>
                                    <!-- /.box-body -->

                                    <div class="box-footer">

                                    </div>

                            </div>
                            <!-- /.box -->
                        <?php
                        }
                    }?>


                </div>

                <div class="col-md-2"></div>
            </div>
        </section>
    </div>
    <?php
    require_once("includes/main-footer.php");
    ?>
</div>

<?php
require_once("includes/required_js.php");
?>
</body>
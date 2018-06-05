<?php
$title="FYPMS";
$subtitle="Group Setting";
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



/* Check if:
 * - User is a groupLeader
 * - User is already in a group
 */

$sql = "SELECT * FROM student WHERE studentId = '$studentId'  LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $groupId =$row['groupId'];
        
        if ($row['isLeader'] == null){

            header('Location:' . 'index.php?status=logged_out'); //TODO : 404 Redirect
            //session_destroy();
            die;
        }
        else{
            //Get group name
            $sql = "SELECT projectName FROM student_group WHERE leaderId = '$studentId' AND groupId='$groupId' LIMIT 1";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $projectName =$row['projectName'];
                }
            }else{
                $projectName ='--';
            }
        }


    }
}



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['btnDeleteMyGroup'])){
        //Check if group has no members
        $sql = "SELECT * FROM student_group WHERE leaderId='$studentId' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $inGroup = $row['inGroup'];
                $spdPart = $row['sdpPart'];
                $groupId = $row['groupId'];
            }
            if ($inGroup < 1 OR $spdPart ==1){

                // Set autocommit to off
                mysqli_autocommit($conn, FALSE);

                //Delete group
                // sql to delete a record
                $sql = "DELETE from student_group WHERE leaderId = '$studentId' ";

                if ($conn->query($sql) === TRUE) {
                    //Check and delete request send
                    $sql = "SELECT requestId FROM faculty_student_request WHERE groupId ='$groupId' LIMIT 1";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $sql = "DELETE from faculty_student_request WHERE groupId = '$groupId' ";
                        if ($conn->query($sql) === TRUE) {
                            //Update student record
                            $sql = "UPDATE student SET groupId=null ,isLeader = null WHERE studentId=' $studentId' ";

                            if ($conn->query($sql) === TRUE) {
                                // Commit transaction
                                mysqli_commit($conn);
                                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                            } else {
                                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
                            }
                        }
                    }else{
                        //Update student record
                        $sql = "UPDATE student SET groupId=null ,isLeader = null WHERE studentId=' $studentId' ";

                        if ($conn->query($sql) === TRUE) {
                            // Commit transaction
                            mysqli_commit($conn);
                            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                        } else {
                            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
                        }
                    }
                }
            }
            else{

            }
        }

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


                <div class="col-md-1"></div>

                <div class="col-md-10">

                    <?php
                    if (isset($_GET['status'])){
                        if ($_GET['status'] == 't'){ ?>
                            <div style="text-align:center;" class="alert alert-success" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Changes saved successfully!
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


                            <!-- general form elements -->
                            <div class="box no-border">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-cogs" aria-hidden="true"></i> Group Settings</h3>
                                </div>
                                <!-- /.box-header -->
                                <!-- form start -->

                                    <div class="box-body">
                                        <form action="" method="post" onsubmit="return confirm('Are you sure you want delete your own group?');" data-toggle="validator">

                                        <ul class="todo-list ui-sortable">
                                            <li class="">
                                                <!-- drag handle -->
                                                  <span class="handle ui-sortable-handle">
                                                    <i class="fa fa-cog" aria-hidden="true"></i>
                                                  </span>
                                                <span class="text">Delete my group</span>
                                                <small class="label label-danger"><?php echo $projectName;?></small>
                                                <button type="submit" name="btnDeleteMyGroup" class="btn btn-defualt  btn-xs pull-right">Submit</button>
                                            </li>
                                        </ul>
                                        </form>
                                    </div>
                                    <!-- /.box-body -->

                                    <div class="box-footer">

                                        <a href="<?php echo siteroot ;?>" class="btn btn-default">Back</a>
                                    </div>

                            </div>
                            <!-- /.box -->



                </div>

                <div class="col-md-1"></div>
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
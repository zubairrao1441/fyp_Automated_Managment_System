<?php
$title="FYPMS";
$subtitle="Supervise Group";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if Supervisor is logged in is logged in else log out
if(!isset($_SESSION["design"]))
{
    header('Location: '.'index.php');
}

//Getting values from SESSION
$facultyId = $_SESSION['facultyId'];

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "FORM SUBMITTED POST";exit;

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
                            <h3 class="box-title">List of Groups</h3>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body ">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th style="width: 10px">Group</th>
                                    <th>Project Name</th>
                                    <th style="width: 100px">Batch</th>
                                    <th style="width: 200px">Actions</th>
                                </tr>
                                <?php
                                $sql = "SELECT * FROM faculty_student_group JOIN student_group WHERE facultyId = '$facultyId' AND faculty_student_group.groupId = student_group.groupId";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['groupId'];?></td>
                                            <td><?php echo $row['projectName'];?></td>

                                            <td><?php $batchId= $row['batchId'];
                                                $batchName = $conn->query("SELECT batchName FROM batch WHERE batchId = '$batchId' ")->fetch_object()->batchName;
                                                echo $batchName;
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo $_SERVER['PHP_SELF']."?details=".$row['groupId'];?>" class="btn btn-default btn-sm">Details</a>
                                                <a href="<?php echo $_SERVER['PHP_SELF']."?uploads=".$row['groupId'];?>" class="btn btn-default btn-sm"> <i class="fa fa-upload"></i>Deliverables</a>
                                            </td>

                                        </tr>

                                        <?php
                                    }
                                }
                                ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

                    <?php if (isset($_GET['details']) && is_numeric($_GET['details']) && strlen($_GET['details'])){ ?>
                        <!-- Group Members details -->
                        <div class="box no-border">
                            <div class="box-header with-border">
                                <h3 class="box-title">Group Members Details</h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body ">
                                <table class="table table-condensed ">
                                    <tr>
                                        <th style="width: 10px">CMS</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                    </tr>
                                    <?php
                                    $groupId = filter_input(INPUT_GET,'details',FILTER_SANITIZE_NUMBER_INT);

                                    $sql = "SELECT * FROM student  WHERE groupId = '$groupId' ";

                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // output data of each row
                                        while($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo $row['studentCMS'];?></td>
                                                <td><?php echo $row['studentName'];?></td>
                                                <td><?php echo $row['studentEmail'];?></td>
                                                <td><?php echo $row['studentPhoneNo'];?></td>
                                            </tr>

                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    <?php
                    }
                    else if (isset($_GET['uploads']) && is_numeric($_GET['uploads']) && strlen($_GET['uploads'])){ ?>
                        <!-- Group Uploads details -->
                        <div class="box no-border">
                            <div class="box-header with-border">
                                <h3 class="box-title">Group Deliverables</h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body ">
                                <table class="table table-condensed ">
                                    <tr>
                                        <th>Title</th>
                                        <th>Deliverable</th>
                                        <th>Uploaded <i class="fa fa-clock-o"></i></th>
                                        <th>Uploaded by</th>
                                    </tr>
                                    <?php
                                    $groupId = filter_input(INPUT_GET,'uploads',FILTER_SANITIZE_NUMBER_INT);

                                    $sql = "SELECT * FROM group_uploads WHERE groupId = '$groupId'";

                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // output data of each row
                                        while($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php
                                                    $taskId = $row['taskId'];
                                                    $taskName = $conn->query("SELECT taskName FROM batch_tasks WHERE taskId = '$taskId' LIMIT 1")->fetch_object()->taskName;
                                                    echo $taskName;

                                                    ?>
                                                </td>
                                                <td><?php
                                                    $deliverableName=$row['uploadFile'];
                                                    $groupId = $row['groupId'];

                                                    //Getting batchId,batch Name from groupId
                                                    $batchId = $conn->query("SELECT batchId FROM student_group WHERE groupId = '$groupId' ")->fetch_object()->batchId;
                                                    $batchName = $conn->query("SELECT batchName FROM batch WHERE batchId = '$batchId' ")->fetch_object()->batchName;

                                                    $group = 'Group '.$groupId;

                                                    $location = siteroot."uploads/".$batchName."/".$group."/".$deliverableName;
                                                    echo "<a href=\"$location\">$deliverableName</a>" ;

                                                    ?></td>
                                                    <td><?php echo $row['uploadedDtm'];?></td>
                                                    <td><?php
                                                    $studentId =$row['uploadedBy'];
                                                    $studentName = $conn->query("SELECT studentName FROM student WHERE studentId = '$studentId' LIMIT 1")->fetch_object()->studentName;
                                                    echo "<a href=\"studentProfile.php?id=$studentId\">$studentName</a>" ;
                                                    ?>
                                                </td>
                                            </tr>

                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->

                    <?php
                    }
                    ?>






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
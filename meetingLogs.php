<?php
//TODO send meeting logs to student notifications
$title="FYPMS";
$subtitle="Meeting Logs";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if supervisor is logged in
if(!isset($_SESSION["design"]))
{
    header('Location: '.'index.php');
}
$supervisorId = $_SESSION['facultyId'];

//Check if supervisor has groups
$sql = "SELECT * FROM faculty_student_group WHERE facultyId = '$supervisorId' ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $groupCheck = true;
    $numOfGroups =$result->num_rows;

    while($row = $result->fetch_assoc()) {
        //echo $row['groupId'];echo '<br/>';
    }
}
else{
    //This faculty isnt supervising any group
    $groupCheck = false;
}



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /*
     * EDIT MEETING LOG
     */

    if (isset($_POST['btnEditLog'])){
        //EDIT log
        $logId = filter_input(INPUT_POST,'editId',FILTER_SANITIZE_NUMBER_INT);
        $groupId = filter_input(INPUT_POST,'groupId',FILTER_SANITIZE_NUMBER_INT);
        //Validations
        if ($_POST['status'] != "" && $_POST['meetingTitle'] != ""){

            $groupId = filter_input(INPUT_POST,'groupId',FILTER_SANITIZE_NUMBER_INT);

            $status = filter_input(INPUT_POST,'status',FILTER_SANITIZE_SPECIAL_CHARS);
            $title = filter_input(INPUT_POST,'meetingTitle',FILTER_SANITIZE_SPECIAL_CHARS);
            $comments = $_POST['addComments'];

            $sql = "UPDATE meeting_logs SET meeting_title = '$title' , meeting_status='$status' , comments='$comments' WHERE id='$logId' ";

            if ($conn->query($sql) === TRUE) {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t&id='.$groupId);
            } else {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f&id='.$groupId);
            }
        }
    }



    //ADD NEW LOG
    if (isset($_POST['addNewLogBtn'])){
        //Validations
        if (isset($_POST['meetingTitle']) && isset($_POST['groupId']) ){

            $meetingTitle = filter_input(INPUT_POST,'meetingTitle',FILTER_SANITIZE_SPECIAL_CHARS);
            $groupId = filter_input(INPUT_POST,'groupId',FILTER_SANITIZE_NUMBER_INT);

            //$meetingDate = date('Y-m-d', strtotime($_POST['meetingDate']));
            $meetingDate = $_POST['meetingDate'];
            $meetingTime = $_POST['meetingTime'];
            //$meetingTime = $_POST['meetingTime'];
            //Converting deadline to MySql format
            $dateTime = $meetingDate ." ". $meetingTime;
            $dateTime = date('Y-m-d H:i:s', strtotime($dateTime));

            $sql = "INSERT INTO meeting_logs (supervisor_id, group_id, meeting_title, meeting_dtm, meeting_status)VALUES ('$supervisorId', '$groupId', '$meetingTitle', '$dateTime','1')";

            if ($conn->query($sql) === TRUE) {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t&id='.$groupId);
            } else {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f&id='.$groupId);
            }


        }

    }

    //ADD COMMENTS
    if(isset($_POST['btnAddComments'])){
        //echo 'HERE';exit
        //Validations
        if (isset($_POST['comments']) && $_POST['comments'] != ""){
            $groupId = filter_input(INPUT_POST,'groupId',FILTER_SANITIZE_NUMBER_INT);
            $logId = filter_input(INPUT_POST,'editId',FILTER_SANITIZE_NUMBER_INT);
            $comments = $_POST['comments'];

            $sql = "UPDATE meeting_logs SET comments = '$comments' WHERE id='$logId' ";

            if ($conn->query($sql) === TRUE) {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t&id='.$groupId);
            } else {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f&id='.$groupId);
            }
        }
    }

    //DELETE MEETING LOG
    if (isset($_POST['btnDelete'])){
        $groupId = filter_input(INPUT_POST,'groupId',FILTER_SANITIZE_NUMBER_INT);
        $logId = filter_input(INPUT_POST,'logId',FILTER_SANITIZE_NUMBER_INT);

        // sql to delete a record
        $sql = "DELETE FROM meeting_logs WHERE id= '$logId' LIMIT 1";

        if ($conn->query($sql) === TRUE) {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t&id='.$groupId);
        } else {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f&id='.$groupId);
        }
    }

}



?>


<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper" >
        <?php require_once("includes/content-header.php"); ?>

        <section class="content" style="min-height: 700px">
            <div class="row">
                <div class="col-md-12">

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
                        else if ($_GET['status'] == 's'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error!
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


            <?php if ($groupCheck == true){ ?>
                <?php if (isset($_GET['add'])){
                    /*******************
                     * ADD MEETING LOGS
                     * ******************/?>
                    <div class="box no-border">
                        <div class="box-header">
                            <h3 class="box-title">Add New Meeting Log</h3>

                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" id="addNewLog" name="addNewLog" method="post" data-toggle="validator">
                            <div class="box-body">
                                <div class="form-group">
                                    <label>Meeting Title</label>
                                    <input type="text" class="form-control" id="meetingTitle" name="meetingTitle" placeholder="Enter meeting title" required>
                                </div>

                                <div class="form-group">
                                    <label>Select Group</label>
                                    <select name="groupId" class="form-control" required>
                                        <?php
                                        $sql = "SELECT * FROM faculty_student_group JOIN student_group WHERE facultyId = '$supervisorId' AND student_group.groupId = faculty_student_group.groupId ";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) { ?>
                                                <option value="<?php echo $row['groupId']; ?>"><?php if (isset($row['projectName'])){echo 'Group:'.$row['groupId']. '[ '.$row['projectName'].' ] ';}else{ echo '--';}?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <label>Select Date & Time:</label>
                                        <div class="form-group">
                                            <input name="meetingDate" id="meetingDate" type=date   required>
                                            <input name="meetingTime" id="meetingTime" type=time required>
                                        </div>


                                    </div>


                                    <div class="col-md-6">

                                    </div>

                                </div>

                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <button onclick="goBack()" class="btn btn-default">Back</button>
<!--                                <a href="--><?php //echo $_SERVER['PHP_SELF'];?><!--" class="btn btn-default ">Cancel</a>-->
                                <button type="submit" name="addNewLogBtn" class="btn btn-primary pull-right" onclick="return confirm('Are you sure?')">Submit</button>
                            </div>
                        </form>

                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->


                    <?php
                }?>



                <?php if (isset($_GET['edit']) && is_numeric($_GET['edit']) && strlen($_GET['edit']) > 0 ){
                    /*******************
                     * EDIT MEETING LOGS
                     * ******************/

                    $id = filter_input(INPUT_GET,'edit',FILTER_SANITIZE_NUMBER_INT);

                    $sql = "SELECT * from meeting_logs WHERE supervisor_id='$supervisorId' AND id='$id' LIMIT 1";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $meetingTitle = $row['meeting_title'];
                            $meetingStatus = $row['meeting_status'];
                            $meetingComments = $row['comments'];
                            $meetingDtm = $row['meeting_dtm'];
                            $groupId = $row['group_id'];
                            $status = $row['meeting_status'];
                        }
                        ?>
                    <div class="box no-border">
                        <div class="box-header">
                            <h3 class="box-title">Edit Meeting Log: <?php echo $meetingTitle?></h3>
                        </div>

                        <div class="box-body">
                            <!-- form start -->
                            <form id="editLogs" name="editLogs" action="" method="post" data-toggle="validator">
                                <input type="hidden" name="groupId" value="<?php echo $groupId;?>">
                                <input type="hidden" name="editId" id="editId" value="<?php echo $id;?>">

                                <div class="box-body">

                                    <div class="form-group">
                                        <label>Meeting Title</label>
                                        <input type="text" name="meetingTitle" id="meetingTitle" class="form-control" value="<?php echo $meetingTitle; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Change Status</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="" <?php if($status==""){echo "selected";}?>>SELECT STATUS</option>
                                            <option value="Pending" <?php if($status=="Pending"){echo "selected";}?>>Pending</option>
                                            <option value="Postponed" <?php if($status=="Postponed"){echo "selected";}?>>Postponed</option>
                                            <option value="Done" <?php if($status=="Done"){echo "selected";}?>>Done</option>
                                            <option value="Cancelled" <?php if($status=="Cancelled"){echo "selected";}?>>Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Add Comments</label>
                                        <div class="box-body pad">
                                            <form data-toggle="validator">
                                                <textarea class="textarea" name="addComments"  placeholder="Add Comments..."  style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.box-body -->

                            </form>
                                <div class="box-footer">
                                    <button onclick="goBack()" class="btn btn-default">Back</button>
<!--                                    <a href="--><?php //echo $_SERVER['PHP_SELF'];?><!--" class="btn btn-default">Back</a>-->
                                    <button type="submit" form="editLogs" name="btnEditLog"  class="btn btn-primary pull-right">Submit</button>
                                </div>

                        </div>


                        </div>



                    <?php
                    }


                ?>





                <?php
                }else if(isset($_GET['comment']) && is_numeric($_GET['comment']) && strlen($_GET['comment']) > 0){
                    /*******************
                     * EDIT MEETING COMMENT
                     * ******************/

                    $id = filter_input(INPUT_GET,'comment',FILTER_SANITIZE_NUMBER_INT);


                    $sql = "SELECT * from meeting_logs WHERE supervisor_id='$supervisorId' AND id='$id' LIMIT 1";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $meetingTitle = $row['meeting_title'];
                        }
                        ?>
                        <div class="box no-border">
                            <div class="box-header">
                                <h3 class="box-title">Edit Meeting Log: <?php echo $meetingTitle?></h3>
                            </div>

                            <div class="box-body">
                                <!-- form start -->
                                <form id="addComments" name="addComments" action="" method="post" data-toggle="validator">
                                    <input type="hidden" name="groupId" value="<?php echo $groupId;?>">
                                    <input type="hidden" name="editId" id="editId" value="<?php echo $id;?>">

                                    <div class="box-body">



                                        <div class="form-group">
                                            <label>Add Comments</label>
                                            <div class="box-body pad">
                                                <form data-toggle="validator">
                                                    <textarea class="textarea" name="comments"  placeholder="Add Comments..."  style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.box-body -->

                                </form>
                                <div class="box-footer">
                                    <button onclick="goBack()" class="btn btn-default">Back</button>
<!--                                    <a href="--><?php //echo $_SERVER['PHP_SELF'];?><!--" class="btn btn-default">Back</a>-->
                                    <button type="submit" form="addComments" name="btnAddComments"  class="btn btn-primary pull-right">Submit</button>
                                </div>

                            </div>


                        </div>



                        <?php
                    }


                }


                else{
                    /*******************
                     * SHOW MEETING LOGS
                     * ******************/
                    ?>
                    <div class="box no-border">
                        <div class="box-header">
                            <h3 class="box-title">Meeting Logs</h3>

                            <div class="box-tools">
                                <form id="selectGroup"  method="get" name="selectGroup" data-toggle="validator">
                                    <div class="input-group input-group-sm" style="width: 250px;">

                                        <select name="id" class="form-control" required>
                                            <?php
                                            $sql = "SELECT * FROM faculty_student_group JOIN student_group WHERE facultyId = '$supervisorId' AND student_group.groupId = faculty_student_group.groupId ";
                                            $result = $conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                while($row = $result->fetch_assoc()) { ?>
                                                    <option value="<?php echo $row['groupId']; ?>"><?php if (isset($row['projectName'])){echo 'Group:'.$row['groupId']. '[ '.$row['projectName'].' ] ';}else{ echo '--';}?>
                                                    </option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>

                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php
                        if (isset($_GET['id']) && is_numeric($_GET['id']) && strlen($_GET['id'])>0){ ?>
                            <!-- /.box-header -->
                            <div class="box-body  no-padding">
                                <table id="meetingLogs" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Group</th>
                                        <th>Title</th>
                                        <th>Meeting Time</th>
                                        <th>Comments</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>

                                    <?php

                                    $groupId = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
                                    //Check if this supervisor has this group
                                    $sql = "SELECT id from meeting_logs WHERE supervisor_id='$supervisorId' AND group_id='$groupId' LIMIT 1";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        $sql = "SELECT * from meeting_logs WHERE supervisor_id = '$supervisorId' AND group_id = '$groupId' ORDER BY created_dtm DESC";
                                        $result = $conn->query($sql);
                                        while($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo $row['group_id'] ;?></td>
                                                <td><?php echo $row['meeting_title'];?></td>
                                                <td><?php echo $row['meeting_dtm'] ;?></td>
                                                <td><?php
                                                    if (is_null($row['comments'])){ ?>
                                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?comment=' . $row['id']; ?>"  class="btn  btn-default btn-flat btn-xs">Add Comments</a>
                                                        <?php
                                                    }else{
                                                        echo $row['comments'];
                                                    }
                                                    ;?></td>

                                                <form id="logActions" name="logActions" method="post" data-toggle="validator">
                                                    <th><?php
                                                        $status =$row['meeting_status'];
                                                        if ($status == 'Pending'){ ?>
                                                            <span class="label label-warning"><?php echo $status?></span>

                                                            <?php
                                                        }
                                                        else if ($status == 'Done'){ ?>
                                                            <span class="label label-success"><?php echo $status?></span>
                                                            <?php
                                                        }
                                                        else if ($status == 'Cancelled'){ ?>
                                                            <span class="label label-danger"><?php echo $status?></span>

                                                            <?php
                                                        }
                                                        else if ($status == 'Postponed'){ ?>
                                                            <span class="label label-primary"><?php echo $status?></span>

                                                            <?php
                                                        }else{ ?>
                                                            <span class="label label-default"><?php echo $status?></span>

                                                            <?php
                                                        }
                                                        ;?></th>
                                                </form>
                                                <td>
                                                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?edit=' . $row['id']; ?>"   class="btn  btn-default btn-flat  btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                                                    <form  action="" method="post" onsubmit="return confirm('Are you sure you want to delete this record?');" data-toggle="validator">
                                                        <input type="hidden" name="logId" value="<?php  echo $row['id'];?>">
                                                        <input type="hidden" name="groupId" value="<?php echo $row['group_id'];?>">
                                                        <button type="submit" name="btnDelete" class="btn  btn-danger btn-flat  btn-xs"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                                    </form>
                                                    <input type="hidden" name="logId" value="<?php echo $row['id'];?>">
                                                </td>
                                            </tr>
                                        <?php }
                                    }

                                    ?>
                                </table>
                                <div class="box-footer  pull-right">
                                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?id='.$groupId.'&add'; ?>" class="btn  btn-primary  ">Add New Log</a>
                                </div>
                            </div>

                        <?php }
                        ?>

                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

                <?php
                }?>

                <?php
            }else if ($groupCheck == false){ ?>
                <div class="box no-border">
                    <div class="box-header">
                        <h3 class="box-title">Add New Meeting Log</h3>

                    </div>
                    <!-- /.box-header -->
                        <div class="box-body">
                            <p>You are not supervising any group</p>
                        </div>
                        <!-- /.box-body -->
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            <?php
            } ?>
                </div>

            </div>
        </section>
    </div><!-- ./content-wrapper -->
    <!--</div>-->
    <?php
    require_once("includes/main-footer.php");
    ?>
</div>
<!-- ./wrapper -->
<?php
require_once("includes/required_js.php");
?>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
    function goBack() {
        window.history.back();
    }

    $(document).ready(function() {

        $('.textarea').wysihtml5();

        $('#meetingLogs').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false
        });
    } );
</script>


</body>
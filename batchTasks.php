<?php
$title="FYPMS";
$subtitle="Batch Tasks";
require_once("includes/header.php");
require_once("includes/config.php");
require_once ("includes/functions.php");
session_start();

//Check if coordinator is logged in
if(!isset($_SESSION["isCord"]))
{
    header('Location: '.'index.php');
}

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    //Function for delete task
    if (isset($_GET['delete']) && isset($_GET['batchId'])){

        //Validations
        if (is_numeric($_GET['delete']) && strlen($_GET['delete'])>0 && is_numeric($_GET['batchId']) && strlen($_GET['batchId'])>0){
            $deleteId = filter_input(INPUT_GET,'delete',FILTER_SANITIZE_NUMBER_INT);
            $batchId =  filter_input(INPUT_GET,'batchId',FILTER_SANITIZE_NUMBER_INT);

            $taskId = '';

            //Check if this record belongs to batch
            $sql = "SELECT taskId FROM batch_tasks WHERE taskId='$deleteId' AND batchId = '$batchId' LIMIT 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                    $taskId = $row['taskId'];
                }

                // Set autocommit to off
                mysqli_autocommit($conn, FALSE);
                // sql to delete a record
                $sql = "DELETE FROM batch_tasks WHERE taskId='$deleteId' AND batchId= '$batchId' ";

                if ($conn->query($sql) === TRUE) {
                   //Also delete from timeline
                    // sql to delete a record
                    $sql = "DELETE FROM timeline_student WHERE taskId='$taskId' AND batchId='$batchId' ";

                    if ($conn->query($sql) === TRUE) {
                        mysqli_commit($conn);
                        header('Location:' . $_SERVER['PHP_SELF'] . '?batchId='.$batchId.'&status=t');
                        die;
                    } else {
                        header('Location:' . $_SERVER['PHP_SELF'] . '?batchId='.$batchId.'&status=f');
                        die;
                    }
                }

            }
        }


    }


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {



    if (isset($_POST['btnAddNewTask'])){

        //Getting values from POST and Sanitizing
        $batchId = filter_input(INPUT_POST,'batchId',FILTER_SANITIZE_NUMBER_INT);
        $taskWeek = filter_input(INPUT_POST,'week',FILTER_SANITIZE_NUMBER_INT);
        $sdpPart = filter_input(INPUT_POST,'sdpPart',FILTER_SANITIZE_NUMBER_INT);
        $taskName = filter_input(INPUT_POST,'taskName',FILTER_SANITIZE_SPECIAL_CHARS);
        $templateId = filter_input(INPUT_POST,'templateId',FILTER_SANITIZE_NUMBER_INT); if($templateId == ""){$templateId=null;};
        $hasDeliverable = filter_input(INPUT_POST,'hasDeliverable',FILTER_SANITIZE_NUMBER_INT);if($hasDeliverable == ""){$hasDeliverable=0;};
        $sendToTimeline = filter_input(INPUT_POST,'sendToTimeline',FILTER_SANITIZE_NUMBER_INT);
        $taskDetail = $_POST['taskDetail'];


        $deadlineDate = $_POST['deadlineDate'];
        $deadlineTime = $_POST['deadlineTime'];

        if ($deadlineDate!="" OR $deadlineTime!=""){
            //Converting deadline to MySql format
            $deadline = $deadlineDate ." ". $deadlineTime;
            $deadline = date('Y-m-d H:i:s', strtotime($deadline));
        }
        else{
            $deadline = null;
        }



        // Set autocommit to off
        mysqli_autocommit($conn, FALSE);

        // prepare and bind
        $stmt = $conn->prepare("INSERT INTO batch_tasks (batchId, taskName, taskDetail, taskWeek, taskDeadline, templateId, hasDeliverable, sdpPart) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("issisiii", $batchId, $taskName, $taskDetail, $taskWeek,$deadline,$templateId,$hasDeliverable,$sdpPart);

        $stmt->execute();
        if ($stmt->affected_rows > 0) {

            if ($sendToTimeline == '1'){
                //Get Las insert id
                $last_id = $stmt->insert_id;


                //Also send to timeline
                $type = 'task';
                $stmt = $conn->prepare("INSERT INTO timeline_student (title, details, type, taskId, batchId, sdpPart) VALUES (?,?,?,?,?,?) ");
                $stmt->bind_param("sssiii", $taskName,$taskDetail,$type,$last_id,$batchId,$sdpPart);
                $stmt->execute();
                // Commit transaction
                mysqli_commit($conn);
                $stmt->close();
                $conn->close();
                header('Location:' . $_SERVER['PHP_SELF'] . '?add='.$batchId.'&batchId='.$batchId.'&status=t');die;
            }
            // Commit transaction
            mysqli_commit($conn);
            $stmt->close();
            $conn->close();
            header('Location:' . $_SERVER['PHP_SELF'] . '?add='.$batchId.'&batchId='.$batchId.'&status=t');die;
        }
        else{
            //printf("Error: %s.\n", $stmt->error);exit;
            header('Location:' . $_SERVER['PHP_SELF'] . '?add='.$batchId.'&batchId='.$batchId.'&status=f');die;
        }



    }

    if (isset($_POST['btnEdit'])){

    }

    if (isset($_POST['btnDelete'])){

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
                <div class="col-lg-12">

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




                        <?php


                         if (isset($_GET['add']) && is_numeric($_GET['add']) && strlen($_GET['add'])>0){ ?>
                            <!--Add new task-->
                            <div class="box no-border">
                                <div class="box-header">
                                    <h3 class="box-title">
                                        Add new task --
                                        <?php
                                        $batchId = filter_input(INPUT_GET,'add',FILTER_SANITIZE_NUMBER_INT);
                                        if ($batchId){
                                            //Get batch Name
                                            $batchName = $conn->query("SELECT batchName FROM batch WHERE batchId = '$batchId' ")->fetch_object()->batchName;
                                            $batchStartDate = $conn->query("SELECT startingDate FROM batch WHERE batchId = '$batchId' ")->fetch_object()->startingDate;
                                            echo "Batch ".$batchName;

                                        }else{
                                            echo "--";
                                        }
                                        ?>
                                    </h3>
                                </div>
                                <!-- /.box-header -->
                                <form name="addNewTask" id="addNewTask" action="" class="form-horizontal" method="post" data-toggle="validator">
                                    <input type="hidden" name="batchId" value="<?php echo $batchId;?>">
                                    <div class="box-body  no-padding">

                                        <div class="form-group">
                                            <label  class="col-sm-2 control-label">Select Week:</label>

                                            <div class="col-sm-10">
                                                <select style="width:200px;"  name="week" id="week" required>
                                                    <option value=""> -Select Week-</option>
                                                    <?php
                                                    for ($i=1 ;$i<=20; $i++){ ?>
                                                        <option value="<?php echo $i;?>">Week <?php echo $i;?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label  class="col-sm-2 control-label">Select SDP Part:</label>

                                            <div class="col-sm-10">
                                                <select style="width:200px;"  name="sdpPart" id="sdpPart" required>
                                                    <option value=""> -Select SDP Part-</option>
                                                    <option value="1"> SDP Part - 1</option>
                                                    <option value="2"> SDP Part - 2</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label  class="col-sm-2 control-label">Task Name:</label>

                                            <div class="col-sm-10">
                                                <input type="text" style="width:500px;" name="taskName" id="taskName" placeholder="Enter Task Name" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label  class="col-sm-2 control-label">Task Details:</label>

                                            <div class="col-sm-10">
                                                <textarea class="textarea" name="taskDetail" id="taskDetail" placeholder="Enter task details" style="width: 90%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label  class="col-sm-2 control-label">Set Deadline:</label>
                                            <div class="col-sm-10">
                                                <input name="deadlineDate" id="deadlineDate" type=date  >
                                                <input name="deadlineTime" id="deadlineTime" type=time >
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label  class="col-sm-2 control-label">Select Template:</label>

                                            <div class="col-sm-10">
                                                <select style="width:400px;"  name="templateId" id="templateId">
                                                    <option value=""> -Select Template-</option>
                                                    <?php
                                                    $sql = "SELECT * FROM batch_templates WHERE batchId='$batchId' ";
                                                    $result = $conn->query($sql);

                                                    if ($result->num_rows > 0) {
                                                        // output data of each row
                                                        while($row = $result->fetch_assoc()) { ?>
                                                            <option value="<?php echo $row['templateId']?>"><?php echo $row['templateName']?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="hasDeliverable" value="1"> This task has deliverable
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="sendToTimeline" id="sendToTimeline" value="1" checked> Send Notification to timeline
                                                    </label>
                                                </div>
                                            </div>
                                        </div>




                                    </div>
                                </form>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" name="btnAddNewTask" id="btnAddNewTask" form="addNewTask" class="btn btn-primary btn-sm pull-right">Submit</button>
                                    <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn btn-default btn-sm"">Back</a>
                                </div>
                            </div>
                            <!-- /.box -->


                        <?php
                        }
                        ?>





                        <div class="box no-border">
                            <div class="box-header">
                                <h3 class="box-title">
                                    <?php
                                    $batchId = filter_input(INPUT_GET,'batchId',FILTER_SANITIZE_NUMBER_INT);
                                    if ($batchId){
                                        //Get batch Name
                                        $batchName = $conn->query("SELECT batchName FROM batch WHERE batchId = '$batchId' ")->fetch_object()->batchName;
                                        echo "Batch ".$batchName;

                                    }else{
                                        echo "<p class=\"text-muted\">Select batch from the list</p>";
                                    }
                                    ?>

                                </h3>

                                <div class="box-tools">
                                    <form name="selectBatch"  id="selectBatch" method="get" name="selectGroup" data-toggle="validator">

                                        <div class="input-group input-group-sm" style="width: 250px;">

                                            <select name="batchId"  id="batchId" class="form-control" required>
                                                <?php
                                                $sql = "SELECT * FROM batch WHERE  batch.isActive = 1";
                                                $result = $conn->query($sql);
                                                if ($result->num_rows > 0) {
                                                    while($row = $result->fetch_assoc()) { ?>
                                                        <option value="<?php echo $row['batchId']; ?>" >
                                                            <?php echo $row['batchName'];?>
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
                            <!-- /.box-header -->
                            <?php if (isset($_GET['batchId']) && is_numeric($_GET['batchId']) && strlen($_GET['batchId']) >0 ){ ?>

                            <div class="box-body  no-padding">
                                <table id="batchTasks" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width:10px;">Week</th>
                                        <th style="width:100px;">Task Name</th>
                                        <th >Task Details</th>
                                        <th style="width:100px;">Deadline</th>
                                        <th>Template</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>

                                    <?php

                                    $batchId = filter_input(INPUT_GET,'batchId',FILTER_SANITIZE_NUMBER_INT);

                                    $sql = "SELECT * from batch_tasks WHERE batchId = '$batchId' ORDER BY taskWeek ASC";
                                    $result = $conn->query($sql);
                                    while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['taskWeek'];?></td>
                                            <td><?php echo $row['taskName'];?></td>
                                            <td>
                                                <?php
                                                if (strlen($row['taskDetail']) > 50){
                                                    echo getExcerpt($row['taskDetail'],"0","100");
                                                }else{
                                                    echo $row['taskDetail'];
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $row['taskDeadline'];?></td>
                                            <td><?php echo $row['templateId'];?></td>
                                            <td>

                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?delete=' . $row['taskId'].'&batchId='.$batchId; ?>" onclick="return confirm('This will also delete record from timeline,Are you sure you want to continue?')" class="btn  btn-danger btn-xs">Delete</a>

                                            </td>
                                        </tr>
                                    <?php }

                                    ?>
                                </table>
                                <?php } ?>
                                <div class="box-footer">
                                    <a href="<?php echo siteroot; ?>" class="btn  btn-default btn-sm  "><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?add='.$batchId.'&batchId='.$batchId; ?>" class="btn  btn-primary btn-sm pull-right" <?php if (!isset($_GET['batchId'])){echo 'disabled';}?>>Add New Task</a>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->


                </div>

<!--            </div>-->
        </section>
    </div>

    <?php
    require_once("includes/main-footer.php");
    ?>
</div>
<!-- ./wrapper -->
<?php
require_once("includes/required_js.php");
?>

<script type="text/javascript">



    // A $( document ).ready() block.
    $( document ).ready(function() {

        $(".textarea").wysihtml5();

        //$("#selectBatch").submit()


    });

</script>
</body>
</html>
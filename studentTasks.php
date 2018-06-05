<?php
$title="FYPMS";
$subtitle="Tasks";
require_once("includes/header.php");
require_once("includes/config.php");
require_once("includes/functions.php");
session_start();

//Check if student is logged in
if(!isset($_SESSION["usrCMS"]))
{
    header('Location: '.'index.php');
}
$check = true; //

//$groupId = $_SESSION['GroupID'];
$batchId = $_SESSION['BatchID'];
$studentId = $_SESSION['usrId'];
//Getting group id and Project Name from DATABASE
$sql = "SELECT * FROM student_group WHERE student_group.leaderId = '$studentId' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $groupId = $row['groupId'];

    }
}else{
    $groupId = $_SESSION["GroupID"];
}

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (isset($_GET['upload']) && is_numeric($_GET['upload']) && strlen($_GET['upload'])){
        $uploadId = filter_input(INPUT_GET,'upload',FILTER_SANITIZE_NUMBER_INT);
        $batchId = $_SESSION['BatchID'];
        //Check if already submitted
        $sql = "SELECT * FROM group_uploads WHERE  groupId='$groupId' AND taskId='$uploadId' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $uploadedBy = $row['uploadedBy'];
                $uploadDtm = $row['uploadedDtm'];
            }

            $check = false;

            $sql = "SELECT studentCMS,studentName FROM student WHERE  studentId = '$uploadedBy' LIMIT 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $cms = $row['studentCMS'];
                    $name = $row['studentName'];

                }
            }
        }


    }
    
    





}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $groupName = 'Group '.$groupId;

    $taskId = filter_input(INPUT_POST,'taskId',FILTER_SANITIZE_NUMBER_INT);

    //Getting Batch Data
    $batchId = $_SESSION["BatchID"];
    $batchName = $conn->query("SELECT batchName FROM batch WHERE batch.batchId = '$batchId' ")->fetch_object()->batchName;

    //Function to upload task
    if (isset($_FILES['uploadTask'])){

        $file=$_FILES['uploadTask'];

        //File properties
        $file_name  =   $file['name'];
        $file_tmp   =   $file['tmp_name'];
        $file_size  =   $file['size'];
        $file_error =   $file['error'];

        //Work out file extension
        $file_ext   =   explode('.',$file_name);
        $file_ext   = strtolower(end($file_ext));

        $allowed    = array('jpg','jpeg','pdf','doc','docx','zip','7zip','rar','ppt','pptx');

        if(in_array($file_ext,$allowed)){
            if($file_error === 0){
                if($file_size <= 10485760){ //10Mib
                    $file_name_new  = 'group_'.$groupId.'_deliverable_'.$taskId.'.'.$file_ext;

                    //Make a directory with group name
                    if (!file_exists('uploads/'.$batchName.'/'.$groupName.'/')) {
                        mkdir('uploads/'.$batchName.'/'.$groupName.'/', 0777, true);
                    }
                    $file_destination   ='uploads/'.$batchName.'/'.$groupName.'/'.$file_name_new;
                    /* Example tree Structure
                     * └───Spring 2016
                     *       └───Group 9
                     *          └───group_9_deliverable_2
                     */


                }else {
                    //ERROR! filesize greater
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=s');
                }
                if(move_uploaded_file($file_tmp, $file_destination)){
                    //echo $file_destination;

                    //FILE UPLOADED SUCCESSFULLY

                    $sql = "INSERT INTO group_uploads (groupId, taskId, uploadFile, uploadedBy)VALUES ('$groupId', '$taskId', '$file_name_new', '$studentId')";

                    if ($conn->query($sql) === TRUE) {
                        header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
                    } else {
                        header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
                    }


                    $stmt->close();

                }
                else {
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
                }
            }
        }else {
            //Not allowed extension
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=e');
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
                <div class="col-md-12">


                    <?php
                    if (isset($_GET['status'])){
                        if ($_GET['status'] == 't'){ ?>
                            <div style="text-align:center;" class="alert alert-success" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Deliverable Uploaded Successfully!
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
                                Error! Filesize exceeded ; Max Filesize 50Mib
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                        else if ($_GET['status'] == 'e'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! File type not supported ; Allowed file types (PDF,DOCX,RAR,ZIP,JPG)
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                    }

                    ?>

                    <?php if (isset($_GET['upload']) && is_numeric($_GET['upload']) && strlen($_GET['upload']) && $check == true){
                        $uploadId = filter_input(INPUT_GET,'upload',FILTER_SANITIZE_NUMBER_INT);
                        $batchId = $_SESSION['BatchID'];

                        //Verification
                        $sql = "SELECT * FROM batch_tasks WHERE  batchId='$batchId' AND taskId='$uploadId' LIMIT 1";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while($row = $result->fetch_assoc()) {
                                $taskName = $row['taskName'];
                                $deadline = $row['taskDeadline'];
                            }
                        }?>
                        <!-- general form elements -->
                        <div class="box no-border">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-upload"></i> Upload <?php echo $taskName;?></h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body ">
                                <div class="form-group">
                                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="post" enctype="multipart/form-data" data-toggle="validator">
                                        <div class="col-sm-10">
                                            <input type="file" name="uploadTask" class="filestyle " data-size="sm" accept=".doc ,.docx, .pdf, .rar, .zip, .jpg, .jpeg, .ppt, .pptx" required />
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="submit" value="Upload" class="btn btn-primary btn-sm  "/>
                                        </div>
                                        <!--HIDDEN INPUT-->
                                        <input type="hidden" name="taskId" value="<?php echo $_GET['upload']?>">
                                    </form>
                                </div>
                                <br/>
                                <p class="text-muted text-center">File size limit :10 MiB</p>
                                <p class="text-muted text-center">Allowed File types : docx | pdf | zip | rar | jpeg | pptx   </p>




                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn btn-default btn-sm">Cancel</a>

                            </div>

                        </div>

                       


                        <?php
                        }else if ($check == false){ ?>
                        <!-- general form elements -->
                        <div class="box no-border">


                            <div class="box-body">
                                <br/>
                                <p class="">Deliverable is already uploaded by  <a href="studentProfile.php?id=<?php echo $studentId;?>"><?php echo $name.' ['.$cms.'] ';?></a> </p>
                                <p class="text-muted"><i class="fa fa-clock-o"></i><?php echo " ".time2str($uploadDtm);?></p>

                            </div>
                            <!-- /.box-body -->



                        </div>
                        <!-- /.box -->
                    <?php
                    }?>





                    <?php if (isset($_GET['details']) && is_numeric($_GET['details']) && strlen($_GET['details'])>0){
                        $detailsId = filter_input(INPUT_GET,'details',FILTER_SANITIZE_NUMBER_INT);
                        $sql = "SELECT * FROM batch_tasks WHERE taskId='$detailsId' LIMIT 1 ";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while($row = $result->fetch_assoc()) {
                                $taskName = $row['taskName'];
                                $taskDetail = $row['taskDetail'];
                                $taskDeadline = $row['taskDeadline'];


                            }
                        } else {
                            $taskName = '--';
                            $taskDetail = 'Nothing to show';
                            $taskName = '--';

                        }

                        ?>

                        <!-- Modal -->
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel"><?php echo $taskName;?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo $taskDetail;?>
                                        <br/>

                                        <p class="text-danger text-center">Deadline : <?php echo $taskDeadline; ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } ?>

                    <?php if (!is_null($groupId)){ ?>
                        <!-- general form elements -->
                        <div class="box no-border ">
                            <div class="box-header with-border">
                                <h3 class="box-title">SDP Part 1</h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">
                                <table class="table table-striped">
                                    <tr>
                                        <th style="width: 10px">Week</th>
                                        <th>Task</th>
                                        <th>Details</th>
                                        <th>Template</th>
                                        <th>Deadline</th>
                                        <th>Action</th>
                                    </tr>
                                    <?php
                                    $sql = "SELECT * FROM batch_tasks WHERE batchId ='$batchId' AND sdpPart = '1' ORDER BY taskWeek ASC ";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // output data of each row
                                        while($row = $result->fetch_assoc()) { ?>

                                            <tr>
                                                <td><?php echo $row['taskWeek']; ?></td>
                                                <td><?php echo $row['taskName']; ?></td>
                                                <td>
                                                    <?php
                                                    if (strlen($row['taskDetail']) >= 100){
                                                        echo getExcerpt($row['taskDetail'],0,100);?>
                                                        <a href="<?php echo $_SERVER['PHP_SELF'].'?details='.$row['taskId']?>">Show Details</a>

                                                        <?php
                                                    }
                                                    else{
                                                        echo $row['taskDetail'];
                                                    }

                                                    ?>
                                                </td>
                                                <td><?php echo $row['templateId']; ?></td>
                                                <td><?php echo $row['taskDeadline']; ?></td>

                                                <td>
                                                    <?php  if ($row['hasDeliverable'] == '1'){ ?>
                                                        <a href="<?php echo $_SERVER['PHP_SELF'].'?upload='.$row['taskId']?>" class="btn btn-default btn-sm"><i class="fa fa-upload"></i> Upload</a>
                                                        <?php
                                                    }else{
                                                        echo '-- --';
                                                    } ?>
                                                </td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>

                                </table>

                            </div>
                            <!-- /.box-body -->

                            <div class="box-header with-border">
                                <h3 class="box-title">SDP Part 2</h3>
                            </div>
                            <!-- /.box-header -->


                            <div class="box-body">
                                <table class="table table-striped">
                                    <tr>
                                        <th style="width: 10px">Week</th>
                                        <th>Task</th>
                                        <th>Details</th>
                                        <th>Template</th>
                                        <th>Deadline</th>
                                        <th>Action</th>
                                    </tr>
                                    <?php
                                    $sql = "SELECT * FROM batch_tasks WHERE batchId ='$batchId' AND sdpPart = '2' ORDER BY taskWeek ASC ";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // output data of each row
                                        while($row = $result->fetch_assoc()) { ?>

                                            <tr>
                                                <td><?php echo $row['taskWeek']; ?></td>
                                                <td><?php echo $row['taskName']; ?></td>
                                                <td>
                                                    <?php
                                                    if (strlen($row['taskDetail']) >= 100){
                                                        echo getExcerpt($row['taskDetail'],0,100);?>


                                                        <a href="<?php echo $_SERVER['PHP_SELF'].'?details='.$row['taskId']?>">Show Details</a>


                                                        <?php
                                                    }
                                                    else{
                                                        echo $row['taskDetail'];
                                                    }

                                                    ?>
                                                </td>
                                                <td><?php echo $row['templateId']; ?></td>
                                                <td><?php echo $row['taskDeadline']; ?></td>

                                                <td>
                                                    <?php  if ($row['hasDeliverable'] == '1'){ ?>
                                                        <a href="<?php echo $_SERVER['PHP_SELF'].'?upload='.$row['taskId']?>" class="btn btn-default btn-sm"><i class="fa fa-upload"></i> Upload</a>
                                                        <?php
                                                    }else{
                                                        echo '-- --';
                                                    } ?>
                                                </td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>

                                </table>

                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">

                            </div>

                        </div>
                        <!-- /.box -->

                    <?php
                    }else{ ?>
                        <div class="col-md-12">
                            <div class="callout callout-info">
                                <h4>Can not show details</h4>

                                <p>You are not part of any group.Please form a group and try again</p>
                            </div>
                        </div>
                    <?php
                    }?>






                </div>

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

<script src="plugins/bootstrap-filestyle-1.2.1/bootstrap-filestyle.min.js"></script>
<script type="text/javascript">
    $('#myModal').modal('show');
    
    $(":file").filestyle({

        size:   sm
    });
</script>
</body>
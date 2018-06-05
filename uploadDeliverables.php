<?php
$title="FYPMS";
$subtitle="Upload Deliverables";
require_once("includes/header.php");
require_once("includes/config.php");
require_once("includes/functions.php");
session_start();
if(!isset($_SESSION["usrCMS"]))
{
    header('Location: '.'index.php');
}
$check = true; //
$groupId = $_SESSION['GroupID'];

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $configId = filter_input(INPUT_GET,'upload',FILTER_SANITIZE_NUMBER_INT);

    //Check if deliverable is already uploaded by this grou
    $sql = "SELECT id,uploaded_by,upload_dtm FROM group_deliverables WHERE group_id='$groupId' AND config_id='$configId' ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $uploadDtm = $row['upload_dtm'];
            $uploadDtm = time2str($uploadDtm);
            $studentId = $row['uploaded_by'];
            $uploadedBy =  $conn->query("SELECT studentName FROM student WHERE studentId = '$studentId' ")->fetch_object()->studentName;
            $check = false;
        }
    }


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {



    $groupName = 'Group '.$groupId;

    $configId = filter_input(INPUT_POST,'configId',FILTER_SANITIZE_NUMBER_INT);

    //Getting Batch Data
    $batchId = $_SESSION["BatchID"];
    $batchName = $conn->query("SELECT batchName FROM batch WHERE batch.batchId = '$batchId' ")->fetch_object()->batchName;


    //Deliverable upload
    if (isset($_FILES['deliverable'])){


        $file=$_FILES['deliverable'];

        //File properties
        $file_name  =   $file['name'];
        $file_tmp   =   $file['tmp_name'];
        $file_size  =   $file['size'];
        $file_error =   $file['error'];

        //Work out file extension
        $file_ext   =   explode('.',$file_name);
        $file_ext   = strtolower(end($file_ext));

        $allowed    = array('jpg','jpeg','pdf','doc','docx','zip','7zip','rar');

        if(in_array($file_ext,$allowed)){
            if($file_error === 0){
                if($file_size <= 52428800){ //50Mib
                    $file_name_new  = 'group_'.$groupId.'_deliverable_'.$configId.'.'.$file_ext;

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

                    $sql = "INSERT INTO group_deliverables (group_id, config_id, deliverable)VALUES ('$groupId', '$configId', '$file_name_new')";

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




                    <?php if (isset($_GET['upload']) AND is_numeric($_GET['upload']) AND strlen($_GET['upload']) > 0 AND $check == true){ ?>


                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title ">Upload Task: <?php
                                    $uploadId =filter_input(INPUT_GET,'upload',FILTER_SANITIZE_NUMBER_INT); //$_GET['upload'];
                                    $taskName = $conn->query("SELECT taskName FROM configurations WHERE configurationId = '$uploadId' ")->fetch_object()->taskName;
                                    echo $taskName;
                                    ?>
                                </h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body ">
                                <div class="form-group">
                                    <form action=""  method="post" enctype="multipart/form-data" data-toggle="validator">
                                        <div class="col-sm-10">
                                            <input type="file" name="deliverable" class="filestyle " data-size="sm" />
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="submit" value="Upload" class="btn btn-primary btn-sm  "/>
                                        </div>
                                        <!--HIDDEN INPUT-->
                                        <input type="hidden" name="configId" value="<?php echo $_GET['upload']?>">
                                    </form>
                                </div>
                                <br/>
                                <p class="text-muted">Max File size: 50MiB &nbsp; Allowed file types (PDF,DOCX,RAR,ZIP,JPG) </p>


                            </div>
                            <!-- /.box-body -->
                            

                        </div>
                        <!-- /.box -->

                    <?php
                    }else if ($check == false){ ?>

                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title ">Upload Task: <?php
                                    $uploadId =filter_input(INPUT_GET,'upload',FILTER_SANITIZE_NUMBER_INT); //$_GET['upload'];
                                    $taskName = $conn->query("SELECT taskName FROM configurations WHERE configurationId = '$uploadId' ")->fetch_object()->taskName;
                                    echo $taskName;
                                    ?>
                                </h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body ">

                                <br/>
                                <p class="">Deliverable is already uploaded by  <a href="studentProfile.php?id=14"><?php echo $uploadedBy;?></a> </p>
                                <p class="text-muted"><i class="fa fa-clock-o"></i><?php echo " ".$uploadDtm;?></p>


                            </div>
                            <!-- /.box-body -->

                        </div>
                        <!-- /.box -->


                    <?php
                    }?>

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title ">SDP - Part 1</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                            <table id="manageStudents" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>WeeK</th>
                                    <th>Task</th>
                                    <th>Deadline</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <?php
                                $sql = "SELECT * from configurations WHERE projectPart='1' AND status='open' ORDER BY week ASC ";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['week'] ;?></td>
                                        <td><?php echo $row['taskName'];?></td>
                                        <td><?php if(!is_null($row['deadline'])){echo time2str($row['deadline']);}else{echo '---';} ;?></td>
                                        <td> <?php if ($row['deliverable'] == 1){ ?>
                                                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?upload=' . $row['configurationId']; ?>"  class="btn  btn-default btn-sm">Upload</a>
                                                <?php
                                            }?></td>
                                    </tr>
                                <?php }
                                ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-header">
                            <h3 class="box-title ">SDP - Part 2</h3>
                        </div>
                        <div class="box-body ">
                            <table id="manageStudents" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Task</th>
                                    <th>Deadline</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <?php
                                $sql = "SELECT * from configurations WHERE projectPart='2' AND status='open' ORDER BY week ASC ";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['week'] ;?></td>
                                        <td><?php echo $row['taskName'];?></td>
                                        <td><?php echo $row['deadline'] ;?></td>

                                        <td> <?php if ($row['deliverable'] == 1){ ?>
                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?upload=' . $row['configurationId']; ?>"  class="btn  btn-default btn-sm">Upload</a>
                                                <?php
                                            }?></td>
                                    </tr>
                                <?php }
                                ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

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
<script>
    $(":file").filestyle({

        size:   sm
    });
</script>
</body>
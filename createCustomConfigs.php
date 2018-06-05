<?php
$title = "FYPMS";
$subtitle = "Custom Configurations";
require_once("includes/header.php");
require_once("includes/config.php");
require_once("includes/functions.php");
session_start();
if (!isset($_SESSION["isCord"])) {
    header('Location: ' . 'index.php');
}

//Check if for is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    //Function for delete configuration
    if (isset($_GET["delete"]) and is_numeric($_GET["delete"]) ){

        $id = $_GET["delete"];

        //Check if there is a student
        $sql = "SELECT configurationId FROM configurations WHERE configurationId = $id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            //Check for attachment
            $sql = "SELECT attachment FROM configurations WHERE configurationId = $id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $file = "public/attachments/".$row["attachment"];
                    if (file_exists($file)){
                        unlink($file);
                    }
                }
            }
            $sql = "DELETE FROM configurations WHERE configurationId='$id' ";

            if ($conn->query($sql) === TRUE) {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
            } else {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
            }
        }else{
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
        }
    }

    //Provide values for Placeholder in editConfigs
    if (isset($_GET['edit'])) {
        if (is_numeric($_GET['edit'])) {
            $configId = $_GET['edit'];
            //Check if config id exists
            $sql_check = "SELECT configurationId from configurations WHERE configurationId = '$configId' ";
            $result = $conn->query($sql_check);
            if ($result->num_rows > 0) {

                //Get Values from Database
                $sql = "SELECT * FROM configurations WHERE configurationId = '$configId' LIMIT 1 ";

                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $week = $row['week'];
                    $taskName = $row['taskName'];
                    $taskDetails = $row['taskDetails'];
                    $deadline = $row['deadline'];
                }
            } else {
                $_GET['edit'] = '';
            }
        }

    }


}


//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Function for edit Configuration
    if (isset($_POST["btnEditConfig"])) {
        //Validations
        if ((isset($_POST['week'])) and (is_numeric($_POST['week'])) and (isset($_POST['taskName']))) {

            //Get Values from POST
            $week = $_POST ['week'];
            $taskName = $_POST['taskName'];
            $taskDetails = $_POST['taskDetails'];
            $sdpPart = $_POST['sdpPart'];
            $deadline = $_POST['deadline'];

            $configId = $_GET['edit'];

            if (isset($configId)) {
                $sql = "UPDATE configurations SET week = '$week' , taskName = '$taskName', taskDetails = '$taskDetails', projectPart='$sdpPart' ,deadline = '$deadline' WHERE configurationId = '$configId'";
                if ($conn->query($sql) === TRUE) {
                    header("Location: createCustomConfigs.php?status=t");
                } else {
                    header("Location: createCustomConfigs.php?status=f");
                }

            } else {
                echo 'Config id not set' ;exit;
            }

        } else {
            echo 'Input Validation Failed';
            exit;
        }

    }

    //Function for add configurations
    if (isset($_POST["btnAddConfig"])){

        /* Validations
         * Required:Week,sdpPart,taskName
         * week is numeric
         * */
        if ((isset($_POST['week'])) and (is_numeric($_POST['week'])) and (isset($_POST['sdpPart'])) and (isset($_POST['taskName']))  ) {

            //Get Values from POST
            $week = $_POST ['week'];
            $sdpPart = $_POST['sdpPart'];
            $task_name = $_POST['taskName'];
            $task_details = $_POST['taskDetails'];
            $deadline = $_POST['deadline'];

            $type = 'default';

            //Check if file is attached
            if(!isset($_FILES['attachment']) || $_FILES['attachment']['error'] == UPLOAD_ERR_NO_FILE) {

                //No file attached
                $sql = "INSERT INTO configurations (week, taskName, taskDetails, projectPart, deadline, configurationType)
                VALUES ('$week', '$task_name', '$task_details', '$sdpPart', '$deadline', '$type')";
                if ($conn->query($sql) === TRUE) {
                    header('Location:' . 'createCustomConfigs.php?status=t');
                } else {
                    header('Location:' . 'createCustomConfigs.php?status=f');
                }


            } else {
                //File is attached

                $file = $_FILES['attachment'];

                //File properties
                $file_name = $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];
                $file_error = $file['error'];

                //Work out file extension
                $file_ext = explode('.', $file_name);
                $file_ext = strtolower(end($file_ext));

                //Allowed file extension
                $allowed = array('jpg', 'jpeg', 'png', 'doc', 'docx', 'pdf', 'rar', 'zip');

                if (in_array($file_ext, $allowed)) {
                    if ($file_error === 0) {
                        if ($file_size <= 10485760) { //Max file size 10 mb
                            $file_name_new = $file_name;
                            $file_destination = 'public/attachments/' . $file_name_new;
                        } else {
                            $error_msg = 'The attachment file is greater than 10MiB';
                        }
                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            $success_msg = 'File Uploaded Successfully';
                            $attachment = $file_name_new;

                            $sql_attachment = "INSERT INTO configurations (week, taskName, taskDetails, projectPart, deadline, attachment, configurationType)
                                                VALUES ('$week', '$task_name', '$task_details', '$sdpPart', '$deadline', '$attachment', '$type') ";
                            if ($conn->query($sql_attachment) === TRUE) {
                                header('Location:' . 'createCustomConfigs.php?status=t');
                            } else {
                                header('Location:' . 'createCustomConfigs.php?status=f');
                            }

                        } else {
                            header('Location:' . 'createCustomConfigs.php?status=f');
                            $error_msg = 'Error! File not uploaded';
                        }
                    }
                } else {
                    header('Location:' . 'createCustomConfigs.php?status=f');
                    $error_msg = 'File not uploaded; Unsupported Format';
                }

            }
        }
        else{
            echo "Something Went Wrong";exit;
        }
    }

    /*
    //Function for delete Configuration
    if (isset($_POST["btnDelete"]) ){
        $configId = $_POST["deleteId"];

        //Check if there is attachment

        $sql = "SELECT attachment FROM configurations WHERE configurationId = $configId";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $file = "public/attachments/".$row["attachment"];
                if (file_exists($file)){
                        unlink($file);
                    }
                }
        }
        $sql = "DELETE FROM configurations WHERE configurationId='$configId' ";

        if ($conn->query($sql) === TRUE) {
            unset($_POST["deleteId"]);
            header('Location:' . 'createCustomConfigs.php?status=t');
        } else {
            header('Location:' . 'createCustomConfigs.php?status=t');
        }


    }
    */

    //Function for edit Upload
    if (isset($_POST["btnEditUpload"])){
        //Validations
        $configId = $_POST['uploadId'];


        //Check if file is attached
        if(!isset($_FILES['attachment']) || $_FILES['attachment']['error'] == UPLOAD_ERR_NO_FILE) {

            //No file attached
            header('Location:' . 'createCustomConfigs.php?status=f');

        } else {
            //File is attached

            $file = $_FILES['attachment'];

            //File properties
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];

            //Work out file extension
            $file_ext = explode('.', $file_name);
            $file_ext = strtolower(end($file_ext));

            //Allowed file extension
            $allowed = array('jpg', 'jpeg', 'png', 'doc', 'docx', 'pdf', 'rar', 'zip');

            if (in_array($file_ext, $allowed)) {
                if ($file_error === 0) {
                    if ($file_size <= 10485760) { //Max file size 10 mb
                        $file_name_new = $file_name;
                        $file_destination = 'public/attachments/' . $file_name_new;
                    } else {
                        $error_msg = 'The attachment file is greater than 10MiB';
                    }
                    if (move_uploaded_file($file_tmp, $file_destination)) {
                        $success_msg = 'File Uploaded Successfully';
                        $attachment = $file_name_new;


                        $sql = "UPDATE configurations SET attachment='$attachment' ";

                        if ($conn->query($sql) === TRUE) {
                            header('Location:' . 'createCustomConfigs.php?status=t');
                        } else {
                            header('Location:' . 'createCustomConfigs.php?status=f');
                        }

                    } else {
                        header('Location:' . 'createCustomConfigs.php?status=f');
                        $error_msg = 'Error! File not uploaded';
                    }
                }
            } else {
                header('Location:' . 'createCustomConfigs.php?status=f');
                $error_msg = 'File not uploaded; Unsupported Format';
            }

        }

    }
}


?>
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

<!-- Sweet Alert -->
<link rel="stylesheet" href="plugins/sweet-alert/sweetalert.css">

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper">
        <?php require_once("includes/content-header.php"); ?>
        <section class="content" style="min-height: 700px">
            <div class="row">


                <?php
                if (isset($_GET['edit']) && is_numeric($_GET['edit'])){ ?>
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <!--Code for edit a Configuration starts here-->
                    <!-- general form elements -->
                    <div class="box box-primary no-border">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Configuration</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" id="editConfig" method="POST">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="week">Week #</label>
                                    <input type="text" class="form-control" name="week"
                                           value="<?php echo $week; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="sdpPart">SDP Part</label>
                                    <select name="sdpPart" class="form-control">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="taskName">Task Name</label>
                                    <input type="text" class="form-control" name="taskName"
                                           value="<?php echo $taskName; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="taskDetails">Task Details</label>
                                    <div class="box-body pad">
                                        <form>
                                            <textarea class="textarea" name="taskDetails"  placeholder="<?php echo $taskDetails;?>"  style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                        </form>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="taskName">Deadline</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="deadline" class="form-control"
                                               value="<?php echo $deadline; ?>"
                                               data-inputmask="'alias': 'yyyy/mm/dd'" data-mask>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger">Back </a>
                                <button type="submit" name="btnEditConfig" form="editConfig" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box -->
                    <div class="col-md-2"></div>

                    <!-- If add button is pressed -->
                    <?php } else if (isset($_GET['add'])) { ?>


                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <!--Code for edit a Configuration starts here-->
                        <!-- general form elements -->
                        <div class="box box-primary no-border">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add new configuration</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="" id="addNewConfig" method="POST" enctype="multipart/form-data">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="sdpPart">SDP Part</label>
                                        <select name="sdpPart" class="form-control">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="week">Week</label>
                                        <input type="text" class="form-control" name="week" placeholder="Week" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="taskName">Task Name</label>
                                        <input type="text" class="form-control" name="taskName" placeholder="Task Name"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="taskDetails">Task Details</label>
                                        <div class="box-body pad">
                                            <form>
                                                <textarea class="textarea" name="taskDetails" placeholder="Place some text here"  style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="taskName">Deadline</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="deadline"  class="form-control" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="taskName">Attachment</label>
                                        <div class="input-group">
                                            <input type="file" name="attachment" >
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger">Back </a>
                                    <button type="submit" name="btnAddConfig" form="addNewConfig" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.box -->
                        <div class="col-md-2"></div>

                        <!--If UPLOAD button is pressed-->
                        <?php } else if (isset($_GET['upload']) && is_numeric($_GET['upload'])) { ?>

                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <!--Code for edit a Configuration starts here-->
                            <!-- general form elements -->
                            <div class="box box-primary no-border">



                                <div class="box-header with-border">
                                    <h3 class="box-title">Upload Attachment</h3>
                                </div>
                                <!-- /.box-header -->
                                <!-- form start -->
                                <form role="form" action="" id="editUploadForm" method="POST" enctype="multipart/form-data">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="taskName">Attachment</label>
                                            <div class="input-group">
                                                <input type="file" name="attachment" >
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger">Back </a>
                                        <input type="hidden" name="uploadId" id="uploadId" value="<?php echo $_GET['upload'];?>">
                                        <button type="submit" name="btnEditUpload" form="editUploadForm" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.box -->
                            <div class="col-md-2"></div>


                        <?php } else { ?>
                            <div class="col-md-12 ">

                                <?php if (isset ($_GET['status'])){
                                    if ($_GET['status'] == 't'){ ?>
                                        <div style="text-align:center;" class="alert alert-success" role="alert">
                                            <span class="glyphicon glyphicon-exclamation-sign"></span>
                                            Changes saved successfully!
                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                        </div>
                                 <?php   }
                                    else if ($_GET['status'] = 'f'){ ?>
                                        <div style="text-align:center;" class="alert alert-danger" role="alert">
                                            <span class="glyphicon glyphicon-exclamation-sign"></span>
                                            Error! Something Went Wrong
                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                        </div>
                                <?php }

                                    else{ ?>
                                        <div style="text-align:center;" class="alert alert-danger" role="alert">
                                            <span class="glyphicon glyphicon-exclamation-sign"></span>
                                            Error! Something Went Wrong
                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                        </div>
                                    <?php    }
                                }?>



                                <div class="box">
                                    <div class="box-header">
                                        <a onclick="goBack()" ><i class="fa fa-arrow-left"></i></a>
                                        <h4 class="text-center ">Custom Configurations</h4>
                                    </div>
                                    <div class="box-header">
                                        <h3 class="box-title">SDP - Part I</h3>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body  no-padding">
                                        <table id="sdppart1" class="table  table-striped table-responsive table-condensed ">
                                            <thead>
                                            <tr>
                                                <th>Week</th>
                                                <th>Task</th>
                                                <th>Description</th>
                                                <th>Deadline</th>
                                                <th>Attachment</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <?php
                                            $sql = "SELECT * FROM configurations WHERE configurationType='default' AND projectPart='1' ORDER BY week";
                                            $result = $conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $row["week"]; ?></td>
                                                        <td><?php echo $row["taskName"]; ?></td>
                                                        <td><?php echo getExcerpt($row["taskDetails"],0,80); ?></td>
                                                        <td><?php echo $row["deadline"]; ?></td>
                                                        <td><?php echo $row["attachment"]; ?></td>
                                                        <td>
                                                            <div class="form-group">
                                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?upload=' . $row['configurationId']; ?>" class="btn  btn-default btn-xs ">Upload</a>
                                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?edit=' . $row['configurationId']; ?>" class="btn  btn-primary btn-xs ">Edit</a>
                                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?delete=' . $row['configurationId']; ?>" onclick="return confirm('Are you sure?')"  class="btn  btn-danger btn-xs ">Delete</a>
<!--                                                                <form action="--><?php //echo $_SERVER['PHP_SELF'];?><!--" method="post"  id="deleteForm" onsubmit="return confirm('Do you really want to delete this configuration?');">-->
<!--                                                                    <input type="hidden" name="deleteId" value="--><?php //echo $row['configurationId']?><!--">-->
<!--                                                                    <button type="submit" name="btnDelete" class="btn btn-danger btn-xs "  form="deleteForm" value="Delete">Delete</button>-->
<!--                                                                </form>-->
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                        </table>
                                        <div class="box-header">
                                            <h3 class="box-title">SDP - Part II</h3>
                                        </div>
                                        <table id="sdppart2" class="table  table-striped table-responsive">
                                            <thead>
                                            <tr>
                                                <th>Week</th>
                                                <th>Task</th>
                                                <th>Description</th>
                                                <th>Deadline</th>
                                                <th>Attachment</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <?php
                                            $sql = "SELECT * FROM configurations WHERE configurationType='default' AND projectPart='2' ORDER BY week";
                                            $result = $conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $row["week"]; ?></td>
                                                        <td><?php echo $row["taskName"]; ?></td>
                                                        <td><?php echo $row["taskDetails"]; ?></td>
                                                        <td><?php echo $row["deadline"]; ?></td>
                                                        <td><?php echo $row["attachment"]; ?></td>
                                                        <td>
                                                            <div class="form-group">
                                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?upload=' . $row['configurationId']; ?>" class="btn  btn-default btn-xs ">Upload</a>
                                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?edit=' . $row['configurationId']; ?>" class="btn  btn-primary btn-xs ">Edit</a>
                                                                <a href="<?php echo $_SERVER['PHP_SELF'] . '?delete=' . $row['configurationId']; ?>" class="btn  btn-danger btn-xs ">Delete</a>
<!--                                                                <form action="--><?php //echo $_SERVER['PHP_SELF'];?><!--" method="post"  id="deleteForm" onsubmit="return confirm('Do you really want to submit the form?');">-->
<!--                                                                    <input type="hidden" name="deleteId" id="deleteId" value="--><?php //echo $row['configurationId']?><!--">-->
<!--                                                                    <button type="submit" name="btnDelete" class="btn btn-danger btn-xs " form="deleteForm" value="Delete">Delete</button>-->
<!--                                                                </form>-->
                                                            </div>

                                                        </td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                        </table>
                                        <div class="box-footer  pull-right">
                                            <a href="<?php echo $_SERVER['PHP_SELF'] . '?add'; ?>"
                                               class="btn  btn-primary btn-sm ">Add New Task</a>
                                            <a href="" class="btn  btn-danger btn-sm ">Multiple Delete</a>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        <?php } ?>


        </section>
    </div>
<?php
require_once("includes/main-footer.php");
?>
</div>
<?php
require_once("includes/required_js.php");
?>
<!-- Select2 -->
<script src="plugins/select2/select2.full.min.js"></script>
<!-- InputMask -->
<script src="plugins/input-mask/jquery.inputmask.js"></script>
<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Sweet-Alert -->
<script src="plugins/sweet-alert/sweetalert.min.js"></script>
<!-- Page script -->
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
        //Datemask dd/mm/yyyy
        $("#datemask").inputmask("yyyy/mm/dd", {"placeholder": "yyyy/mm/dd"});
        $("[data-mask]").inputmask();

        $('.textarea').wysihtml5();

    });
</script>
<script>
    function goBack() {
        window.history.back();
    }
</script>

</body>
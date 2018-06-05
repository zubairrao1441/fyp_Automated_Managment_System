<?php 
$title="FYPMS";
$subtitle="Create batch";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if coordinator is logged in
if(!isset($_SESSION["isCord"]))
{
        header('Location: '.'index.php');
}

//Check if for is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['btnCreateBatch'])){
        //Validations
        if(($_POST['batch']!="") && ($_POST['year']!="") && ($_POST['startingDate']!=""))
        {

            //Getting Data from POST and sanitizing
            $batch = filter_input(INPUT_POST,'batch',FILTER_SANITIZE_SPECIAL_CHARS);
            $batchYear = filter_input(INPUT_POST,'year',FILTER_SANITIZE_SPECIAL_CHARS);

            $batchName = $batch ." ". $batchYear;


            //$startingDate = $_POST['startingDate'];
            $startingDate = date('Y-m-d', strtotime($_POST['startingDate']));

            //Other values
            $isActive = 1;
            $sdpPart = 1;

            //Check if BATCH already exists
            $sql = "SELECT batchId FROM batch WHERE batchName ='$batchName' LIMIT 1";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                //Batch Already Exist
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=a');
            }else{

                // prepare and bind
                $stmt = $conn->prepare("INSERT INTO batch (batchName, startingDate, isActive, sdpPart) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssii", $batchName, $startingDate, $isActive, $sdpPart);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {

                    //Ad to batch settings
                    $last_id = $stmt->insert_id;
                    $sql = "INSERT INTO batch_settings (batchId) VALUES ('$last_id')";

                    if ($conn->query($sql) === TRUE) {

                        //MAKE a folder with BATCH name
                        if (!file_exists('uploads/'.$batchName)) {
                            mkdir('uploads/'.$batchName, 0777, true);

                            // Commit transaction
                            $stmt->close();
                            $conn->close();
                            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                        }
                    }


                }
                else{
                    //SQL Error
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
                    printf("Error: %s.\n", $stmt->error);exit;
                }
            }
        }
    }

}


?>
<!--Date Picker-->
<link rel="stylesheet" href="plugins/datepicker/datepicker3.css"/>
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
                    <p><span class="glyphicon glyphicon-exclamation-sign"></span> Batch Created successfully!</p>
                    <a href="./registerStudents.php"> <i class="fa fa-chevron-right" aria-hidden="true"></i> Register Students</a>
                    <br/>
                    <a href="./batchTasks.php"><i class="fa fa-chevron-right" aria-hidden="true"></i> Add Batch Tasks</a>
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
            else if ($_GET['status'] == 'a'){ ?>
                <div style="text-align:center;" class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    Error! Batch Already Exist
                    <button type="button" class="close" data-dismiss="alert">x</button>
                </div>
                <?php
            }
            else if ($_GET['add'] == 'e'){ ?>
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
                <h3 class="box-title">Create new batch</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form id="createBatch" action="" method="post" data-toggle="validator">
                    <div class="form-group has-feedback">
                        <b>Batch</b>
                        <select name="batch" class="form-control" required>
                            <option value="">-Select Batch-</option>
                            <option value="Spring">Spring</option>
                            <option value="Fall">Fall</option>
                            <span class="glyphicon glyphicon-education form-control-feedback"></span>
                        </select>
                    </div>

                    <div class="form-group has-feedback">
                        <b>Year</b>
                        <select name="year" class="form-control" required>
                            <option selected value="<?php echo date('Y')-1;?>" selected><?php echo date('Y')-1;?></option>
                            <option value="<?php echo date('Y');?>" selected><?php echo date('Y');?></option>
                        </select>
                    </div>


                    <div class="form-group has-feedback">
                        <b>Starting Date Of semester</b>
                        <div class="input-group date" data-provide="datepicker" >
                            <input type="text" name="startingDate"  id="startingDate"  class="form-control" required>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>





                </form>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
                <a href="<?php echo siteroot; ?>" class="btn  btn-default btn-sm  "><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                <button type="submit" form="createBatch" name="btnCreateBatch" name="Createbatch" class="btn btn-primary btn-sm pull-right">Create Batch</button>
            </div>

        </div>
        <!-- /.box -->



       <div class="col-md-2"></div>
    </div>
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
<!--Datepicker-->
<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',

    });
</script>
</body>
</html>
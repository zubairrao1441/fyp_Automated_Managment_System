<?php
$title = "FYPMS";
$subtitle = "Set Project Name";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if student is logged in and formed a group
if (!isset($_SESSION["usrCMS"]) ) {
    header('Location: ' . 'index.php');
}

$studentId = $_SESSION['usrId'];

//Getting group id
$sql = "SELECT * FROM student WHERE student.studentId = '$studentId' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $groupId = $row['groupId'];
    }
}else{
    $groupId = $_SESSION["GroupID"];
}

//Getting Project name
if (!is_null($groupId)){
    $sql = "SELECT * FROM student_group WHERE groupId = '$groupId' LIMIT 1 ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $projectName = $row['projectName'];
        }
    }else{
        $projectName = '--';

    }
}





//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (isset($_POST['btnChangeName'])) {
        if ($_POST['projectName'] != "") {
            
            $projectName = filter_input(INPUT_POST, "projectName", FILTER_SANITIZE_SPECIAL_CHARS);
            
                 $sql = "UPDATE student_group SET projectName='$projectName' WHERE groupId='$groupId'  ";

                if ($conn->query($sql) === TRUE) {
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
                } else {
                     header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
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
        <div class="content-wrapper">
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

                        <?php if (is_null($groupId)){ ?>
                            <div class="col-md-12">
                                <div class="callout callout-info">
                                    <h4>Can not show details</h4>

                                    <p>You are not part of any group.Please form a group and try again</p>
                                </div>
                            </div>
                        <?php
                        } else{ ?>
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-pencil-square-o"></i>

                                <h3 class="box-title">Set Project Name</h3>
                            </div>
                            <!-- /.box-header -->
                            <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="changeProjName" method="POST" data-toggle="validator">
                                <div class="box-body">


                                    <h4>Project Name: <?php echo $projectName; ?></h4><br/>
                                    <div class="form-group">
                                        <!--<label for="projectName" class="col-sm-2 control-label">Project Name</label>-->
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="projectName" id="projectName" placeholder="Set Project name or Update existing one" required>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- /.box-body -->
                            <div class="box-footer">
                                <a href="<?php echo siteroot; ?>" class="btn btn-default">Back </a>
                                <!--<button type="submit" class="btn btn-danger">Back</button>-->
                                <button type="submit" name="btnChangeName" form="changeProjName" class="btn btn-primary pull-right">Change Name</button>
                            </div>
                            <!-- /.box-footer -->

                        </div>
                        <!-- /.box -->
                    </div>
                        <?php
                        } ?>


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
</html>
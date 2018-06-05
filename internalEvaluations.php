<?php
$title="FYPMS";
$subtitle="Internal Demo Evaluations";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if Faculty is logged in
if(!isset($_SESSION["facultyId"]))
{
    header('Location: '.'index.php');
}
$facultyId = $_SESSION['facultyId'];



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_POST['internal_group'])){

    }


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (isset($_POST['btnInternalEval'])){
        //Validations
        if (isset($_POST['ae_vote']) && isset($_POST['oh_vote'])){
            //Getting value from POST and sanitizing
            $groupId = filter_input(INPUT_POST,'groupId',FILTER_SANITIZE_NUMBER_INT);
            $ae_vote = filter_input(INPUT_POST,'ae_vote',FILTER_SANITIZE_NUMBER_INT);
            $oh_vote = filter_input(INPUT_POST,'oh_vote',FILTER_SANITIZE_NUMBER_INT);
            $voted_by = $facultyId;

            // prepare and bind
            $stmt = $conn->prepare("INSERT INTO internal_evaluations (groupId, aeVote, ohVote, votedBy) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $groupId, $ae_vote, $oh_vote,$voted_by );

            // set parameters and execute

            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                $conn->close();
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
            }





        }
        else{
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=validation_err');
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
                        else if ($_GET['status'] == 'validation_err'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! Please fill all the required fields correctly
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
                            <h3 class="box-title">Internal Demo Evaluations</h3>
                            <?php
                            if(isset($_SESSION["isCord"])){ ?>
                                <div class="box-tools">
                                    <a href="./internalEvaluationsReport.php" target="_blank" class="btn btn-default btn-xs "><i class="fa fa-external-link" aria-hidden="true"></i> Show Report</a>
                                </div>
                            <?php
                            }
                            ?>

                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">



                            <?php
                            if (isset($_GET['internal_group']) && is_numeric($_GET['internal_group']) && strlen($_GET['internal_group']) > 0){
                                $groupId = filter_input(INPUT_GET,'internal_group',FILTER_SANITIZE_NUMBER_INT);
                                $eval_check = true;

                                //Check if this group is already Voted by this faculty
                                $sql = "SELECT * FROM internal_evaluations WHERE groupId = '$groupId' AND votedBy = '$facultyId' LIMIT 1 ";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    $eval_check = false;

                                } else {
                                    $eval_check = true;
                                }

                                $sql = "SELECT * FROM faculty_student_group JOIN student_group ON faculty_student_group.groupId = student_group.groupId JOIN faculty ON faculty_student_group.facultyId = faculty.facultyId WHERE student_group.groupId = '$groupId' LIMIT 1";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        $supervisorId = $row['facultyId'];
                                        $supervisorName = $row['facultyName'];
                                        $projectName = $row['projectName'];
                                        $groupId = $row['groupId'];
                                    }

                                }else{
                                    $eval_check = false;
                                }

                                if ($eval_check != false){ ?>
                                    <!-- form start -->
                                    <form class="form-horizontal" action="" method="post" name="internalEval" data-toggle="validator">
                                        <div class="box-body">
                                            <input type="hidden" name="groupId" value="<?php echo $groupId;?>">
                                            <strong>Project: <?php echo $projectName;?></strong>
                                            <p><?php echo 'Supervised by:'.$supervisorName;?></p>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Allow External Examination</label>

                                                <div class="col-sm-10">
                                                    <select name="ae_vote" id="ae_vote" required>
                                                        <option value="">--</option>
                                                        <option value="2" style="color:green;">Strong Accept [2] </option>
                                                        <option value="1" style="color:lightgreen;">Weak Accept [1]</option>
                                                        <option value="-1" style="color:lightpink;">Weak Reject [-1] </option>
                                                        <option value="-2" style="color:red;">Strong Reject [-2]</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label  class="col-sm-2 control-label">Allow Participation in Open House</label>

                                                <div class="col-sm-10">
                                                    <select name="oh_vote" id="oh_vote" required>
                                                        <option value="">--</option>
                                                        <option value="2" style="color:green;">Strong Accept [2] </option>
                                                        <option value="1" style="color:lightgreen;">Weak Accept [1]</option>
                                                        <option value="-1" style="color:lightpink;">Weak Reject [-1] </option>
                                                        <option value="-2" style="color:red;">Strong Reject [-2]</option>
                                                    </select>
                                                    <br><br>
                                                    <button type="submit" name="btnInternalEval" class="btn btn-primary  btn-flat">Submit</button>
                                                </div>
                                            </div>


                                        </div>
                                        <!-- /.box-body -->
                                    </form>
                                <?php
                                }else{ ?>
                                    <h4>This group has already been voted</h4>
                                <?php
                                }

                                ?>




                            <?php
                            }else{ ?>
                                <div class="form-group">
                                    <label for="chooseGroup" class="col-sm-2 control-label">Choose Group</label>
                                    <div class="col-sm-10">
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Choose a Group
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                <li><a href="<?php echo $_SERVER['PHP_SELF'];?>">---</a></li>
                                                <?php
                                                $sql = "SELECT * FROM faculty_student_group JOIN student_group ON faculty_student_group.groupId = student_group.groupId ";
                                                $result = $conn->query($sql);

                                                if ($result->num_rows > 0) {
                                                    // output data of each row
                                                    while($row = $result->fetch_assoc()) { ?>
                                                        <li><a href="<?php echo $_SERVER['PHP_SELF'].'?internal_group='.$row['groupId'];?>"><?php echo $row['projectName'];?></a></li>
                                                    <?php    }
                                                } else { ?>
                                                    <li><a href="<?php echo $_SERVER['PHP_SELF'];?>">No Groups Available</a></li>
                                                <?php   }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <br>

                            <?php
                            }
                            ?>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn btn-default">Back</a>



                        </div>

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
<!--PAGE SCRIPT-->

</body>
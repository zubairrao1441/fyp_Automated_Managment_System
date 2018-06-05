<?php
$title="FYPMS";
$subtitle="Grade Students";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if External Examiner is logged in
if(!isset($_SESSION["facultyId"]))
{
    header('Location: '.'index.php');
}
$facultyId = $_SESSION['facultyId'];

/***************************************
 * Check if Coordinator allowed grading
 ***************************************/
$sql = "SELECT * FROM batch JOIN batch_settings ON batch_settings.batchId = batch.batchId WHERE isActive = 1 AND sdpPart =1 LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $male_female_group = $row['male_female_group'];
        $sdp1_grading = $row['sdp1_grading'];
        $internal_evaluation = $row['internal_evaluation'];
        $sdp2_grading = $row['sdp2_grading'];
    }
}



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["group"]) and is_numeric($_GET["group"]) ){

        $groupId =  filter_input(INPUT_GET, "group",FILTER_SANITIZE_SPECIAL_CHARS);

        //Check if group is already graded
        $sql = "SELECT id FROM grades WHERE groupId='$groupId' AND sdpPart=1 LIMIT 1 ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $grade_check = TRUE;

        }else{
            $grade_check = FALSE;
            $projName= $conn->query("SELECT projectName FROM student_group WHERE student_group.groupId = '$groupId' ")->fetch_object()->projectName;

            //Supervisor Data
            $supervisorId = $conn->query("SELECT facultyId FROM faculty_student_group WHERE faculty_student_group.groupId = '$groupId' ")->fetch_object()->facultyId;
            if($supervisorId){
                $supervisorName = $conn->query("SELECT facultyName FROM faculty WHERE faculty.facultyId= '$supervisorId' ")->fetch_object()->facultyName;
            }

        }

    }



}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['btnGradeStudents'])){
        //VALIDATIONS
        if (isset($_POST['grade'])){

            //HIDDEN INPUTS
            $studentId = $_POST['studentId'];
            $count = $_POST['count'];
            $groupId = $_POST['groupId'];
            $sdpPart = '1';

            //FROM FORM
            $grade = $_POST['grade'];
            $comments = $_POST['comments'];

            for ($x = 0; $x < $count; $x++) {
                $sql = "INSERT INTO grades (studentId, groupId, sdpPart, comments, grade,gradedBy) VALUES ('$studentId[$x]', '$groupId', '$sdpPart' , '$comments[$x]', '$grade[$x]','$facultyId')";

                if ($conn->query($sql) === TRUE) {
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
                } else {
                    //SQL ERROR
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=fs');
                }
            }

        }
        else if ($_POST['grade[]'] == "" ){
            //GRADE NOT SELECTED
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
        }

    }

    /**************
     * Edit Grade
     ************/
    if (isset($_POST['btnEditGrade'])){

        $gradeId = filter_input(INPUT_POST,'gradeId',FILTER_SANITIZE_NUMBER_INT);
        $groupId = filter_input(INPUT_POST,'groupId',FILTER_SANITIZE_NUMBER_INT);
        $grade = filter_input(INPUT_POST,'grade',FILTER_SANITIZE_SPECIAL_CHARS);


        $sql = "UPDATE grades SET grade='$grade' WHERE id='$gradeId' ";

        if ($conn->query($sql) === TRUE) {
            header('Location:' . $_SERVER['PHP_SELF'] . '?group='.$groupId.'&status=t');die;
        } else {
            header('Location:' . $_SERVER['PHP_SELF'] . '?group='.$groupId.'&status=f');die;
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
                                Error! Something went wrong
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

                    <?php
                    if (isset($_GET['edit']) && is_numeric($_GET['edit']) && strlen($_GET['edit'])>0 ){
                        $gradeId = filter_input(INPUT_GET,'edit',FILTER_SANITIZE_NUMBER_INT);


                        $sql = "SELECT * FROM grades JOIN student ON student.studentId = grades.studentId LIMIT 1";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                        // output data of each row
                            while($row = $result->fetch_assoc()) {
                                $name = $row['studentName'];
                                $cms = $row['studentCMS'];
                                $grade = $row['grade'];
                                $groupId = $row['groupId'];
                            }
                        }
                        ?>
                        <div class="box no-border">
                            <div class="box-header with-border">
                                <h3 class="box-title">Edit Grade: <?php echo $name;?> </h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">
                                <form action="" method="post" class="form-horizontal">
                                    <input type="hidden" name="gradeId" value="<?php echo $gradeId;?>">
                                    <input type="hidden" name="groupId" value="<?php echo $groupId;?>">
                                    <label for="grade">Select Grade</label>
                                    <select class="form-control" name="grade" style="width:200px;" required>
                                        <option value="A+" <?php if ($grade=="A+"){echo 'selected';} ?> >A+</option>
                                        <option value="A" <?php if ($grade=="A"){echo 'selected';} ?> >A</option>
                                        <option value="B+" <?php if ($grade=="B+"){echo 'selected';} ?>>B+</option>
                                        <option value="B" <?php if ($grade=="B"){echo 'selected';} ?>>B</option>
                                        <option value="C+" <?php if ($grade=="C+"){echo 'selected';} ?>>C+</option>
                                        <option value="C" <?php if ($grade=="C"){echo 'selected';} ?>>C</option>
                                        <option value="D+" <?php if ($grade=="D+"){echo 'selected';} ?>>D+</option>
                                        <option value="D" <?php if ($grade=="D"){echo 'selected';} ?>>D</option>
                                        <option value="F" <?php if ($grade=="F"){echo 'selected';} ?>>F</option>
                                    </select>
                                    <br>

                                </form>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" name="btnEditGrade" class="btn btn-primary btn-sm pull-right">Submit</button>
                                <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn btn-default">Back</a>
                            </div>

                        </div>

                    <?php
                    }
                    ?>

                    <?php
                    /*****
                     * If coordinater allowed SPD-1 grading
                     */
                    if ($sdp1_grading == 1){ ?>
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Grade Supervising groups</h3>
                                <p class="text-muted">Select a group you are supervising and Grade SDP - 1</p>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body  ">
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
                                                $sql = "SELECT * FROM faculty_student_group JOIN student_group ON faculty_student_group.groupId = student_group.groupId WHERE facultyId= '$facultyId'";
                                                $result = $conn->query($sql);

                                                if ($result->num_rows > 0) {
                                                    // output data of each row
                                                    while($row = $result->fetch_assoc()) { ?>
                                                        <li><a href="<?php echo $_SERVER['PHP_SELF'].'?group='.$row['groupId'];?>"><?php echo $row['projectName'];?></a></li>
                                                    <?php    }
                                                } else { ?>
                                                    <li><a href="<?php echo $_SERVER['PHP_SELF'];?>">No Groups Available</a></li>
                                                <?php   }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!--Show Group Members-->
                                <?php
                                if (isset($_GET['group']) AND is_numeric($_GET['group']) AND $grade_check == FALSE ){ ?>

                                    <br/>
                                    <h2 class="page-header">
                                        <i class="fa fa-list-alt"></i> <?php echo $projName;?>
                                        <small class="pull-right">Supervisor: <?php if (isset($supervisorName)){echo $supervisorName;}?></small>
                                    </h2>

                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>CMS</th>
                                            <th>Name</th>
                                            <th>Set Grade</th>
                                            <th>Comments/Review</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <form role="form" action="" id="gradeStudents" name="gradeStudents" method="POST" data-toggle="validator">
                                            <?php
                                            $sql = "SELECT *  FROM student WHERE student.groupId ='$groupId' ";
                                            $result = $conn->query($sql);
                                            if ($result->num_rows > 0) {
                                            // output data of each row
                                            while($row = $result->fetch_assoc()) { ?>
                                            <!--HIDDEN INPUTS-->
                                            <input type="hidden" name="studentId[]" id="studentId[]" value="<?php echo $row['studentId'];?>">
                                            <input type="hidden" name="count" id="count" value="<?php echo $result->num_rows; ?>">
                                            <input type="hidden" name="groupId" id="groupId" value="<?php echo $groupId; ?>">
                                            <tr>
                                                <td><?php echo $row['studentCMS'];?></td>
                                                <td><?php echo $row['studentName'];?></td>
                                                <td><select class="form-control" name="grade[]" required>
                                                        <option value="">Select Grade</option>
                                                        <option value="A+">A+</option>
                                                        <option value="A">A</option>
                                                        <option value="B+">B+</option>
                                                        <option value="B">B</option>
                                                        <option value="C+">C+</option>
                                                        <option value="C">C</option>
                                                        <option value="D+">D+</option>
                                                        <option value="D">D</option>
                                                        <option value="F">F</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control" id="comments[]" name="comments[]" placeholder="Comments/Reviews if any"></td>
                                                <!--                                        <td><button type="submit" name="btnGradeStudents" form="gradeStudents" class="btn btn-default btn-sm ">Grade Student</button></div></td>-->
                                                <?php } } ?>
                                            </tr>

                                        </form>
                                        </tbody>
                                    </table>

                                    <div class="box-footer">
                                        <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn btn-default" >Cancel</a>
                                        <button type="submit" name="btnGradeStudents" form="gradeStudents" class="btn btn-primary pull-right">Grade Students</button>
                                    </div>

                                    <?php
                                }else if (isset($grade_check)) {
                                    if ($grade_check == TRUE){


                                        ?>
                                        <br/><br/>

                                        <div class="callout callout-info">
                                            <h4>Already Graded!</h4>

                                            <p>This group has already been graded.You can edit Grades until coordinator locks them</p>
                                        </div>

                                        <table class="table table-condensed">
                                            <tr>
                                                <th style="width: 20px;">CMS</th>
                                                <th>Name</th>
                                                <th sty="width: 10px;">Grade</th>
                                                <th sty="width: 10px;">Actions</th>

                                            </tr>
                                            <?php
                                            $sql = "SELECT * FROM grades JOIN student ON student.studentId = grades.studentId WHERE grades.groupId = '$groupId' AND sdpPart = 1";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
                                                // output data of each row
                                                while($row = $result->fetch_assoc()) {
                                                    $gradedBy = $row['gradedBy'];
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $row['studentCMS'];?></td>
                                                        <td><?php echo $row['studentName'];?></td>
                                                        <td><?php echo $row['grade'];?></td>
                                                        <td><a href="<?php echo $_SERVER['PHP_SELF']."?group=".$row['groupId']."&edit=".$row['id'];?>" class="btn btn-default btn-sm">Edit</a></td>
                                                    </tr>

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </table>

                                        <?php
                                    }   }

                                ?>



                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->

                    <?php
                    }

                    /*****
                     * If coordinator internal demo evaluation
                     */
                    if ($internal_evaluation == 1){ ?>

                        <!-- general form elements -->
                        <div class="box no-border">
                            <div class="box-header with-border">
                                <h3 class="box-title">Internal Demo Evaluation</h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">
                                <a href="./internalEvaluations.php" target="_blank" class="btn btn-default btn-flat btn-sm"><i class="fa fa-external-link" aria-hidden="true"></i> Goto Internal Demo Evaluation</a>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">

                            </div>

                        </div>

                    <?php
                    }

                    ?>




                  


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
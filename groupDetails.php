<?php
$title = "FYPMS";
$subtitle = "Group Details";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();
if (!isset($_SESSION["usrCMS"])) {
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

//Get Project name
if (!is_null($groupId)){

    //Getting group id and Project Name from DATABASE
    //If groupLeader
    $sql = "SELECT * FROM student_group WHERE student_group.leaderId = '$studentId' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $projectName = $row['projectName'];
        }
    }
    else{
        $projectName = $conn->query("SELECT projectName FROM student JOIN student_group ON student.groupId = student_group.groupId  WHERE student.studentId = '$studentId' LIMIT 1" )->fetch_object()->projectName;
    }

}


//Getting supervisor id and name
$sql = "SELECT facultyId FROM faculty_student_group WHERE faculty_student_group.groupId = '$groupId' LIMIT 1 ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $supervisorId =  $row["facultyId"];
    }
    $sql_name = "SELECT facultyName FROM faculty WHERE faculty.facultyId = '$supervisorId' ";
    $result = $conn->query($sql_name);
            if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
            $supervisorName =  $row["facultyName"];
            }

        }
}

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
                <?php
                if (!is_null($groupId)   ) { ?>

                    <div class="col-md-12">
                        <div class="box box-solid">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <h3>Project Name:<?php echo $projectName?></h3>
                                <!--Supervisor Name-->
                                <h4>Supervisor:<?php
                                if (isset($supervisorName)){
                                    echo $supervisorName;
                                }else{ 
                                echo ' --- ';
                            }
                                
                                ?></h4>
                            </div>
                            <!-- /.box-body -->

                            <!--GROUP MEMBERS-->
                            <div class="box-header">
                                <h3 class="box-title">Group Members</h3>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body  no-padding">
                                <table id="groupMembers" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>CMS</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                    </tr>
                                    </thead>
                                    <?php
                                    //$groupID = $_SESSION['GroupID'];
                                    $sql = "SELECT * from student WHERE student.groupId = '$groupId' ";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['studentCMS']; ?></td>
                                            <td><?php echo $row['studentName'];
                                            if ($row['isLeader'] == 1){
                                               echo '  <span class="label label-primary">Leader</span>'; 
                                            }
                                            ?></td>
                                            <td><?php echo $row['studentEmail']; ?></td>
                                            <td><?php echo $row['studentPhoneNo']; ?></td>
                                        </tr>
                                    <?php }
                                    ?>
                                </table>
                                
                                
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <a href="<?php echo siteroot;?>" class="btn btn-default btn-sm">Back</a>
                            </div>


                        </div>
                        <!-- /.box -->
                    </div>


                <?php } else if (is_null($groupId)) { ?>

                    <div class="col-md-12">
                        <div class="callout callout-info">
                            <h4>Can not show details</h4>

                            <p>You are not part of any group.Please form a group and try again</p>
                        </div>
                    </div>

                <?php
                }

                ?>


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
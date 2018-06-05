<?php

$title="FYPMS";
$subtitle="Join Group";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if student is logged in and not a group leader
if(!isset($_SESSION["usrCMS"]))
{
    header('Location:' . 'index.php?status=logged_out');
}

$check = true;

//Getting values from SESSION
$studentId = $_SESSION['usrId'];
$gender = $_SESSION["usrGender"];
$batchId = $_SESSION["BatchID"];


/****
 * Check if user is group leader OR part of group
 * OR he already sent request to user
 */
$sql = "SELECT * FROM student WHERE studentId = '$studentId' LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $isLeader = $row['isLeader'];
        $groupId = $row['groupId'];
    }
    if ($isLeader == 1 OR !is_null($groupId)){
        header('Location:' . 'index.php?status=logged_out'); //TODO : 404 Redirect
        session_destroy();
        die;
    }
}
/****
 * Now check if he already sent a request
 */
    $sql = "SELECT * FROM student_group_request WHERE studentId = '$studentId' LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $check= false;
    }




//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Send Request
    if (isset($_POST['btnSendRequest'])){
        //Validations
        if ($_POST['requestId'] != ""){

            //Getting value from POST and sanitizing it
            $requestId = filter_input(INPUT_POST,'requestId',FILTER_SANITIZE_NUMBER_INT);

            $sql = "INSERT INTO student_group_request (studentId, groupId) VALUES ('$studentId', '$requestId')";

            if ($conn->query($sql) === TRUE) {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
            } else {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
            }
        }
    }

    //Delete Request
    if (isset($_POST['btnDeleteRequest'])){
        // sql to delete a record
        $sql = "DELETE FROM student_group_request WHERE studentId='$studentId' LIMIT 1";

        if ($conn->query($sql) === TRUE) {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
        } else {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
        }
    }

}


?>

</head>

<body class="hold-transition skin-blue sidebar-mini">	
<div class="wrapper">
<?php
//****************************************************************************************************************************************************
//
//													headers and sidebars
//
//****************************************************************************************************************************************************
?>  
    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper" >
    <?php require_once("includes/content-header.php"); ?>
        
    <section class="content" style="min-height: 700px">
    <div class="row">
    <div class="col-md-1"></div>

    <div class="col-md-10">

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
            else if ($_GET['status'] == 'a'){ ?>
                <div style="text-align:center;" class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    Error!
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

        <?php if ($check == true){ ?>

            <!-- general form elements -->
            <div class="box no-border">
                <div class="box-header with-border">
                    <h3 class="box-title">List of Available Groups</h3>
                    <p class="text-muted">Choose from available groups and click on send request</p>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <table id="joinGroup" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Created by:</th>

                            <th><i class="fa fa-clock-o" aria-hidden="true"></i> Group Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <?php
                        /************************
                         * Check batch settings
                         ***********************/
                        $sql = "SELECT * FROM batch_settings WHERE batchId ='$batchId' ";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while($row = $result->fetch_assoc()) {
                                $male_female_group = $row['male_female_group'];
                            }
                        }
                        

                        if ($male_female_group == 0){
                            //Not Allowed
                            $sql = " SELECT student.studentId,student_group.createdDtm,projectName,studentCMS,studentName,student_group.groupId FROM student_group INNER JOIN student ON student.studentId = student_group.leaderId WHERE inGroup < groupLimit AND studentGender = '$gender' " ;
                        }
                        else if ($male_female_group == 1){
                            //Allowed
                            $sql = " SELECT student.studentId,student_group.createdDtm,projectName,studentCMS,studentName,student_group.groupId FROM student_group INNER JOIN student ON student.studentId = student_group.leaderId WHERE inGroup < groupLimit " ;
                        }

                        //$sql = " SELECT student.studentId,student_group.createdDtm,projectName,studentCMS,studentName,student_group.groupId FROM student_group INNER JOIN student ON student.studentId = student_group.leaderId WHERE inGroup < groupLimit AND studentGender = '$gender' " ;
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['projectName'] ;?></td>
                                <td><a target="_blank" href="<?php echo siteroot.'studentProfile.php?id='.$row['studentId'];?>" ><?php echo $row['studentName']." [".$row['studentCMS']."]";?></a></td>
                                <td><?php echo time2str($row['createdDtm']) ;?></td>
                                <td>
                                    <form  action="" method="post" onsubmit="return confirm('Are you sure you want to send request to this group?');" data-toggle="validator">
                                        <input type="hidden" name="requestId" value="<?php echo $row['groupId'];?>">
                                        <button type="submit" name="btnSendRequest" class="btn  btn-primary btn-block  btn-sm"<i class="fa fa-user-plus" aria-hidden="true"></i> Send Request</button>
                                    </form>
                                </td>
                            </tr>
                        <?php }
                        ?>
                    </table>

                </div>
                <!-- /.box-body -->

                <div class="box-footer">

                </div>

            </div>
            <!-- /.box -->


            <?php
        }else if ($check == false){ ?>
            <!-- general form elements -->
            <div class="box no-border">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info" aria-hidden="true"></i> Can not send request to group</h3>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <p>You have already sent request to a group</p>
                    <form  action="" method="post" onsubmit="return confirm('Are you sure you want to cancel your sent request?');" data-toggle="validator">
                        <button type="submit" name="btnDeleteRequest" class="btn  btn-default  "><i class="fa fa-times" aria-hidden="true"></i> Cancel Request</button>
                    </form>

                </div>
                <!-- /.box-body -->

                <div class="box-footer">

                </div>

            </div>
            <!-- /.box -->
        <?php
        }?>











    </div>
    <div class="col-md-1"></div>

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
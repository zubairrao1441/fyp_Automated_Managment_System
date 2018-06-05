<?php 
$title="FYPMS";
$subtitle="Choose Supervisor";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if student is logged in
if(!isset($_SESSION["usrCMS"]))
{
    header('Location: '.'index.php');
}
//Check if request is sent to a supervisor already
$studentId = $_SESSION["usrId"];

$sql = "SELECT * FROM student WHERE studentId = '$studentId' LIMIT 1 ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $batchId = $row['batchId'];
        $groupId = $row['groupId'];
        $isLeader = $row['isLeader'];
    }
}
//If leader
if ($isLeader != 1 OR is_null($groupId)){
    header('Location: '.'index.php');
}

$sql_check = "SELECT requestId FROM faculty_student_request WHERE groupId = '$groupId ' LIMIT 1";
$result = $conn->query($sql_check);
 if ($result->num_rows > 0) {
     $request_sent = true; //User has already sent request to a supervisor
    while($row = $result->fetch_assoc()) {
        $requestId = $row['requestId'];
    }
     
 }
 else{
    //Check if group has a supervisor already
    $sql_check = "SELECT facultyStudentId FROM faculty_student_group WHERE groupId = '$groupId ' LIMIT 1 ";
    $result = $conn->query($sql_check);
     if ($result->num_rows > 0) {
         $request_sent = true; //User has already a supervisor

     }else{
        $request_sent = false;
     }
 }

//If form is submitted using POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Function to send supervisor a request
    if (isset($_POST['btnChooseSupervisor'])){
        //Getting value from POST and sanitizing
        $facultyId = filter_input(INPUT_POST,'facultyId');

        //Check if request is already sent
        $check = $conn->query("SELECT facultyId FROM faculty_student_request WHERE studentId = '$studentId' LIMIT 1 ");
        if ($check->num_rows == 0){

            $sql = "INSERT INTO faculty_student_request (facultyId, groupId) VALUES ('$facultyId', '$groupId')";

            if ($conn->query($sql) === TRUE) {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
            } else {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
            }
        }
    }

    //Function to delete Request
    if (isset($_POST['btnDeleteReq'])){
        //Getting values from POST and Sanitizing

        $requestId = filter_input(INPUT_POST,'deleteId',FILTER_SANITIZE_NUMBER_INT);
        // sql to delete a record
        $sql = "DELETE FROM faculty_student_request WHERE requestId= '$requestId' ";

        if ($conn->query($sql) === TRUE) {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=req_del_t');die;
        } else {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=req_del_f');die;
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
    <div class="col-md-1"></div>
    <div class="col-md-10">

        <?php
        if (isset($_GET['status'])){
            if ($_GET['status'] == 't'){ ?>
                <div style="text-align:center;" class="alert alert-success" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    Request sent to supervisor successfully!
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
            else if ($_GET['status'] == 'req_del_t'){ ?>
                <div style="text-align:center;" class="alert alert-success" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    Request Deleted Successfully!
                    <button type="button" class="close" data-dismiss="alert">x</button>
                </div>
                <?php
            }
            else if ($_GET['add'] == 'req_del_f'){ ?>
                <div style="text-align:center;" class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    Error! Something Went Wrong
                    <button type="button" class="close" data-dismiss="alert">x</button>
                </div>
                <?php
            }

        }
        ?>

        <div class="box no-border">
            <div class="box-header">
              <h3 class="box-title"><i class="fa fa-info-circle" aria-hidden="true"></i> Info</h3>
            </div>
              <?php 
              //If request is sent to supervisor or group already has a supervisor
              if ($request_sent == true){ ?>
            <div class="box-body ">
              <h4>You can not sent request to supervisor</h4>
                <p>This may be due to reasons</p>
                <ul>
                    <li>You already have a group supervisor</li>
                    <!--<li>You have sent request to a supervisor</li>-->
                    <form action="" name="cancelRequest" method="POST" data-toggle="validator">
                      <li >You have sent request to a supervisor already
                        <?php if (isset($requestId)){ ?>
                            <input type="hidden" name="deleteId" value="<?php echo $requestId;?>">
                            <button type="submit" name="btnDeleteReq" class="btn btn-default btn-flat btn-xs"><i class="fa fa-user-times" aria-hidden="true"></i> Cancel Request</button>
                        <?php
                        }?>
                      </li>
                    </form>
                </ul>
            </div>
            <?php  }
            //If request is not sent
              else{ ?>
              
               <!-- /.box-header -->
            <div class="box-body ">
              <table id="chooseSupervisor" class="table table-hover" >
              <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Supervising Quota</th>
                    <th>Action</th>
                </tr>
              </thead>
                    <?php
                    $sql = "SELECT * FROM faculty JOIN work_load ON faculty.facultyId = work_load.facultyId WHERE currentLoad < totalLoad ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                    // output data of each row
                        while($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                            <td><?php echo $row["facultyName"]; ?></td>
                            <td><?php echo $row["facultyEmail"]; ?></td>
                            <td><?php echo $row["designation"]; ?></td>
                            <td><span class="label label-default">
                                <?php
                                echo 'Current:'.$row['currentLoad'].' Total '.$row['totalLoad'];
                                ?>
                                </span>
                            </td>
                            <td>
                            <form name="chooseSupervisor" action="" method="post" data-toggle="validator">
                                <input type= "hidden" name="facultyId" value="<?php echo $row["facultyId"];?>"/>
                                <button type="submit" name="btnChooseSupervisor" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-user-plus" aria-hidden="true"></i> Send Request</button>
                            </form>
                            </td>
                            </tr>
                        <?php }
                    }
                            ?>
              </table>
                <div class="box-footer">
                    <a href="<?php echo siteroot; ?>" class="btn btn-default btn-sm">Back</a>
                </div>
            </div>
            <!-- /.box-body -->
                  
            <?php  } ?>
              
           
          </div>
          <!-- /.box -->

    </div>
    <div class="col-md-1"></div>

    </div>

    </section>
    </div>
<!--    </div>-->
<?php require_once("includes/main-footer.php");?>
</div>
<?php require_once("includes/required_js.php");?>

 
</body>
</html>
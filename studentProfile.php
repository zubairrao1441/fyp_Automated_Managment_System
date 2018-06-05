<?php
$title="FYPMS";
$subtitle="Profile";
require_once("includes/config.php");
require_once("includes/header.php");
session_start();

//Check if user is logged in Else log out
if(isset($_SESSION["usrId"]) OR isset($_SESSION['facultyId']) OR isset($_SESSION['examinerId']))
{

}else{
    header('Location: '.'index.php');
}

//Code implementation for remove photo
if (isset($_POST['btnDelete'])){
    $sql_remove='UPDATE student SET studentImage=? WHERE studentId=?';
    $user_id=$_SESSION['usrId'];
    $dummy_image=null;
    $stmt_remove = $conn->prepare($sql_remove);
    if($stmt_remove === false) {
    trigger_error('Wrong SQL: ' . $sql_remove . ' Error: ' . $conn->error, E_USER_ERROR);
    }
    $stmt_remove->bind_param('ss',$dummy_image,$user_id);
    $stmt_remove->execute();
    $stmt_remove->close();
    
    $file = "public/profile_images/".$_SESSION["image"];
    if (file_exists($file)){
       if ($file=="public/profile_images/dummy.png"){
        //Dont delete
    }else{
    if (unlink($file)){
    $_SESSION["image"]=null;
        header('Location:' . $_SERVER['PHP_SELF'] . '?status=remove');;
    }else{header('Location:' . $_SERVER['PHP_SELF']);};
    } 
    }
    
    
}
//Code for Image upload 
if (isset($_FILES['image'])){
    $file=$_FILES['image'];
    
    //File properties
    $file_name  =   $file['name'];
    $file_tmp   =   $file['tmp_name'];
    $file_size  =   $file['size'];
    $file_error =   $file['error'];
    
    //Work out file extension
    $file_ext   =   explode('.',$file_name);
    $file_ext   = strtolower(end($file_ext));
    
    $allowed    = array('jpg','jpeg');

    if(in_array($file_ext,$allowed)){
    if($file_error === 0){
        if($file_size <= 2097152){
            $file_name_new  = uniqid('',true).'.'.$file_ext;
            $file_destination   ='public/profile_images/'.$file_name_new;
        }else {$error_msg='The picture size is greater than 2MiB';}
        if(move_uploaded_file($file_tmp, $file_destination)){
            //echo $file_destination;
            $success_msg='File Uploaded Successfully';
            $sql = "UPDATE student SET studentImage=? WHERE studentId=? ";
            $stmt = $conn->prepare($sql);
            if($stmt === false) {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
            }
            $stmt->bind_param('ss',$file_name_new,$_SESSION["usrId"]);
            $stmt->execute();
            //Delete old photo and set new photo
           $file = "public/profile_images/".$_SESSION["image"];
            if (file_exists($file)){
               if ($file=="public/profile_images/dummy.png"){
                //Dont delete
            }else{
            if (unlink($file)){
                }else{};
            } 
            }
            $_SESSION["image"]=$file_name_new;        
           
                
            $stmt->close();
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
        }else {$error_msg='Error! File not uploaded';}
    }
    }else {$error_msg='File not uploaded';}
}


//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["id"]) and is_numeric($_GET["id"]) ){
        $studentId = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);

        //Default Values
        $name = '--';
        $cms = '--';
        $email = '--';
        $batchName = '--';
        $contact = '--';
        $groupId = '--';
        $projectName = '--';
        $image = NULL;

        $sql = "SELECT * FROM student WHERE student.studentId = '$studentId' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $name = $row['studentName'];
                $cms = $row['studentCMS'];
                $email = $row['studentEmail'];
                $image = $row['studentImage'];
                $batchId = $row['batchId'];
                $contact = $row['studentPhoneNo'];
                $groupId = $row['groupId'];

            }
        } else {

        }
        
        //Get project Name
        if (isset($groupId) AND is_numeric($groupId)){
            $projectName = $conn->query("SELECT projectName FROM student_group WHERE groupId = '$groupId' ")->fetch_object()->projectName;
        }
        
        //Get batch Name
        if (isset($batchId) AND is_numeric($batchId)){
            $batchName = $conn->query("SELECT batchName FROM batch WHERE batchId = '$batchId' ")->fetch_object()->batchName;
        }

    }
}



//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['btnEditProf']) AND $_POST['phoneNumber'] != ""){
        
        $studentId = $_SESSION['usrId'];
        $phoneNum = $_POST['phoneNumber'];
        
        $sql = "UPDATE student SET studentPhoneNo='$phoneNum' WHERE studentId='$studentId' ";

        if ($conn->query($sql) === TRUE) {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
        } else {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
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

        <div class="col-lg-1"></div>
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



                <?php
                if (isset($_GET['id']) AND is_numeric($_GET['id']) ) { ?>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Student Information</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->

                            <div class="box-body">

                                <!-- Profile Image -->
                                    <div class="box-body box-profile">
                                        <img class="profile-user-img img-responsive img-rounded" src="<?php if (isset($image)){
                                            echo 'public/profile_images/'.$image;
                                        }else {echo 'public/profile_images/dummy.png';}?>" alt="User profile picture">
                                        <h3 class="profile-username text-center"><?php echo $name;?></h3>
                                    </div>
                                    <!-- /.box-body -->
                                <!-- /.box -->
                                <p class="text-muted text-center">Student</p>
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <b>CMS</b> <a class="pull-right"><?php echo $cms;?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Email</b> <a class="pull-right"><?php echo $email;?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Batch</b> <a class="pull-right"><?php echo $batchName;?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Contact No.</b> <a class="pull-right"><?php echo $contact;?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Group</b> <a class="pull-right"><?php echo "Group #". $groupId;?></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Project</b> <a class="pull-right"><?php echo $projectName;?></a>
                                    </li>
                                </ul>


                            </div>

                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button class="btn btn-default btn-sm" onclick="goBack()">Back</button>
                        </div>

                    </div>  <!-- /.box -->
                </div> <!-- end of col-md-8 !-->

                <div class="col-md-2"></div>


            </div>
        </div>

                <?php
                }else if(isset($_SESSION["usrId"])){ ?>
            <div class="row">
                <div class="col-md-8 pull-left">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Personal Information</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" id="studentProfile" name="studentProfile" method="POST" data-toggle="validator">

                            <div class="box-body">
                                <div class="form-group has-feedback">
                                    <input type="text" name="userCMS" class="form-control " disabled="" placeholder="<?php echo $_SESSION["usrCMS"]; ?>" />
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="text" name="userName" class="form-control " disabled="" placeholder="<?php echo $_SESSION["usrnm"]; ?>" />
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="email" name="userEmail" class="form-control" disabled="" placeholder="<?php echo $_SESSION["usrEmail"]?>" />
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="text" name="phoneNumber" class="form-control bfh-phone" placeholder="<?php echo $_SESSION["contact"]?>" />
                                    <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                </div>
                                <p class="text-aqua">*You can't change your Name and CMS.If you think there is a mistake kindly contact your Coordinator</p>
                            </div>

                        </form>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit" name="btnEditProf" form="studentProfile" class="btn btn-primary pull-right">Submit</button>
                            <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn btn-default" >Cancel</a>
                            <!--                        <button type="submit" class="btn btn-primary">Submit</button>-->
                        </div>

                    </div>  <!-- /.box -->
                </div> <!-- end of col-md-8 !-->

                <div class="col-md-4 pull-right">
                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                            <img class="profile-user-img img-responsive img-circle" src="<?php if (isset($_SESSION['image'])){
                                echo 'public/profile_images/'.$_SESSION['image'];
                            }else {echo 'public/profile_images/dummy.png';}?>" alt="User profile picture">
                            <h3 class="profile-username text-center"><?php echo $_SESSION["usrnm"]?></h3>
                            <p class="text-muted text-center"><?php echo $_SESSION["usrCMS"]?></p>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <p class="text-aqua" style="text-align: center">Browse image and press upload</p>
                                    <form action="" method="post" enctype="multipart/form-data" data-toggle="validator">
                                        <div class="form-group">
                                        <input type="file" name="image" class="btn btn-block btn-flat" accept=".jpg ,.jpeg, .png, .bmp, .svg" required>
                                        <input type="submit" value="Upload" class="btn btn-block ">
                                        </div>
                                    </form>
                                </li>
                            </ul>
                            <form role="form" id="change_image" action="" method="post" data-toggle="validator">
                                <input   name="btnDelete" id="btnDelete" value="Remove Photo" class="btn bg-maroon btn-block" />
                            </form>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>


                <?php
                }
                ?>

    </div>        
    <div class="col-lg-1"></div>        
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
<script>
    function goBack() {
        window.history.back();
    }

$( "#btnDelete" ).click(function() {
swal({
  title: "Are you sure?",
  text: "You will not be able to recover this",
  type: "warning",
  showCancelButton: true,
  confirmButtonColor: "#DD6B55",
  confirmButtonText: "Yes, delete it!",
  closeOnConfirm: false
},
function(){
  swal("Deleted!", "Your profile image has been deleted.", "success");
  change_image.submit();
});
});;
</script>
</body>
</html>    


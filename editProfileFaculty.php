<?php
$title="FYPMS";
$subtitle="Edit Profile";
require_once("includes/config.php");
require_once("includes/header.php");
session_start();
//Check if user is logged in Else log out
if(!isset($_SESSION["usrId"]))
{
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
                $_SESSION["image"]=null;header('Location: '.'editProfile.php?=status=remove');}else{header('Location: '.'editProfile.php');};
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
                header('Location: '.'editProfile.php?=status=updated');
            }else {$error_msg='Error! File not uploaded';}
        }
    }else {$error_msg='File not uploaded';}
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
                    <div class="row">
                        <div class="col-md-8 pull-left">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Personal Information</h3>
                                </div>
                                <!-- /.box-header -->
                                <!-- form start -->
                                <form role="form" id="add_coordinator" action="studentProfile.php" method="post" data-toggle="validator">
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
                                            <input type="email" name="userEmail" class="form-control" disabled="" placeholder="Email" />
                                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <input type="text" name="phoneNumber" class="form-control bfh-phone" placeholder="Phone Number" />
                                            <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                        </div>
                                        <p class="text-aqua">*You can't change your Name and CMS.If you think there is a mistake kindly contact your Coordinator</p>

                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">

                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
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
                                            <form action="studentProfile.php" method="post" enctype="multipart/form-data" data-toggle="validator">
                                                <input type="file" name="image" class="btn btn-block btn-flat">
                                                <input type="submit" value="Upload" class="btn btn-block ">
                                                <?php if(isset($error_msg)){?><br/><p class="text-red" style="text-align: center"><?php echo $error_msg?></p><?php }?>
                                                <?php if(isset($success_msg)){?><br/><p class="text-green" style="text-align: center"><?php echo $success_msg?></p><?php }?>
                                            </form>
                                        </li>
                                    </ul>
                                    <form role="form" id="change_image" action="studentProfile.php" method="post" data-toggle="validator">
                                        <input   name="btnDelete" id="btnDelete" value="Remove Photo" class="btn bg-maroon btn-block" />
                                    </form>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                </div>
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


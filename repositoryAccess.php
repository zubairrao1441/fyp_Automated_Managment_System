<?php
$title="FYPMS";
$subtitle="Project Repository - Access ";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if COORDINATOR is logged in else log out
if(!isset($_SESSION["isCord"]))
{
    header('Location: '.'index.php');
}



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /****************
     * Register User
     ***************/
    if (isset($_POST['btnRegisterUser'])){
        //VALIDATIONS
        if (isset($_POST['userName']) && isset($_POST['userEmail'])){

            //Getting values from POST and sanitizing
            $name = filter_input(INPUT_POST,'userName',FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST,'userEmail',FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST,'userPassword',FILTER_SANITIZE_SPECIAL_CHARS);

            //Check if user already exists with email

            $check = $conn->query("SELECT user_id FROM repository_users WHERE user_email = '$email' LIMIT 1");
            if ($check->num_rows>0){
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=ae');die;
            }
            else{
                $access_type = 1;
                // prepare and bind
                $stmt = $conn->prepare("INSERT INTO repository_users (user_name, user_email, user_password, access_type) VALUES (?, ?,?,?)");
                $stmt->bind_param("sssi", $name, $email, $password, $access_type);

                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                }


                $stmt->close();
                $conn->close();

            }
        }
        else{
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=validation_err');die;
        }

    }

    /****************
     * Delete User
     ***************/
    if (isset($_POST['btnDelete'])){
        $deleteId = filter_input(INPUT_POST,'deleteId',FILTER_SANITIZE_NUMBER_INT);
        // sql to delete a record
        $sql = "DELETE FROM repository_users WHERE user_id = '$deleteId' ";

        if ($conn->query($sql) === TRUE) {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
        } else {
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
        }

    }

}



?>
<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper" >
        <?php require_once("includes/content-header.php"); ?>

        <section class="content" style="min-height: 700px">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">

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
                        else if ($_GET['status'] == 'ae'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! User already exists
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                        else if ($_GET['status'] == 'mail_err'){ ?>
                            <div style="text-align:center;" class="alert alert-info" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                User Registered Successfully but Email was not sent due to unknown error
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

                    }
                    ?>

                    <?php if (isset($_GET['Register'])){ ?>
                        <div class="register-box-body">


                            <div class="box-header">
                                <h4 class="text-center ">Register New User</h4>
                            </div>

                            <form id="registerUser" action="" method="post" data-toggle="validator">

                                <div class="form-group has-feedback">
                                    <input type="text" name="userName" class="form-control" placeholder="Enter Full name" required/>
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                </div>

                                <div class="form-group has-feedback">
                                    <input type="email" name="userEmail" class="form-control" placeholder="Enter Email address" required/>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                </div>

                                <div class="form-group has-feedback">
                                    <input type="text" name="userPassword" class="form-control" placeholder="Enter Password" required/>
                                    <span class="glyphicon glyphicon-asterisk form-control-feedback"></span>
                                </div>







                                <div class="box-footer ">


                                    <div class="form-group ">
                                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>"  class="btn btn-default ">Back </a>
                                        <button type="submit" name="btnRegisterUser" class="btn btn-primary pull-right">Register</button>
                                    </div>
                                </div>


                            </form>

                        </div><br>

                    <?php
                    }?>

                    <div class="box no-border">
                        <div class="box-header with-border">
                            <h3 class="box-title">List of Users</h3>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">
                            <table class="table" >
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Access Type</th>
                                    <th>Actions</th>
                                </tr>
                                <?php
                                $sql = "SELECT * FROM repository_users ";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['user_name']; ?></td>
                                            <td><?php echo $row['user_email']; ?></td>
                                            <td><?php
                                                if ($row['access_type'] == 1){
                                                    echo "FULL ACCESS";
                                                }else{
                                                    echo "OTHER";
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <form  action="" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');" data-toggle="validator">
                                                    <input type="hidden" name="deleteId" value="<?php echo $row['user_id'];?> ">
                                                    <button type="submit" name="btnDelete" class="btn  btn-danger btn-flat  btn-xs"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                                </form>

                                            </td>
                                        </tr>

                                        <?php
                                    }
                                } ?>
                            </table>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <a href="<?php echo $_SERVER['PHP_SELF'].'?Register';?>" class="btn btn-primary btn-sm pull-right">Register New User</a>
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
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>

<!-- page script -->
<script>
    $(function () {
        $('#projectRepository').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ],
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false

        });

    });
</script>

</body>
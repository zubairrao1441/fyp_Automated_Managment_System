<?php
$title = "FYPMS";
$subtitle = "Password Recovery";
require_once("includes/header.php");
require_once("includes/config.php");
require("libs/sendgrid-php/sendgrid-php.php");
require_once("includes/mail-tempelates.php");
require_once("includes/functions.php");

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Validation
    if (isset($_POST['forgetPassBtn'])) {
        //Getting value from POST
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        //Search email in Student Database
        $sql = "SELECT studentEmail FROM student WHERE studentEmail = '$email' LIMIT 1 ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            //Email found

            //Generate Random Password
            $newPassword = random_password();

            //Turn autocommit off
            mysqli_autocommit($conn, FALSE);

            //UPDATE password in database student database
            $sql = "UPDATE student SET studentPassword='$newPassword' WHERE studentEmail ='$email' LIMIT 1";

            if ($conn->query($sql) === TRUE) {

                mail_password_reset($email,$newPassword);


                if (mail_password_reset()) {
                    mysqli_commit($conn);
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
                    die;
                } else {
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
                    die;
                }
            }
        }
        else{
            //Search email in Faculty database
            $sql = "SELECT facultyEmail FROM faculty WHERE facultyEmail = '$email' LIMIT 1 ";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                //Email found

                //Generate Random Password
                $newPassword = random_password();

                //Turn autocommit off
                mysqli_autocommit($conn, FALSE);

                //UPDATE password in database student database
                $sql = "UPDATE faculty SET facultyPassword='$newPassword' WHERE facultyEmail ='$email' LIMIT 1";

                if ($conn->query($sql) === TRUE) {

                    mail_password_reset($email,$newPassword);


                    if (mail_password_reset()) {
                        mysqli_commit($conn);
                        header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
                        die;
                    } else {
                        header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
                        die;
                    }
                }
            }
            else{
                //Search email in External Examiner
                //Search email in Faculty database
                $sql = "SELECT examinerEmail FROM external_examiner WHERE examinerEmail = '$email' LIMIT 1 ";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    //Email found

                    //Generate Random Password
                    $newPassword = random_password();

                    //Turn autocommit off
                    mysqli_autocommit($conn, FALSE);

                    //UPDATE password in database student database
                    $sql = "UPDATE external_examiner SET examinerPassword='$newPassword' WHERE examinerEmail ='$email' LIMIT 1";

                    if ($conn->query($sql) === TRUE) {

                        mail_password_reset($email,$newPassword);


                        if (mail_password_reset()) {
                            mysqli_commit($conn);
                            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
                            die;
                        } else {
                            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
                            die;
                        }
                    }

                }
                else{
                    //Email not found
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
                    die;
                }

            }
        }

    }

}


?>
<title>Forgot Password | FYP Management System</title>
</head>
<?php
if (isset($_GET['status'])){
    if ($_GET['status'] == 't'){ ?>
        <div style="text-align:center;" class="alert alert-success" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign"></span>
            An email has been sent to your account with new password
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
    else if ($_GET['status'] == 's'){ ?>
        <div style="text-align:center;" class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign"></span>
            Error!
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
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="<?php echo siteroot;?>"><img src="./img/logo_type.png" alt="fyp_logo" width="360" length="52"></a>
    </div>

    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Forgot Password</p>



        <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="forgetPass" name="forgetPass" method="POST" data-toggle="validator">
            <div class="form-group has-feedback">
                <input type="email" name="email" class="form-control" name="email"
                       placeholder="Enter your email address" required  autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>

            <div class="row">
                <div class="col-xs-8">
                    <a href="<?php echo siteroot;?>" class="btn btn-default  btn-flat">Back</a>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">

                    <button type="submit" name="forgetPassBtn"
                            class="btn btn-primary btn-block btn-flat">Submit
                    </button>
                </div>
                <!-- /.col -->
            </div>
        </form>


    </div>
    <!-- /.login-box-body -->
</div>
<?php require_once("includes/required_js.php");?>
</body>
</html>

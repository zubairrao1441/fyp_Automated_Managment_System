<?php
$title="FYPMS";
$subtitle="Repository Login";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

if(isset($_SESSION["user_id"]))
{
    header('Location: '."home.php");
}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //VALIDATIONS
    if(isset($_POST["email"]) && isset($_POST["pasword"]))
    {
        $userEmail =  filter_input(INPUT_POST, "email",FILTER_SANITIZE_SPECIAL_CHARS);
        $userPass = filter_input(INPUT_POST, "password",FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "SELECT * FROM repository_users WHERE user_email='$userEmail' AND user_password= '$userPass' LIMIT 1 ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                //SETTING UP SESSION VALUES FOR STUDENT;
                $_SESSION["user_id"]=$row["user_id"];
            }
            header('Location: '.'projectRepository.php');
        }
        else{
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
        }



    }
    else{
        header('Location:' . $_SERVER['PHP_SELF'] . '?status=validation_err');die;
    }


}

?>

<?php
if (isset($_GET['status'])){
    if ($_GET['status'] == 't'){ ?>
        <div style="text-align:center;" class="alert alert-success" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign"></span>
            Login successfull!
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

<title>Repository Login | FYP Management System</title>
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="<?php echo siteroot;?>"><img src="./img/logo_type.png" alt="fyp_logo" width="360" length="52"></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to see Project Repository</p>

        <form action="index.php" method="POST">
            <div class="form-group has-feedback">
                <input type="email" class="form-control" name="email" placeholder="Email" data-toggle="validator" required autofocus >
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">

                </div>
                <!-- /.col -->
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <br />


    </div>
    <!-- /.login-box-body -->
</div>
<?php require_once("includes/required_js.php");?>
</body>
</html>

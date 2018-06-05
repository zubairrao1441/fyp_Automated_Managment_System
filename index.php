<?php
$title="FYPMS";
$subtitle="Login";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

if(isset($_SESSION["usrnm"]))
{
	header('Location: '."home.php");
}
//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST["email"]) && isset($_POST["pasword"]))
    {
        $userEmail =  filter_input(INPUT_POST, "email",FILTER_SANITIZE_SPECIAL_CHARS);
        $userPass = filter_input(INPUT_POST, "pasword",FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "SELECT * FROM student";
        $sql2 = "SELECT * FROM faculty";
        $sql3 ="SELECT * FROM external_examiner";

        $result = $conn->query($sql);
        $result2 = $conn->query($sql2);
        $result3 = $conn->query($sql3);

        $check=0;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row["studentEmail"]==$userEmail && $row["studentPassword"]==$userPass)
                {
                    //Check if account is active
                    if ($row['isActive'] != 1){
                        header('Location: '.'index.php?status=deactivated');die;
                    }
                    //SETTING UP SESSION VALUES FOR STUDENT;
                    $_SESSION["usrId"]=$row["studentId"];
                    $_SESSION["usrnm"]=$row["studentName"];
                    $_SESSION["usrCMS"]=$row["studentCMS"];
                    $_SESSION["usrEmail"]=$row["studentEmail"];
                    $_SESSION["usrGender"]=$row["studentGender"];
                    $_SESSION["type"]="Student";
                    $_SESSION["usrId"]=$row["studentId"];
                    $_SESSION["isLead"]=$row["isLeader"];
                    $_SESSION["GroupID"]=$row["groupId"];
                    $_SESSION["BatchID"]=$row["batchId"];
                    $_SESSION["usrEmail"]=$row["studentEmail"];
                    $_SESSION["image"]=$row["studentImage"];
                    $_SESSION["contact"]=$row["studentPhoneNo"];
                    $check=1;
                    header('Location: '.'home.php');
                }
            }
        }
        if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
                if($row2["facultyEmail"]==$userEmail && $row2["facultyPassword"]==$userPass)
                {
                    //Check if account is active
                    if ($row2['isActive'] != 1){
                        header('Location: '.'index.php?status=deactivated');die;
                    }
                    //SETTING UP SESSION VALUES FOR FACULTY
                    session_start();
                    $_SESSION["facultyId"]=$row2["facultyId"];
                    $_SESSION["usrnm"]=$row2["facultyName"];
                    $_SESSION["email"]=$row2["facultyEmail"];
                    $_SESSION["contact"]=$row2["facultyPhoneNo"];
                    $_SESSION["design"]=$row2["designation"];
                    $_SESSION["isCord"]=$row2["isCoordinator"];
                    $_SESSION["isAdmin"]=$row2["isAdmin"];
                    $_SESSION["type"]="Faculty";
                    $_SESSION["image"]=$row2["facultyImage"];
                    $check=1;
                    header('Location: '.'home.php');
                }
            }
        }
        if ($result3->num_rows > 0) {
            while($row3 = $result3->fetch_assoc()) {
                if($row3["examinerEmail"]==$userEmail && $row3["examinerPassword"]==$userPass)
                {
                    //Check if account is active
                    if ($row3['isActive'] != 1){
                        header('Location: '.'index.php?status=deactivated');die;
                    }
                    //SETTING UP SESSION VALUES FOR EXTERNAL EXAMINER
                    session_start();
                    $_SESSION["examinerId"]=$row3["examinerId"];
                    $_SESSION["usrnm"]=$row3["examinerName"];
                    $examiner=$row3["examinerId"];
                    $_SESSION["design"]="External Examiner";
                    $_SESSION["type"]="Examiner";
                    $_SESSION["image"]=$row2["profileImage"];
                    $check=1;
                    header('Location: '.'home.php');
                }
            }
        }

        if($check==0)
        {
            $error="Email or Password is invalid";?>
            <div style="text-align:center;" class="alert alert-danger" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign"></span>
                <?php echo $error?>
                <button type="button" class="close" data-dismiss="alert">x</button>
            </div>
            <?php
        }
        $conn->close();
    }


}

?>

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
    else if ($_GET['status'] == 'deactivated'){ ?>
        <div style="text-align:center;" class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign"></span>
            Your account is deactivated.
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

<title>Login | FYP Automated Management System</title>
</head>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo siteroot;?>"><img src="./img/logo_type.png" alt="fyp_logo" width="360" length="52"></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <form action="index.php" method="POST">
      <div class="form-group has-feedback">
          <input type="email" class="form-control" name="email" placeholder="Email" data-toggle="validator" required autofocus >
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
          <input type="password" class="form-control" name="pasword" placeholder="Password" required>
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
    <a href="resetPassword.php">I forgot my password</a><br>

  </div>
  <!-- /.login-box-body -->
</div>
<?php require_once("includes/required_js.php");?>
</body>
</html>

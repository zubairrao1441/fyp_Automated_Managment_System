<?php 
$title="FYPMS";
$subtitle="Register Faculty Members";
require_once("includes/header.php");
require_once("includes/config.php");
require("libs/sendgrid-php/sendgrid-php.php");
require_once("includes/mail-tempelates.php");
require_once("includes/functions.php");
session_start();

//Check if Coordinator is logged ins
if($_SESSION["isCord"] != 1)
{
    header('Location: '.'index.php');
}
//If form is submitted using POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Validations
    if (isset($_POST['facultyName']) && isset($_POST['facultyEmail']) && isset($_POST['facultyDesignation']) && isset($_POST['supervisingQuota']) && isset($_POST['emailSend'])){

        //Getting values from POST and Sanitizing
        $name = filter_input(INPUT_POST,'facultyName',FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST,'facultyEmail',FILTER_SANITIZE_EMAIL);
        $design = filter_input(INPUT_POST,'facultyDesignation',FILTER_SANITIZE_SPECIAL_CHARS);
        $contact = filter_input(INPUT_POST,'facultyContact',FILTER_SANITIZE_NUMBER_INT);
        $quota = filter_input(INPUT_POST,'supervisingQuota',FILTER_SANITIZE_NUMBER_INT);
        $password = filter_input(INPUT_POST,'facultyPassword',FILTER_SANITIZE_SPECIAL_CHARS);
        $emailSend = filter_input(INPUT_POST,'emailSend',FILTER_SANITIZE_SPECIAL_CHARS);

        if ($password == ""){
            //If password field left blank
            $password = random_password();
        }

        //Check if faculty already exists with email

        $check = $conn->query("SELECT facultyId FROM faculty WHERE facultyEmail = '$email' LIMIT 1");
        if ($check->num_rows>0){
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=ae');die;
        }
        else{
            // Set autocommit to off
            mysqli_autocommit($conn, FALSE);


            // prepare and bind
            $stmt = $conn->prepare("INSERT INTO faculty (facultyName, facultyEmail, designation, facultyPhoneNo, facultyPassword ) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $design, $contact, $password);

            // set parameters and execute

            $stmt->execute();

            $last_id = $stmt->insert_id;
            $currentLoad = 0;

            //Also add to work_load
            $stmt = $conn->prepare("INSERT INTO work_load (facultyId, totalLoad, currentLoad) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $last_id, $quota, $currentLoad);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                // Commit transaction
                mysqli_commit($conn);

                if ($emailSend != 'false'){
                    mail_user_registration($email,$name,$password); // Send email
                    if (!mail_user_registration()){
                        header('Location:' . $_SERVER['PHP_SELF'] . '?status=mail_err');die;
                    }
                }else
                {
                    $stmt->close();
                    $conn->close();
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                }
            }

        }
    }
    else{
        //Failed validations
        header('Location:' . $_SERVER['PHP_SELF'] . '?status=validation_err');die;
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
	
    <div class="col-md-2"></div>        
    <div class="col-md-8">
        <?php
        if (isset($_GET['status'])){
            if ($_GET['status'] == 't'){ ?>
                <div style="text-align:center;" class="alert alert-success" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    Faculty Registered successfully!
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
                    Error! Faculty already exists
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
<!--Code for register faculty starts here-->
  <div class="register-box-body">


      <div class="box-header">
          <h4 class="text-center ">Register a Faculty</h4>
      </div>

    <form id="registerFaculty" action="" method="post" data-toggle="validator">

      <div class="form-group has-feedback">
        <input type="text" name="facultyName" pattern="[a-zA-Z][a-zA-Z ]{4,}" class="form-control" placeholder="Enter Full name" required/>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>

        <div class="form-group has-feedback">
            <input type="email" name="facultyEmail" class="form-control" placeholder="Enter Email address" required/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>


      <div class="form-group has-feedback">
        <input type="text" name="facultyDesignation" class="form-control" placeholder="Enter Designation" required/>
        <span class="glyphicon glyphicon-bookmark form-control-feedback"></span>
      </div>


      <div class="form-group has-feedback">
        <input type="text" name="facultyContact" pattern="[0-9]+" minlength="10" maxlength="11" class="form-control bfh-phone" placeholder="Phone Number" />
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
      </div>

	  <div class="form-group has-feedback">
		<input type="number" name="supervisingQuota" class="form-control bfh-phone" placeholder="Enter Group supervising Quota" min="0" max="10" required/>
        <span class="glyphiconglyphicon glyphicon-plus form-control-feedback"></span>
	  </div>

      <div class="form-group has-feedback">
        <input type="text" name="facultyPassword" class="form-control" placeholder="Enter password of leave empty to genereate random password" />
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>


        <div class="box-footer ">
            <div class="checkbox pull-left">
                <label>
                    <input type="checkbox" name="emailSend" value="false" checked> Do not send email to user
                </label>
            </div>

            <div class="form-group pull-right">
                <a href="<?php echo siteroot ?>"  class="btn btn-default">Back </a>
                <button type="submit" name="AddFaculty" class="btn btn-primary">Register</button>
            </div>
        </div>


    </form>

  </div>
<!--Code for register faculty ends here-->

    </div>
    <div class="col-md-2"></div>

    </div>
    </section>
    </div>
    
    <?php

    require_once("includes/main-footer.php");
    require_once("includes/required_js.php");
    ?>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
 
</body>
</html>
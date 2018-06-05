<?php
$title = "FYPMS";
$subtitle = "Register Students";
require_once("includes/header.php");
require_once("includes/config.php");
require("libs/sendgrid-php/sendgrid-php.php");
require_once("includes/mail-tempelates.php");
require_once("includes/functions.php");

session_start();

//Check if Coordinator is loggedIn else log out
if($_SESSION['isCord'] != 1){
    header('Location: ' . 'index.php');
}

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['btnRegisterStudent'])){
        /* Validations
        * Required name, cms, gender, email, batch
        */


        if (($_POST['name']) !="" && $_POST['cms'] !="" && $_POST['gender'] !="" && $_POST['email'] !="" && $_POST['batch'] !="" ){

            //Getting values from POST and sanitizing it
            $cms = filter_input(INPUT_POST,'cms',FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST,'name',FILTER_SANITIZE_SPECIAL_CHARS);
            $gender = filter_input(INPUT_POST,'gender',FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
            $contact = filter_input(INPUT_POST,'contact',FILTER_SANITIZE_NUMBER_INT);
            $batchId = filter_input(INPUT_POST,'batch',FILTER_SANITIZE_NUMBER_INT);

            //If password field is empty generate password
            if (isset($_POST['password'])){
                $password = filter_input(INPUT_POST,'password',FILTER_SANITIZE_SPECIAL_CHARS);
            }
            else{
                $password =  random_password();
            }

            //Check if student already exists with email & cms
            $sql = "SELECT * FROM student WHERE (studentCMS = '$cms' OR studentEmail = '$email') AND isActive = 1 LIMIT 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                //Student Already Registered
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=ar');die;
            } else {
                //Student not registered already

                //other values
                $isActive = 1;

                // prepare and bind
                $stmt = $conn->prepare("INSERT INTO student (studentName, studentCMS, studentEmail, studentPhoneNo, studentGender, studentPassword, batchId, isActive) VALUES (?, ?, ?, ?, ?, ? , ? , ?)");
                $stmt->bind_param("ssssssii", $name, $cms, $email, $contact, $gender, $password, $batchId, $isActive  );

                $stmt->execute();

                if ($stmt->affected_rows > 0) {

                    //Check for checkbox
                    if ($_POST['emailSend'] != 'false'){
                        mail_user_registration($StudentEmail,$StudentName,$StudentPass);
                        if (!mail_user_registration()){

                            header('Location:' . $_SERVER['PHP_SELF'] . '?status=mail_err');die;
                        }
                    }

                    $stmt->close();
                    $conn->close();
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                }



            }



        }
        else{
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=e');die;
        }
    }



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
                <div class="col-md-2"></div>
                <div class="col-md-8">

                    <?php
                    if (isset($_GET['status'])){
                        if ($_GET['status'] == 't'){ ?>
                            <div style="text-align:center;" class="alert alert-success" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                <p>Student Registered successfully!</p>
                                <a href="./manageStudents.php"><i class="fa fa-chevron-right" aria-hidden="true"></i> Manage Students</a>
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
                        else if ($_GET['status'] == 'ar'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! This student is already registered
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                        else if ($_GET['status'] == 'mail_err'){ ?>
                            <div style="text-align:center;" class="alert alert-info" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Student Registered Successfully but email was not sent to user due to some error
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                            <?php
                        }
                    }

                    ?>

                    <!--Code for register student starts here-->
                    <div class="register-box-body">

                        <div class="box-header">
                            <h4 class="text-center ">Register a Student</h4>
                        </div>

                        <form id="registerStudent" name="registerStudent" action="" method="post" data-toggle="validator">

                            <div class="form-group has-feedback">
                                <input type="number" min="000001" max="99999" name="cms" class="form-control" placeholder="Enter CMS" required/>
                                <span class="glyphicon glyphicon-asterisk form-control-feedback"></span>
                            </div>

                            <div class="form-group has-feedback">
                                <input type="text" name="name" pattern="[a-zA-Z][a-zA-Z ]{4,}"  class="form-control" placeholder="Enter Full name" maxlength="30" minlength="5" required/> <!--TODO : Regex for name-->
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            </div>

                            <div class="form-group has-feedback">
                                <label>Gender </label>
                                <input type="radio" name="gender" value="male" checked> Male
                                <input type="radio" name="gender" value="female"> Female<br>
                            </div>

                            <div class="form-group has-feedback">
                                <input type="email" name="email" class="form-control" placeholder="Enter Email" required/>
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            </div>

                            <div class="form-group has-feedback">
                                <input type="text" name="contact" pattern="[0-9]+" minlength="10" maxlength="11" class="form-control bfh-phone"  placeholder="Phone Number" /> <!--TODO : Add pattern for number here-->
                                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                               <select name="batch" class="form-control" required>
                                   
                                    <?php
                                    $sqlGet = "SELECT * FROM batch WHERE isActive= 1 ORDER BY createdDtm DESC";
                                    $result = $conn->query($sqlGet);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) { ?>
                                            <option value="<?php echo $row['batchId'];?>"><?php echo $row['batchName']; ?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group has-feedback">
                                <input type="text" name="password" class="form-control" placeholder="Enter Password" />
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>

                            <p class="help-block">Leave password field empty for random password</p>



                            <div class="box-footer ">
                                <div class="checkbox pull-left">
                                    <label>
                                        <input type="checkbox" name="emailSend" value="false" checked> Do not send email to user
                                    </label>
                                </div>
                                <div class="form-group pull-right">
                                <a href="<?php echo siteroot; ?>" class="btn  btn-default btn-sm  "> Back</a>
                                <button type="submit" name="btnRegisterStudent" class="btn btn-primary btn-sm">Register</button>
                                </div>
                            </div>

                        </form>

                    </div>
                    <!--Code for register student ends here-->

                </div>
                <div class="col-md-2"></div>

            </div>
        </section>
    </div>

    <?php require_once("includes/main-footer.php"); ?>

</div>
<?php
    require_once("includes/required_js.php");
    ?>



</body>
</html>
<?php
$title="FYPMS";
$subtitle="Register Coordinator";
require_once("includes/header.php");
require_once("includes/config.php");
require_once("includes/functions.php");
session_start();

//Check if superadmin is logged in else log out
if($_SESSION['isAdmin'] != 1)
{
    header('Location: '.'index.php');
}

//Check if for is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

}

//Check if for is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Getting values from POST and Sanitizing
    $name = filter_input(INPUT_POST,'coordName',FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST,'coordEmail',FILTER_SANITIZE_EMAIL);
    $designation = filter_input(INPUT_POST,'coordDesign',FILTER_SANITIZE_SPECIAL_CHARS);
    $contact = filter_input(INPUT_POST,'phoneNumber',FILTER_SANITIZE_NUMBER_INT);
    $quota = filter_input(INPUT_POST,'quota',FILTER_SANITIZE_NUMBER_INT);
    $password = filter_input(INPUT_POST,'coordPass',FILTER_SANITIZE_SPECIAL_CHARS);
    $isCoordinator = 1;

    // Set autocommit to off
    mysqli_autocommit($conn, FALSE);
    // prepare and bind
    $stmt = $conn->prepare("INSERT INTO faculty (facultyName, facultyEmail, designation, facultyPhoneNo, facultyPassword, isCoordinator) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $email, $designation, $contact, $password, $isCoordinator);

    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $last_id = $stmt->insert_id;
        $currentLoad = 0;

        //Also add to work_load
        $stmt = $conn->prepare("INSERT INTO work_load (facultyId, totalLoad, currentLoad) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $last_id, $quota, $currentLoad);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            // Commit transaction
            mysqli_commit($conn);
            $stmt->close();
            $conn->close();
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;

        }
    }
    else{
        header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');die;
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
                                Coordinator Registered successfully!
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

                    <!--Code for register coord starts here-->
                    <div class="register-box-body">


                        <div class="box-header">

                            <h4 class="text-center ">Register Coordinator</h4>

                        </div>

                        <form id="registerCoordinator" action="" method="post" data-toggle="validator">


                            <div class="form-group has-feedback">
                                <input type="text" name="coordName" class="form-control" placeholder="Full name" required/>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="email" name="coordEmail" class="form-control" placeholder="Email" required/>
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            </div>

                            <div class="form-group has-feedback">
                                <input type="text" name="coordDesign" class="form-control" placeholder="Designation" required/>
                                <span class="glyphicon glyphicon-bookmark form-control-feedback"></span>
                            </div>

                            <div class="form-group has-feedback">
                                <input type="text" name="phoneNumber" class="form-control bfh-phone" placeholder="Phone Number" required/>
                                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="number" name="quota" class="form-control bfh-phone" placeholder="Supervising Quota" min="0" max="10" required/> <!-- TODO coordQuota Limit-->
                                <span class="glyphicon glyphicon-shopping-cart form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="text" name="coordPass" class="form-control" placeholder="Password" required />
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>




                            <div class="box-footer ">


                                <div class="form-group">
                                    <a href="<?php echo siteroot ?>"  class="btn btn-default">Back </a>
                                    <button type="submit" name="addCoord" class="btn btn-primary pull-right">Register</button>
                                </div>
                            </div>


                        </form>

                    </div>
                    <!--Code for register coord ends here-->

                </div>
                <div class="col-md-2"></div>

            </div>
        </section>
    </div>

    <?php
    require_once("includes/main-footer.php");?>
    </div>
    <?php
    require_once("includes/required_js.php");    ?>


</body>
</html>
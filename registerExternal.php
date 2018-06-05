<?php 
$title="FYPMS";
$subtitle="Register External Examiners";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if Coordinator is logged in
if($_SESSION["isCord"] != 1)
{
    header('Location: '.'index.php');
}

//If form is submitted using POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Validations
    if ( $_POST['name'] != "" && $_POST['email'] != "" && $_POST['company'] != "" && $_POST['password'] != "" ){

        //Getting values from POST and sanitizing it
        $name = filter_input(INPUT_POST,'name',FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
        $company = filter_input(INPUT_POST,'company',FILTER_SANITIZE_SPECIAL_CHARS);
        $designation = filter_input(INPUT_POST,'designation',FILTER_SANITIZE_SPECIAL_CHARS);
        $contact = filter_input(INPUT_POST,'contact',FILTER_SANITIZE_NUMBER_INT);
        $password = filter_input(INPUT_POST,'password',FILTER_SANITIZE_SPECIAL_CHARS);

        //Check if faculty already exists with email

        $check = $conn->query("SELECT examinerId FROM external_examiner WHERE examinerEmail = '$email' LIMIT 1");
        if ($check->num_rows>0){
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=ae');die;
        }
        else{
            $isActive = 1;
            $stmt = $conn->prepare("INSERT INTO external_examiner (examinerName, examinerEmail, examinerPassword, examinerPhone, company, designation, isActive) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $name, $email, $password, $contact, $company, $designation, $isActive);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
            }
        }


    }
    else{
        header('Location:' . $_SERVER['PHP_SELF'] . '?status=validation_err');die;
    }

}
if((isset($_POST['externalName'])) && (isset($_POST['externalEmail']))) {
    if(($_POST['externalName']!="") && ($_POST['externalEmail']!=""))
    {

    //echo $_POST['facultyName']." ".$_POST['facultyCMS']." ".$_POST['facultyEmail']." ".$_POST['phoneNumber']." ".$_POST['batch']." ".$_POST['facultyPass'];
    $ExternalCompany = $_POST['externalCompany'];
	$ExternalName = $_POST['externalName'];
    $ExternalDesign = $_POST['externalDesign'];
    $ExternalEmail = strtolower($_POST['externalEmail']);
    $ExternalPhone = $_POST['externalNumber'];
    $ExternalPass = $_POST['externalPass'];
	
	

	$sql = "INSERT INTO external_examiner (examinerName, examinerPhone, examinerEmail, examinerPassword, company) 
    VALUES ('$ExternalName','$phoneNumber','$ExternalEmail','$ExternalPass','$ExternalCompany')";

    $sqlCheck = "SELECT * FROM external_examiner WHERE examinerName = '$ExaminerName' OR exminerEmail = '$ExaminerEmail'";
	
	
    $results=$conn->query($sqlCheck);

    if (!$results->num_rows > 0) {
		
            if (!$conn->query($sql) === TRUE) {
              $error="Registration Unsuccessfull";
              header("Location: registerExternal.php?status=f");
            }
            else{
              $error="";
			  //get the id of new faculty member created
			    
				$sqlIdGet="SELECT examinerId FROM external_examiner WHERE examinerName = '$ExaminerName' AND examinerEmail = '$ExaminerEmail'";
				$FacultyId='0';
				
				$idGet=$conn->query($sqlIdGet);
				if ($idGet->num_rows > 0) {
					
						while($rowId = $idGet->fetch_assoc())
						{
							$ExaminerId=$rowId["examinerId"];
						}
				}
				
					$error="Successfully Regstered";
					
					header('Location: registerExternal.php?status=t');
				}
				
					
				
            }
    }
    else{
        $error="Already Registered";
        header('Location: '.'registerExternal.php?status=f');
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
                    External Examiner Registered successfully!
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
          <h4 class="text-center ">Register External Examiner</h4>

      </div>

    <form id="registerExternal" action="" method="post" data-toggle="validator">

      <div class="form-group has-feedback">
        <input type="text" name="name" pattern="[a-zA-Z][a-zA-Z ]{4,}" class="form-control" placeholder="Full name" required/>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>

        <div class="form-group has-feedback">
            <input type="email" name="email" class="form-control" placeholder="Enter Email address" required/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>

      <div class="form-group has-feedback">
        <input type="text" name="company" class="form-control" placeholder="Enter Company Name" required/>
        <span class="glyphicon glyphicon-home form-control-feedback"></span>
      </div>

        <div class="form-group has-feedback">
            <input type="text" name="designation" class="form-control" placeholder="Enter Designation" />
            <span class="glyphicon glyphicon glyphicon-star form-control-feedback"></span>
        </div>



      <div class="form-group has-feedback">
        <input type="text" name="contact" pattern="[0-9]+" minlength="10" maxlength="11" class="form-control bfh-phone" placeholder="Phone Number" />
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
      </div>

      <div class="form-group has-feedback">
        <input type="text" name="password" class="form-control" placeholder="Enter Passassword" required/>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      
	 

        <div class="box-footer ">
            <div class="form-group">
                <a href="<?php echo siteroot; ?>"  class="btn btn-default">Back </a>
                <button type="submit" name="btnRegisterExternal" class="btn btn-primary pull-right">Register</button>
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
	//****************************************************************************************************************************************************
//
//																Footer includes
//
//**************************************************************************************************************************************************** -->
     
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
<?php
$title="FYPMS";
$subtitle="Home";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

$_GLOBALS["usr_image"]= $_SESSION["image"];
if(isset($_POST['Status']))
{
        echo $_POST['Status'];
}
if(!isset($_SESSION["usrnm"]))
{
        header('Location: '.'index.php');
}



     
if(isset($_POST['signout'])) { // logout button
        // Clear and destroy sessions and redirect user to home page url.
        $_SESSION = array();
        session_destroy();
        // Redirect to where the site home page is located -- Eg: localhost
        header('Location: '.'index.php');
        die;
}
?>
<script src="plugins/jQuery/jQuery-2.2.0.min.js"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

    <!-- Main head tag contains all header elements -->
    <?php require_once("includes/main-header.php"); ?>
        
    <!-- Left side column. contains the logo and sidebar -->
    <?php require_once("includes/main-sidebar.php"); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" >
    <!-- Content Header (Page header) -->
    <?php require_once("includes/content-header.php"); ?>

    <!-- Main content -->
    <section class="content">
    <!-- row -->
      <div class="row">
          <?php
          //Coordinator
          if (isset($_SESSION["isCord"]) && $_SESSION["isCord"] = 1){
              $num_of_batch = $conn->query("SELECT batchId FROM batch WHERE isActive = 1 ")->num_rows;
              $num_of_students = $conn->query("SELECT studentId FROM student ")->num_rows;
              $num_of_groups = $conn->query("SELECT groupId FROM student_group JOIN batch ON student_group.batchId = batch.batchId WHERE batch.isActive = 1 ")->num_rows;
              $num_of_supervisor = $conn->query("SELECT * FROM faculty JOIN work_load ON faculty.facultyId = work_load.facultyId WHERE totalLoad > 0")->num_rows;
              ?>
              <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-aqua">
                      <div class="inner">
                          <h3><?php echo $num_of_batch;?></h3>

                          <p>Batch Active</p>
                      </div>
                      <div class="icon">
                          <i class="ion ion-university"></i>
                      </div>
                      <a href="./manageBatch.php" class="small-box-footer" target="_blank">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-green">
                      <div class="inner">
                          <h3><?php echo $num_of_students;?></h3>

                          <p>Students Registered</p>
                      </div>
                      <div class="icon">
                          <i class="ion ion-person-add"></i>
                      </div>
                      <a href="./manageStudents.php" class="small-box-footer" target="_blank">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-yellow">
                      <div class="inner">
                          <h3><?php echo $num_of_groups;?></h3>

                          <p>Groups Created</p>
                      </div>
                      <div class="icon">
                          <i class="ion ion-ios-people"></i>
                      </div>
                      <a href="./manageGroups.php" class="small-box-footer" target="_blank">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-red">
                      <div class="inner">
                          <h3><?php echo $num_of_supervisor;?></h3>

                          <p>Supervisors</p>
                      </div>
                      <div class="icon">
                          <i class="ion-ios-personadd"></i>
                      </div>
                      <a href="./manageFaculty.php" class="small-box-footer" target="_blank">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
              </div>
              <!-- ./col -->
              <?php
          }
          ?>
        <div class="col-md-12">



            <?php
            if (isset($_SESSION['usrCMS'])){
               require_once ('studentTimeline.php');
            }
            if (isset($_SESSION['facultyId'])){
                require_once ('facultyTimeline.php');
            }

            ?>

        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <!--  Main Footer contains copyright and credits-->

<?php
require_once("includes/main-footer.php");
?>
</div>
<!-- ./wrapper -->
<?php
require_once("includes/required_js.php");
?>
</body>
</html>

<?php
$title="FYPMS";
$subtitle="Project Templates";
require_once("includes/header.php");
require_once("includes/config.php");
require_once("includes/functions.php");
session_start();

if(!isset($_SESSION["usrCMS"]))
{
    header('Location: '.'index.php');
}


$groupId = $_SESSION['GroupID'];
$batchId = $_SESSION["BatchID"];
if ($batchId){
    $batchName = $conn->query("SELECT batchName FROM batch WHERE batchId = '$batchId' ")->fetch_object()->batchName;
}
//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {




}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {





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
                <div class="col-sm-1"></div>
                <div class="col-md-10">
                    <!-- general form elements -->
                    <div class="box no-border">
                        <div class="box-header with-border">
                            <h3 class="box-title">Batch - <?php echo $batchName;?></h3>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">
                            <table class="table table-hover">
                            <?php
                            $sql = "SELECT * FROM batch_templates WHERE batch_templates.batchId = '$batchId' ";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // output data of each row

                                while($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <h4>
                                            <i class="<?php get_icon($row['templateLocation'])?>" ></i>
                                            <a href="<?php echo 'uploads/'.$batchName.'/templates/'.$row['templateLocation'];?>">
                                                <?php echo $row['templateName'];?>
                                            </a>

                                        </h4>
                                    </tr>
                                <?php
                                }
                            } else { ?>
                                <h5>No templates available.</h5>
                            <?php
                            }

                            ?>
                            </table>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">

                        </div>

                    </div>
                    <!-- /.box -->







                </div>
                <div class="col-sm-1"></div>
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
</body>
<?php
//TODO send meeting logs to student notifications
$title="FYPMS";
$subtitle="Meeting Logs";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if supervisor is logged in
if(!isset($_SESSION["usrCMS"]))
{
    header('Location: '.'index.php');
}
$studentId = $_SESSION['usrId'];
//Getting groupId
$groupId = $conn->query("SELECT groupId FROM student WHERE studentId = '$studentId' LIMIT 1" )->fetch_object()->groupId;



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {




}



?>


<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper" >
        <?php require_once("includes/content-header.php"); ?>

        <section class="content" style="min-height: 700px">
            <div class="row">
                <div class="col-md-12">

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

                    <div class="box no-border">
                        <div class="box-header">
                            <h3 class="box-title">Logs</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <table class="table">
                                <tr>
                                    <th>Meeting Title</th>
                                    <th>Meeting Time <i class="fa fa-clock-o" aria-hidden="true"></i></th>
                                    <th>Comments</th>
                                    <th style="width: 40px">Status</th>
                                </tr>
                                <?php
                                $sql = "SELECT * FROM meeting_logs WHERE group_id ='$groupId' ";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['meeting_title']; ?></td>
                                            <td><?php echo $row['meeting_dtm']; ?></td>
                                            <td><?php echo $row['comments']; ?></span></td>
                                            <th><?php
                                                $status =$row['meeting_status'];
                                                if ($status == 'Pending'){ ?>
                                                    <span class="label label-warning"><?php echo $status?></span>

                                                    <?php
                                                }
                                                else if ($status == 'Done'){ ?>
                                                    <span class="label label-success"><?php echo $status?></span>
                                                    <?php
                                                }
                                                else if ($status == 'Cancelled'){ ?>
                                                    <span class="label label-danger"><?php echo $status?></span>

                                                    <?php
                                                }
                                                else if ($status == 'Postponed'){ ?>
                                                    <span class="label label-primary"><?php echo $status?></span>

                                                    <?php
                                                }else{ ?>
                                                    <span class="label label-default"><?php echo $status?></span>

                                                    <?php
                                                }
                                                ;?></th>
                                        </tr>
                                    <?php
                                    }
                                }
                                ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->



                </div>

            </div>
        </section>
    </div><!-- ./content-wrapper -->
    <!--</div>-->
    <?php
    require_once("includes/main-footer.php");
    ?>
</div>
<!-- ./wrapper -->
<?php
require_once("includes/required_js.php");
?>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
    function goBack() {
        window.history.back();
    }

    $(document).ready(function() {

        $('.textarea').wysihtml5();

        $('#meetingLogs').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false
        });
    } );
</script>


</body>
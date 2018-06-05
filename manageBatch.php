<?php
$title="FYPMS";
$subtitle="Manage Batch";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

if(!isset($_SESSION["isCord"]))
{
    header('Location: '.'index.php');
}



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {





}



?>
<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">

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
                        else if ($_GET['status'] == 'n'){ ?>
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

                    <!-- general form elements -->
                    <div class="box no-border">
                        <div class="box-header with-border">
                            <h3 class="box-title">List of Batch</h3>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">
                            <table class="table" >
                                <tr>
                                    <th>Batch Name</th>
                                    <th>SDP Part</th>
                                    <th>Registered Students</th>
                                    <th>Start Date</th>
                                    <th>Status</th>
                                    <th >Actions</th>
                                </tr>
                                <?php
                                $sql = "SELECT * FROM batch";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['batchName']; ?></td>
                                            <td><?php echo $row['sdpPart']; ?></td>
                                            <td><?php
                                                $batchId = $row['batchId'];
                                                echo $conn->query("SELECT studentId FROM student WHERE batchId ='$batchId' ")->num_rows;  ?>
                                            </td>
                                            <td><?php echo $row['startingDate']; ?></td>
                                            <td><?php if ($row['isActive'] == 1){
                                                    echo "<span class=\"label label-success\">Active</span>";
                                                }else if ($row['isActive'] == 0){
                                                    echo "<span class=\"label label-danger\">Inactive</span>";
                                                }  ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo siteroot."batchReport.php?id=".$row['batchId'] ;?>" class="btn btn-default btn-flat btn-sm" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i> Show Report</a>
                                            </td>
                                        </tr>

                                        <?php
                                    }
                                } ?>
                            </table>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">

                        </div>

                    </div>
                    <!-- /.box -->







                </div>

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
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#manageStudents').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ],
            "pageLength": 15,
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false
        });


    } );


</script>

</body>
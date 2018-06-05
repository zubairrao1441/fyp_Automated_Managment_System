<?php
$title="FYPMS";
$subtitle="Manage Groups";
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
                                Error! This faculty is supervising a group. Can not delete this
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
                            <h3 class="box-title">List of Groups</h3>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">
                            <table id="manageGroups" class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Batch</th>
                                    <th>Project Name</th>
                                    <th>Group Members</th>
                                    <th>Actions</th>

                                </tr>
                                </thead>
                                <?php
                                $sql = "SELECT * FROM student JOIN student_group ON student.studentId = student_group.leaderId JOIN batch ON batch.batchId = student_group.batchId WHERE isLeader = 1 AND batch.isActive = 1";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['batchName'];?></td>
                                            <td><?php echo $row['projectName'];?></td>
                                            <td>
                                                <?php
                                                $groupId = $row['groupId'];
                                                $groupMembers = $conn->query("SELECT * FROM student WHERE groupId = '$groupId' ");
                                                if ($groupMembers -> num_rows > 0){
                                                    while($member = $groupMembers->fetch_assoc()){ ?>
                                                        <a href="<?php echo siteroot."studentProfile.php?id=".$member['studentId'] ;?>" target="_blank"><?php  echo $member['studentName']. " [" .$row['studentCMS']. " ]"."<br/>"; ?></a>
                                                    <?php
                                                    }
                                                }

                                                ;?>
                                            </td>

                                            <td>
                                                <a href="<?php echo siteroot."groupReport.php?id=".$row['groupId'] ;?>" class="btn btn-default btn-flat btn-sm" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i> Show Report</a>
                                            </td>
                                        </tr>

                                    <?php
                                    }
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
        $('#manageGroups').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ],
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
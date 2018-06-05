<?php
$title="FYPMS";
$subtitle="Project Repository";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

/******************************************************
 * Check if repository user of Coordinator is logged in
 *****************************************************/


//Check if COORDINATOR is logged in else log out
if(!isset($_SESSION["isCord"]))
{
    header('Location: '.'index.php');
}



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['btnDownload'])){

        $downloadID = filter_input(INPUT_POST,'downloadId',FILTER_SANITIZE_NUMBER_INT);
        //uploads/batch Name/group id/deliverable naem

        $sql = "SELECT * FROM group_uploads WHERE id = '$downloadID' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $deliverableName = $row['uploadFile'];
                $groupId = $row['groupId'];
            }
        }

        //Getting batchId,batch Name from groupId
        $batchId = $conn->query("SELECT batchId FROM student_group WHERE groupId = '$groupId' ")->fetch_object()->batchId;
        $batchName = $conn->query("SELECT batchName FROM batch WHERE batchId = '$batchId' ")->fetch_object()->batchName;

        $group = 'Group '.$groupId;

        $location = siteroot."uploads/".$batchName."/".$group."/".$deliverableName;

        //Download file
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Type: application/pdf');

        header('Content-Disposition: attachment; filename="'. basename($location) . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($location));

        readfile($location);



    }



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


                <?php if (isset($_GET['details']) && is_numeric($_GET['details']) && strlen($_GET['details'])>0){
                        $groupId = filter_input(INPUT_GET,'details',FILTER_SANITIZE_NUMBER_INT);
                        if (isset($groupId) && is_numeric($groupId) && strlen($groupId)>0){
                            $projectName = $conn->query("SELECT projectName FROM student_group WHERE groupId = '$groupId' LIMIT 1")->fetch_object()->projectName;
                        }else{
                            $projectName="--";
                        }



                    ?>
                    <!--DETAILS-->
                    <div class="box no-border">
                        <div class="box-header">
                            <h3><?php echo $projectName;?></h3>

                            <h3 class="box-title">Members</h3>

                            <div class="box-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <a href="<?php echo siteroot."groupReport.php?id=".$_GET['details'];?>" target="_blank" class="btn btn-default btn-flat pull-right"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>  Show Report</a>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="" class="table table-striped">
                                <thead>
                                <tr>
                                    <th>CMS</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $groupId = filter_input(INPUT_GET,'details',FILTER_SANITIZE_NUMBER_INT);

                                $sql = "SELECT * FROM student WHERE groupId = '$groupId'";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) { ?>
                                        <form name="detailForm" id="detailForm" method="post" data-toggle="validator">
                                            <input type="hidden" name="downloadId" value="<?php echo $row['id'];?>" >
                                            <tr>
                                                <td><?php echo $row['studentCMS']; ?></td>
                                                <td><?php echo $row['studentName'];?></td>
                                                <td><?php echo $row['studentEmail'];?></td>
                                                <td><?php echo $row['studentPhoneNo'];?></td>
                                            </tr>
                                        </form>
                                        <?php
                                    }
                                }

                                ?>

                                </tbody>

                            </table>

                        </div>
                        <div class="box no-border">
                            <div class="box-header">
                                <h3 class="box-title">Group Uploads</h3>

                            </div>

                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Deliverable</th>
                                        <th>Uploaded by</th>
                                        <th>Uploaded <i class="fa fa-clock-o"></i></th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    $groupId = filter_input(INPUT_GET,'details',FILTER_SANITIZE_NUMBER_INT);

                                    $sql = "SELECT * FROM group_uploads  JOIN student ON student.studentId = group_uploads.uploadedBy WHERE group_uploads.groupId = '$groupId' LIMIT 1 ";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // output data of each row
                                        while($row = $result->fetch_assoc()) { ?>
                                            <form name="detailForm" id="detailForm" method="post">
                                                <input type="hidden" name="downloadId" value="<?php echo $row['id'];?>" >
                                                <tr>
                                                    <td><?php $taskId = $row['taskId'];
                                                        echo $conn->query("SELECT taskName FROM batch_tasks WHERE taskId = '$taskId' ")->fetch_object()->taskName;?>
                                                    </td>
                                                    <td><a href="<?php echo siteroot."studentProfile.php?id=".$row['studentId']; ?>" target="_blank"><?php echo $row['studentName'];?></a></td>
                                                    <td><?php echo $row['uploadedDtm'];?></td>
                                                    <td>

                                                        <button type="submit" name="btnDownload"  class="btn btn-default btn-sm"><i class="fa fa-download"></i> Download</button>
                                                    </td>
                                                </tr>
                                            </form>
                                            <?php
                                        }
                                    }

                                    ?>

                                    </tbody>

                                </table>

                            </div>

                            <div class="box no-border">
                                <div class="box-header">
                                    <h3 class="box-title">Meeting Logs</h3>

                                </div>

                                <!-- /.box-header -->
                                <div class="box-body">
                                    <table id="" class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Meeting Title</th>
                                            <th><i class="fa fa-clock-o"></i> Date Time</th>
                                            <th>Comments</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        $groupId = filter_input(INPUT_GET,'details',FILTER_SANITIZE_NUMBER_INT);

                                        $sql = "SELECT * FROM meeting_logs WHERE group_id = '$groupId' ";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            // output data of each row
                                            while($row = $result->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo $row['meeting_title'];?></td>
                                                        <td><?php echo $row['meeting_dtm'];?></td>
                                                        <td><?php echo $row['comments'];?></td>
                                                        <td><?php echo $row['meeting_status'];?></td>
                                                    </tr>
                                                <?php
                                            }
                                        }

                                        ?>

                                        </tbody>

                                    </table>

                                </div>



                        <!-- /.box-body -->

                        <div class="box-footer">
                            <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn btn-default">Back</a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!-- /.box -->


                    <?php
                }else{ ?>
                    <div class="box no-border">
                        <div class="box-header">
                            <h3 class="box-title">List of Projects</h3>
                            <?php
                            if(isset($_SESSION["isCord"])){ ?>
                                <div class="box-tools">
                                    <a href="./repositoryAccess.php" target="_blank" class="btn btn-default btn-xs "><i class="fa fa-external-link" aria-hidden="true"></i> Give Access</a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="projectRepository" class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Batch</th>
                                    <th>Group</th>
                                    <th>Project Name</th>
                                    <th>Supervisor</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql = "SELECT * FROM project_repository JOIN batch ON batch.batchId = project_repository.batchId JOIN student_group ON student_group.batchId = project_repository.batchId JOIN faculty_student_group ON faculty_student_group.groupId = student_group.groupId JOIN faculty ON faculty.facultyId = faculty_student_group.facultyId";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['batchName'];?></td>
                                            <td><?php echo "Group # ".$row['groupId'];?></td>
                                            <td><?php echo $row['projectName'];?></td>
                                            <td><?php echo $row['facultyName'];?></td>
                                            <td>
                                                <a href="<?php echo $_SERVER['PHP_SELF'] . "?details=".$row['groupId']?>" class="btn btn-default btn-sm">Details</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }

                                ?>

                                </tbody>

                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                <?php
                } ?>






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

<!-- page script -->
<script>
    $(function () {
        $('#projectRepository').DataTable({
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

        $('#projectDetials').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ],
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false
        });
    });
</script>

</body>
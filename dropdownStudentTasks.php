<?php
require_once('includes/config.php');
require_once('includes/functions.php');


if (isset($_SESSION["usrId"])) {
    $studentId = $_SESSION['usrId'];
    $batchId = $_SESSION['BatchID'];
}
//Getting group id
$groupId = $conn->query("SELECT groupId FROM student WHERE studentId = '$studentId' LIMIT 1" )->fetch_object()->groupId;


$sql = "SELECT * FROM batch_tasks WHERE batchId ='$batchId'  ORDER BY createdDtm DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $num_of_tasks = $result->num_rows;
    while ($row = $result->fetch_assoc()) { ?>
        <li class="dropdown tasks-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-flag-o"></i>
                <span class="label label-danger"><?php echo $num_of_tasks;?></span>
            </a>
            <ul class="dropdown-menu">
                <li class="header">You have <?php echo $num_of_tasks;?> task(s)</li>
                <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                        <li><!-- Task item -->
                            <a href="<?php echo "studentTasks.php?details=".$row['taskId'];?>" target="_blank">
                                <h3>
                                    <?php echo $row['taskName'];?>
                                    <?php if (check_group_uploads($groupId,$row['taskId'],$batchId)){ ?>
                                        <small class="label label-success pull-right">DONE</small>
                                    <?php
                                    }else{ ?>
                                        <small class="label label-warning pull-right">Pending</small>
                                    <?php
                                    }?>
                                </h3>
                            </a>
                        </li>
                        <!-- end task item -->
                    </ul>
                </li>
                <li class="footer">
                    <a href="studentTasks.php" target="_blank">View all tasks</a>
                </li>
            </ul>
        </li>

    <?php
    }
}

?>
<!-- Tasks: style can be found in dropdown.less -->


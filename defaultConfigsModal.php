<?php require_once ('includes/functions.php')?>
<!-- /.row -->
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-header">
                <h3 class="box-title">Senior Design Project Part - I</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-condensed">
                    <tr>
                        <th>Week</th>
                        <th>Task Name</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th>Attachment</th>
                    </tr>

                    <?php
                    global $conn;
                    $sql = "SELECT * FROM configurations WHERE projectPart='1' AND configurationType='default' ORDER BY week";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['week'];?></td>
                        <td><?php echo $row['taskName'];?></td>
                        <td><?php echo getExcerpt($row['taskDetails'],0,50);?></td>
                        <td><?php echo $row['deadline'];?></td>
                        <td><?php echo $row['attachment'];?></td>
                        <?php } ?>

                    </tr>
                </table>
            </div>
            <!-- /.box-body -->
            <div class="box-header">
                <h3 class="box-title">Senior Design Project Part - II</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-condensed">
                    <tr>
                        <th>Week</th>
                        <th>Task Name</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th>Attachment</th>
                    </tr>

                    <?php
                    global $conn;
                    $sql = "SELECT * FROM configurations WHERE projectPart='2' AND configurationType='default' ORDER BY week";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['week'];?></td>
                        <td><?php echo $row['taskName'];?></td>
                        <td><?php echo getExcerpt($row['taskDetails'],0,50);?></td>
                        <td><?php echo $row['deadline'];?></td>
                        <td><?php echo $row['attachment'];?></td>
                        <?php } ?>

                    </tr>
                </table>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>
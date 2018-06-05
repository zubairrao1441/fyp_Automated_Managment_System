
<?php if (isset($_GET['details']) && is_numeric($_GET['details']) && strlen($_GET['details'])>0){
   
    $detailsId = filter_input(INPUT_GET,'details',FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT * FROM timeline_student WHERE id='$detailsId' LIMIT 1 ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $title = $row['title'];
        $details = $row['details'];
        $type = $row['type'];
        $createdDtm = $row['createdDtm'];

    }

    }
?>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title;?></h4>
            </div>
            <div class="modal-body">

                <?php echo $details;?>
                <p class="text-muted"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo time2str($createdDtm);?></p>

                <br/>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script>
        // A $( document ).ready() block.
        $( document ).ready(function() {
            $('#myModal').modal('show');
        });
    </script>
    
<?php } ?>

<ul class="timeline">
    <?php
    require_once ("includes/functions.php");
    require_once("includes/config.php");
    //session_start();

    //If student is logged in
    if (isset($_SESSION['usrCMS'])){

        $batchId = $_SESSION['BatchID'];
        

    }
    

    //Get Values from Database
    $sql = "SELECT * FROM timeline_student WHERE batchId ='$batchId' ORDER BY createdDtm DESC  ";//Chronoligical Order
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) { ?>

        <!-- timeline time label -->
        <li class="time-label">
                  <span class="bg-gray">
                      <?php echo date('F d, Y ',strtotime($row["createdDtm"])); ?>
                  </span>
        </li>
        <!-- /.timeline-label -->
        <li>
            <i class="fa fa-info bg-blue"></i>

            <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i>
                <?php echo time2str($row["createdDtm"]); ?>
            </span>
                <h3 class="timeline-header"><?php echo $row["title"] ;?></h3>

                <div class="timeline-body">
                    <?php

                    if (strlen($row["details"] >= '500')){

                        echo getExcerpt($row["details"],0,500)  ;
                    }
                    else{

                        echo $row["details"] ;
                    }

                    ?>
                </div>
                <div class="timeline-footer">
                    <?php if (strlen($row["details"] >= '500')){ ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'].'?details='.$row['id'];?>"  class="btn btn-primary btn-xs">Show Details</a>
<!--                        <button  class="btn btn-primary btn-xs">Show Details</button>-->
                    <?php } ?>

                </div>
            </div>
        </li>
        <!-- END timeline item -->
        <!-- timeline item -->


        <?php
    }
    ?>
    <li>
        <i class="fa fa-clock-o bg-gray"></i>
    </li>

</ul>

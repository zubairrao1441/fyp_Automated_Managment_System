<ul class="timeline">
<?php
require_once ("includes/functions.php");
require_once("includes/config.php");
session_start();

//If student is logged in
if (isset($_SESSION['usrCMS'])){
    //$batchId = $_SESSION['BatchID'];
    //$whereSQL = "WHERE batch_id = '$batchId' ";
}


// check for optional where clause
$whereSQL = 'WHERE is_active = 1 ';
if (!empty($where_clause)) {
    // check to see if the 'where' keyword exists
    if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
        // not found, add keyword
        $whereSQL = " WHERE " . $where_clause;
    } else {
        $whereSQL = " " . trim($where_clause);
    }
}

//Get Values from Database
$sql = "SELECT * FROM timeline ".$whereSQL." ORDER BY created_dtm DESC  ";//Chronoligical Order
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) { ?>

    <!-- timeline time label -->
    <li class="time-label">
                  <span class="bg-gray">
                      <?php echo date('F d, Y ',strtotime($row["created_dtm"])); ?>
                  </span>
    </li>
    <!-- /.timeline-label -->
    <li>
        <i class="fa fa-info bg-blue"></i>

        <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i>
                <?php echo time2str($row["created_dtm"]); ?>
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
                    <a href="<?php echo $_SERVER["PHP_SELF"]."?details=".$row["item_id"];?>" class="btn btn-primary btn-xs">Read more</a>

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

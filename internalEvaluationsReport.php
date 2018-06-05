<?php
$title="FYPMS";
$subtitle="Internal Demo Evaluations";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();

//Check if Coordinator is logged in

if(!isset($_SESSION["isCord"]))
{
    header('Location: '.'index.php');
}
$facultyId = $_SESSION['facultyId'];



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_POST['internal_group'])){

    }


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {




}




?>
<!--KendoUI-->
<link href="plugins/kendo-ui/styles/kendo.common.min.css" rel="stylesheet">
<link href="plugins/kendo-ui/styles/kendo.rtl.min.css" rel="stylesheet">
<link href="plugins/kendo-ui/styles/kendo.default.min.css" rel="stylesheet">
<link href="plugins/kendo-ui/styles/kendo.dataviz.min.css" rel="stylesheet">
<link href="plugins/kendo-ui/styles/kendo.dataviz.default.min.css" rel="stylesheet">
<script src="plugins/kendo-ui/js/jquery.min.js"></script>
<script src="plugins/kendo-ui/js/jszip.min.js"></script>
<script src="plugins/kendo-ui/js/kendo.all.min.js"></script>

</head>


<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper" >
        <?php require_once("includes/content-header.php"); ?>

        <section class="content" style="min-height: 700px">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <div class="box no-padding no-border">
                        <div class="box-body ">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="text-center">
                                    <button class='export-pdf btn btn-defualt btn-flat '><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export as PDF</button>
                                    <button class='export-img btn btn-defualt btn-flat '><i class="fa fa-file-image-o" aria-hidden="true"></i> Export as Image</button>
                                    <button class='anon-faculty btn btn-defualt btn-flat  '><i class="fa fa-eye" aria-hidden="true"></i> Anonymize Faculty</button>
                                </div>
                            </div>

                        </div>

                    </div>

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
                        else if ($_GET['status'] == 'validation_err'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! Please fill all the required fields correctly
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

                    <div class="internal-report">

                        <!-- general form elements -->
                        <div class="box no-border">
                            <div class="box-header ">

                                <h3 class="box-title">Internal Demo Results BSSE Final Year Projects (Part II)</h3>
                                <br>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">
                                <?php
                                $sql_groups = "SELECT * FROM faculty_student_group JOIN student_group ON faculty_student_group.groupId = student_group.groupId JOIN faculty ON faculty_student_group.facultyId = faculty.facultyId";
                                $result_groups = $conn->query($sql_groups);

                                if ($result_groups->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result_groups->fetch_assoc()) {

                                        $groupId = $row['groupId']; ?>
                                        <strong><?php echo $row['projectName']." - Supervised by: ".$row['facultyName'];?></strong>
                                        <table class="table table-condensed">
                                            <tr>
                                                <th>Faculty</th>
                                                <th style="width: 10px">AE</th>
                                                <th style="width: 10px">OH</th>
                                            </tr>
                                            <?php
                                            $sql = "SELECT * FROM internal_evaluations JOIN faculty ON internal_evaluations.votedBy = faculty.facultyId WHERE internal_evaluations.groupId = '$groupId' ";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
                                                // output data of each row
                                                while($row = $result->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td class="faculty-name"><?php echo $row['facultyName']; ;?></td>
                                                        <td><?php
                                                            if ($row['aeVote'] <= 0){ ?>
                                                                <span class="label label-danger"><?php echo $row['aeVote'];?></span>
                                                            <?php
                                                            }else if ($row['aeVote'] > 0){ ?>
                                                                <span class="label label-success"><?php echo $row['aeVote'];?></span>
                                                            <?php
                                                            }else{
                                                                echo $row['aeVote'];
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php
                                                            if ($row['ohVote'] <= 0){ ?>
                                                                <span class="label label-danger"><?php echo $row['ohVote'];?></span>
                                                                <?php
                                                            }else if ($row['ohVote'] > 0){ ?>
                                                                <span class="label label-success"><?php echo $row['ohVote'];?></span>
                                                                <?php
                                                            }else{
                                                                echo $row['ohVote'];
                                                            }
                                                            ?></td>
                                                    </tr>

                                                    <?php

                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td ><strong>Total</strong></td>
                                                <td><?php
                                                    $aeTotal = $conn->query("SELECT sum(aeVote) AS totalAE FROM internal_evaluations WHERE groupId = '$groupId'")->fetch_object()->totalAE;
                                                    if ($aeTotal <= 0){ ?>
                                                        <span class="label label-danger"><?php echo $aeTotal;?></span>
                                                        <?php
                                                    }else if ($aeTotal > 0){ ?>
                                                        <span class="label label-success"><?php echo $aeTotal;?></span>
                                                        <?php
                                                    }else{
                                                        echo $aeTotal;
                                                    }
                                                    ;?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $ohTotal = $conn->query("SELECT sum(ohVote) AS totalOH FROM internal_evaluations WHERE groupId = '$groupId'")->fetch_object()->totalOH;
                                                    if ($ohTotal <= 0){ ?>
                                                        <span class="label label-danger"><?php echo $ohTotal;?></span>
                                                        <?php
                                                    }else if ($ohTotal > 0){ ?>
                                                        <span class="label label-success"><?php echo $ohTotal;?></span>
                                                        <?php
                                                    }else{
                                                        echo $ohTotal;
                                                    }
                                                    ;?>

                                                </td>

                                            </tr>


                                        </table>
                                        <br><br>


                                    <?php
                                    }
                                } else {
                                    echo "--";
                                }
                                ?>








                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td >Strong Reject</td>
                                                <td >SR</td>
                                                <td  >-2</td>
                                            </tr>
                                            <tr>
                                                <td>Weak Reject</td>
                                                <td>SR</td>
                                                <td >-1</td>
                                            </tr>
                                            <tr>
                                                <td>Weak Accept</td>
                                                <td>SR</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>Strong Accept</td>
                                                <td>SR</td>
                                                <td >2</td>
                                            </tr>
                                        </table>

                                    </div>
                                    <div class="col-md-6 pull-right">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td>Allow External Exam</td>
                                                <td>AE</td>
                                            </tr>
                                            <tr>
                                                <td>Allow Participation in Open House</td>
                                                <td>OH</td>
                                            </tr>


                                        </table>

                                    </div>
                                </div>








                            </div>
                        <!-- /.box -->

                    </div>





                </div>
                <div class="col-md-1"></div>

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
<!--PAGE SCRIPT-->

<script>
    // Import DejaVu Sans font for embedding

    // NOTE: Only required if the Kendo UI stylesheets are loaded
    // from a different origin, e.g. cdn.kendostatic.com
    kendo.pdf.defineFont({
        "DejaVu Sans"             : "//kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans.ttf",
        "DejaVu Sans|Bold"        : "//kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans-Bold.ttf",
        "DejaVu Sans|Bold|Italic" : "//kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans-Oblique.ttf",
        "DejaVu Sans|Italic"      : "//kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans-Oblique.ttf"
    });
</script>

<!-- Load Pako ZLIB library to enable PDF compression -->
<script src="plugins/kendo-ui/js/pako_deflate.min.js"></script>
<script>
    $(document).ready(function() {

        $(".export-pdf").click(function() {
            // Convert the DOM element to a drawing using kendo.drawing.drawDOM
            kendo.drawing.drawDOM($(".internal-report"))
                .then(function(group) {
                    // Render the result as a PDF file
                    return kendo.drawing.exportPDF(group, {
                        paperSize: "auto",
                        margin: { left: "1cm", top: "1cm", right: "1cm", bottom: "1cm" }
                    });
                })
                .done(function(data) {
                    // Save the PDF file
                    kendo.saveAs({
                        dataURI: data,
                        fileName: "internal-report.pdf",
                        //proxyURL: "//demos.telerik.com/kendo-ui/service/export"
                    });
                });
        });

        $(".export-img").click(function() {
            // Convert the DOM element to a drawing using kendo.drawing.drawDOM
            kendo.drawing.drawDOM($(".internal-report"))
                .then(function(group) {
                    // Render the result as a PNG image
                    return kendo.drawing.exportImage(group);
                })
                .done(function(data) {
                    // Save the image file
                    kendo.saveAs({
                        dataURI: data,
                        fileName: "internal-report.png",
                        //proxyURL: "//demos.telerik.com/kendo-ui/service/export"
                    });
                });
        });

        $(".anon-faculty").click(function() {
            $( ".faculty-name" ).replaceWith( "xxxxx" );
            alert("To show faculty names,refresh page")
        });





    });
</script>
</body>
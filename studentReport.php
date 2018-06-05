<?php
$title="FYPMS";
$subtitle="Student Report";
require_once("includes/header.php");
require_once("includes/config.php");
require_once ("includes/functions.php");
session_start();

//Check if coordinator is logged in
if(!isset($_SESSION["isCord"]))
{
    header('Location: '.'index.php');
}
$facultyName = "--";
$facultyEmail = "--";
$examinerName = "--";
$examinerEmail = "--";

//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $studentId = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);

    //Student Details
    $sql = "SELECT * FROM student WHERE  studentId = '$studentId' LIMIT 1 ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $cms = $row['studentCMS'];
            $name = $row['studentName'];
            $email = $row['studentEmail'];
            $contact = $row['studentPhoneNo'];
            $image = $row['studentImage'];
            $groupId = $row['groupId'];
        }
    } else {
        $cms = "--";
        $name = "--";
        $email = "--";
        $contact = "--";
        $image = "--";
        $groupId = "--";
    }

    //Getting student group details
    $sql = "SELECT * FROM student_group WHERE  groupId = '$groupId' LIMIT 1 ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $projectName = $row['projectName'];
        }
    } else {
        $projectName = "--";
    }

    //Getting student grades Part 1
    $sql = "SELECT * FROM grades WHERE sdpPart = 1 AND studentId = '$studentId' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $facultyId = $row['gradedBy'];
            $gradePt1 = $row['grade'];
        }
    } else {
        $gradedByPt1 = "--";
        $gradePt1 = '--';
    }

    if (isset($facultyId) && is_numeric($facultyId)){
        //Getting name &email of supervisor of graded by
        $sql = "SELECT facultyName,facultyEmail FROM faculty WHERE facultyId = '$facultyId' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $facultyName = $row['facultyName'];
                $facultyEmail = $row['facultyEmail'];
            }
        }

    }




    //Getting student grades Part 2
    $sql = "SELECT * FROM grades WHERE sdpPart = 2 AND studentId = '$studentId' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $examinerId = $row['gradedBy'];
            $gradePt2 = $row['grade'];
        }
    } else {
        $examinerId = "--";
        $gradePt2 = '--';
    }

    if (isset($examinerId) && is_numeric($examinerId) ){
        //Getting name &email of examiner of graded by
        $sql = "SELECT examinerName,examinerEmail FROM external_examiner WHERE examinerId = '$examinerId' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $examinerName = $row['examinerName'];
                $examinerEmail = $row['examinerEmail'];
            }
        }

    }
}


?>
<style>
    body {
        background: rgb(204,204,204);
    }
    page {
        background: white;
        display: block;
        margin: 0 auto;
        margin-bottom: 0.5cm;
        box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
    }
    page[size="A4"] {
        width: 21cm;
        height: 29.7cm;
    }
    page[size="A4"][layout="portrait"] {
        width: 29.7cm;
        height: 21cm;
    }
    page[size="A3"] {
        width: 29.7cm;
        height: 42cm;
    }
    page[size="A3"][layout="portrait"] {
        width: 42cm;
        height: 29.7cm;
    }
    page[size="A5"] {
        width: 14.8cm;
        height: 21cm;
    }
    page[size="A5"][layout="portrait"] {
        width: 21cm;
        height: 14.8cm;
    }
    @media print {
        body, page {
            margin: 0;
            box-shadow: 0;
        }
    }
</style>
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
<body>

<!-- general form elements -->
<div class="box no-padding">
    <div class="box-body ">
        <div class="col-md-6 col-md-offset-3">
            <div class="text-center">
                <button class='export-pdf btn btn-defualt btn-flat btn-lg'><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export as PDF</button>
                <button class='export-img btn btn-defualt btn-flat btn-lg'><i class="fa fa-file-image-o" aria-hidden="true"></i> Export as Image</button>
            </div>
        </div>

    </div>

</div>
<!-- /.box -->


<page size="A4" class="groupReport">
    <section class="invoice">
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <img src="./img/logo_type.png" alt="fyp_logo" length="52" width="250">
                    <small class="pull-right">Date: <?php echo date("d/m/Y");?></small>
                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row ">
            <div class="col-sm-6 col-md-offset-3 ">
                <h4 class="text-center">Student Report : <?php echo $name;?>  </h4><br>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row" >
            <div class="col-md-6 ">
                <p class="text-left">CMS : <?php echo $cms; ?></p>
                <p class="text-left">Name : <?php echo $name; ?></p>
                <p class="text-left">Email : <?php echo $email; ?></p>
            </div>
            <div class="col-md-6">
                <?php if (isset($image) && $image != "--"){ ?>
                    <img src="<?php echo siteroot."/public/profile_images/".$image?>"  class="pull-right profile-user-img img-responsive img-rounded" alt="student_image" width="100" height="100">
                <?php
                } ?>

            </div>
        </div>
        <!-- /.row -->


        <br>
        <!-- Table row -->
        <strong>Project: <?php echo $projectName;?></strong>
        <br>

        <div class="row">
            <div class="col-xs-12 table-responsive">
                <p>Group Members:</p>
                <table class="table ">
                    <thead>
                    <tr>
                        <th style="width:40px;">CMS</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT * FROM student WHERE student.groupId = '$groupId' AND studentId !='$studentId' ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['studentCMS'];?></td>
                                <td><?php echo $row['studentName'];?></td>
                                <td><?php echo $row['studentEmail'];?></td>
                                <td><?php echo $row['studentPhoneNo'];?></td>
                            </tr>

                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <h5>Grades</h5>
        <div class="row">


            <div class="col-lg-6 pull-left">
                <strong>SDP Part - 1 </strong>
                <h1><?php echo $gradePt1;?></h1>
                <p >Graded by: <?php echo $facultyName;?></p>
                <p><?php echo $facultyEmail;?></p>
            </div>


            <div class="col-lg-6 pull-right">
                <strong>SDP Part - 2 </strong>
                <h1 ><?php echo $gradePt2;?></h1>
                <p >Graded by: <?php echo $examinerName;?></p>
                <p><?php echo $examinerEmail;?></p>
            </div>
            <!-- /.col -->

        </div>
        <!-- /.row -->

        <br><br><br>


    </section>
</page>


</div>
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
        kendo.drawing.drawDOM($(".groupReport"))
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
                    fileName: "student-report.pdf",
                    //proxyURL: "//demos.telerik.com/kendo-ui/service/export"
                });
            });
    });

    $(".export-img").click(function() {
        // Convert the DOM element to a drawing using kendo.drawing.drawDOM
        kendo.drawing.drawDOM($(".groupReport"))
            .then(function(group) {
                // Render the result as a PNG image
                return kendo.drawing.exportImage(group);
            })
            .done(function(data) {
                // Save the image file
                kendo.saveAs({
                    dataURI: data,
                    fileName: "student-report.png",
                    //proxyURL: "//demos.telerik.com/kendo-ui/service/export"
                });
            });
    });

    $(".export-svg").click(function() {
        // Convert the DOM element to a drawing using kendo.drawing.drawDOM
        kendo.drawing.drawDOM($(".groupReport"))
            .then(function(group) {
                // Render the result as a SVG document
                return kendo.drawing.exportSVG(group);
            })
            .done(function(data) {
                // Save the SVG document
                kendo.saveAs({
                    dataURI: data,
                    fileName: "student-report.svg",
                    //proxyURL: "//demos.telerik.com/kendo-ui/service/export"
                });
            });
    });





});
</script>

</body>


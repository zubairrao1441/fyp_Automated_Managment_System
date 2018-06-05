<?php
require_once ('includes/config.php');
session_start();

//

generate_pdf($_SESSION['batch_id']);

function generate_pdf($batchId){

    define('FPDF_FONTPATH','libs/fpdf/font/');
    require('libs/fpdf/mysql_report.php');

    // the PDF is defined as normal, in this case a Portrait, measurements in points, A4 page.
    $pdf = new PDF('P','pt','A4');
    $pdf->SetFont('Helvetica','',10);


    // should not need changing, change above instead.
    $pdf->connect(servername, username, password, dbname);

    // attributes for the page titles
    $attr = array('titleFontSize'=>'16', 'titleText'=>'List of Groups');
    $sql_statement = "SELECT projectName AS ProjectName, facultyName AS Supervisor FROM student_group JOIN faculty_student_group ON faculty_student_group.groupId = student_group.groupId JOIN faculty ON faculty.facultyId = faculty_student_group.facultyId ";

    $pdf->mysql_report($sql_statement, false, $attr );





    unset($_SESSION['batch_id']);

    $pdf->Output();
}

<?php
require_once ('includes/config.php');
session_start();
$title= $_SESSION['sql_title'] ;
$titleFontSize = $_SESSION['sql_title_font_size'] ;
$sql = $_SESSION['sql_statement'] ;

generate_pdf($title,$titleFontSize,$sql);


function generate_pdf($title,$titleFontSize,$sql){

    //require ('includes/config.php');
    define('FPDF_FONTPATH','libs/fpdf/font/');
    require('libs/fpdf/mysql_report.php');

    // the PDF is defined as normal, in this case a Portrait, measurements in points, A4 page.
    $pdf = new PDF('P','pt','A4');
    $pdf->SetFont('Helvetica','',10);


    // should not need changing, change above instead.
    $pdf->connect(servername, username, password, dbname);

    // attributes for the page titles
    $attr = array('titleFontSize'=>$titleFontSize, 'titleText'=>$title);

    $sql_statement = $sql;

    // Generate report
    $pdf->mysql_report($sql_statement, false, $attr );
    
    unset($_SESSION['sql_title'],$_SESSION['sql_title_font_size'],$_SESSION['sql_statement']);
    
    $pdf->Output();
}

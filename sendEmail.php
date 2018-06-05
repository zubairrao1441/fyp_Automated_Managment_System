<?php
$title = "FYPMS";
$subtitle = "Send Email";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();
if (!isset($_SESSION["isCord"])) {
    header('Location: ' . 'index.php');
}

//
require("libs/sendgrid-php/sendgrid-php.php");
$sendgrid = new SendGrid(sendgridapi);

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['btnSendMail'])) {
        $recipient = $_POST['recipient'];
        $subject = $_POST['subject'];
        $msgBody = $_POST['msgBody'];

        //Setting up SendGrid
        $email = new SendGrid\Email();

        $email
            ->addTo($recipient)
            ->setFrom('coordinator@fypms.com')
            ->setFromName('Coordinator')
            ->setSubject($subject)
            ->setHtml($msgBody);

        try {
            $res = $sendgrid->send($email);
            if ($res->getCode() == 200){
                //Email send successfully
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');
            }
            else{
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=f');
            }

        } catch (\SendGrid\Exception $e) {
            echo $e->getCode();
            foreach ($e->getErrors() as $er) {
                echo $er;
                exit;
            }
        }




    }
}


?>

</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php require_once("includes/main-header.php"); ?>
    <?php require_once("includes/main-sidebar.php"); ?>
    <div class="content-wrapper">
        <?php require_once("includes/content-header.php"); ?>

        <section class="content" style="min-height: 700px">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-10">

                    <!--Show validation errors-->
                    <?php if (isset ($_GET['status'])){
                        if ($_GET['status'] == 't'){ ?>
                            <div style="text-align:center;" class="alert alert-success" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Email Sent successfully!
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                        <?php   }
                        else if ($_GET['status'] = 'f'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! Email was not sent
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                        <?php }

                        else{ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! Something Went Wrong
                                <button type="button" class="close" data-dismiss="alert">x</button>
                            </div>
                        <?php    }
                    }?>

                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Compose New Message</h3>
                        </div>
                        <!-- /.box-header -->
                        <form role="form" action="" id="sendEmail" name="sendEmail" method="POST" data-toggle="validator">
                        <div class="box-body">
                            <div class="form-group">
                                <input type="email" name="recipient" class="form-control" placeholder="To:" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="subject" class="form-control" placeholder="Subject:" required>
                            </div>
                            <div class="form-group">
                    <textarea id="compose-textarea" name="msgBody" class="form-control" style="height: 300px" required>

                    </textarea>
                            </div>

                        </div>
                        </form>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="pull-right">
                                <button type="submit" name="btnSendMail" form="sendEmail" class="btn btn-primary" ><i class="fa fa-envelope-o"></i> Send
                                </button>
                            </div>
                            <a href="<?php echo siteroot;?>" class="btn btn-default"><i class="fa fa-times"></i> Discard</a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!-- /. box -->

                </div class="col-md-1">
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

<!-- Page Script -->
<script>
    $(function () {
        //Add text editor
        $("#compose-textarea").wysihtml5();
    });
</script>
</body>
<?php
$title="FYPMS";
$subtitle="Manage External Examiner";
require_once("includes/header.php");
require_once("includes/config.php");
session_start();
if(!isset($_SESSION["isCord"]))
{
    header('Location: '.'index.php');
}



//Check if form is submitted by GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


}

//Check if form is submitted by POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['btnDelete'])){

        //Getting value from POST and sanitizing
        $deleteId = filter_input(INPUT_POST,'deleteId',FILTER_SANITIZE_NUMBER_INT);


        //Check if faculty is supervising any group
        $sql = "SELECT facultyId FROM faculty_student_group WHERE facultyId= '$deleteId' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            //Faculty is supervising a group
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=n');die;
        } else {
            //Delete faculty
            // sql to delete a record
            $sql = "DELETE FROM faculty WHERE facultyId= '$deleteId' LIMIT 1";

            if ($conn->query($sql) === TRUE) {
                //Also delete record from work_load
                $sql = "DELETE FROM work_load WHERE facultyId='$deleteId' ";

                if ($conn->query($sql) === TRUE) {
                    header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
                }
            }
        }

    }

    if (isset($_POST['btnEditExternal'])){
        //Validations
        if ($_POST['name']!="" && $_POST['email']!="" && $_POST['company']!=""  && $_POST['password'] !="" ){

            //Getting values from POST and sanitizing
            $editId = filter_input(INPUT_POST,'editId',FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST,'name',FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
            $company = filter_input(INPUT_POST,'company',FILTER_SANITIZE_SPECIAL_CHARS);
            $designation = filter_input(INPUT_POST,'designation',FILTER_SANITIZE_SPECIAL_CHARS);
            $contact = filter_input(INPUT_POST,'contact',FILTER_SANITIZE_NUMBER_INT);
            $password = filter_input(INPUT_POST,'password',FILTER_SANITIZE_NUMBER_INT);
            $isActive = filter_input(INPUT_POST,'isActive',FILTER_SANITIZE_NUMBER_INT);
            // prepare and bind
            $stmt = $conn->prepare("UPDATE  external_examiner SET examinerName = ?, examinerEmail = ?, examinerPhone = ?, examinerPassword = ?, company =?, designation=?, isActive=? WHERE examinerId = ?");
            $stmt->bind_param("ssssssii", $name, $email,$contact, $password,$company,$designation,$isActive,$editId);


            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                $conn->close();
                header('Location:' . $_SERVER['PHP_SELF'] . '?status=t');die;
            }

        }
        else{
            header('Location:' . $_SERVER['PHP_SELF'] . '?status=validation_err');die;
        }



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
                        else if ($_GET['status'] == 'n'){ ?>
                            <div style="text-align:center;" class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Error! This faculty is supervising a group. Can not delete this
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

                    <?php if (isset ($_GET['edit']) && is_numeric($_GET['edit']) && strlen($_GET['edit'])>0 )  {
                        $editId = filter_input(INPUT_GET,'edit',FILTER_SANITIZE_NUMBER_INT);

                        $sql = "SELECT * FROM external_examiner WHERE examinerId='$editId' LIMIT 1 ";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while($row = $result->fetch_assoc()) {
                                $name = $row['examinerName'];
                                $email = $row['examinerEmail'];
                                $contact = $row['examinerPhone'];
                                $company = $row['company'];
                                $designation = $row['designation'];
                                $password = $row['examinerPassword'];
                                $isActive = $row['isActive'];
                            }
                        }
                        ?>
                        <!-- general form elements -->
                        <div class="box no-border">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit: <?php echo $name;?> </h3>
                            </div>

                            <form class="form-horizontal" name="editExternal" action=""  method="post" onsubmit="return confirm('Are you sure you want to submit these changes?');" data-toggle="validator" >
                                <input type="hidden" name="editId" value="<?php echo $editId; ?>">
                                <div class="box-body">

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Name</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name;?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Email</label>

                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email;?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Company</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="company" name="company" value="<?php echo $company;?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Designation</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="designation" name="designation" value="<?php echo $designation;?>" required>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Contact</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $contact;?>" >
                                        </div>
                                    </div>



                                    <div class="form-group">


                                        <label class="col-sm-2 control-label">Password <i class="fa fa-eye text-primary" id="eye" aria-hidden="true"></i> </label>
                                        <div class="col-sm-10 " >

                                            <input type="password"  class="form-control" id="password" name="password" value="<?php echo $password ;?>"  required>
                                        </div>

                                    </div>




                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Status</label>

                                        <div class="col-sm-10">
                                            <select name="isActive" id="isActive" style="width:200px;" required>
                                                <option value="1" <?php if ($isActive==1){echo 'selected';}?>>Active</option>
                                                <option value="0" <?php if ($isActive==0){echo 'selected';}?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>





                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn  btn-default btn-sm  "> Cancel</a>
                                    <button type="submit" name="btnEditExternal" class="btn btn-primary pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>

                        </div>
                        <!-- /.box -->
                        <?php
                    }else{

                    }?>

                    <div class="box no-border">
                        <div class="box-header">
                            <h3 class="box-title">External Examiner List</h3>

                        </div>
                        <!-- /.box-header -->
                        <div class="box-body  no-padding">
                            <table id="manageFaculty" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <?php
                                $sql = "SELECT * from external_examiner  ";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['examinerName'];?></td>
                                        <td><?php echo $row['examinerEmail'];?></td>
                                        <td><?php echo $row['examinerPhone'] ;?></td>
                                        <td><?php echo $row['designation']." - ".$row['company'] ;?></td>
                                        <td><?php
                                            if ($row['isActive'] == 1){ ?>
                                                <span class="label label-success">Active</span>
                                                <?php
                                            }
                                            else if ($row['isActive'] == 0){ ?>
                                                <span class="label label-danger">Inactive</span>
                                                <?php
                                            }
                                            ;?></td>
                                        <td>
                                            <a href="<?php echo $_SERVER['PHP_SELF'] . '?edit=' . $row['examinerId']; ?>"   class="btn  btn-default btn-flat  btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                                            <form  action="" method="post" onsubmit="return confirm('Are you sure you want to delete this examiner?');" data-toggle="validator">
                                                <input type="hidden" name="deleteId" value="<?php echo $row['examinerId'];?> ">
                                                <button type="submit" name="btnDelete" class="btn  btn-danger btn-flat  btn-xs"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>

                                        </td>
                                    </tr>
                                <?php }
                                ?>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="./registerFaculty.php" class="btn btn-primary btn-sm pull-right">Add New Faculty</a>
                            <a href="<?php echo siteroot; ?>" class="btn  btn-default btn-sm  "> Back</a>

                        </div>
                    </div>
                    <!-- /.box -->

                </div>

            </div>
        </section>
    </div><!-- ./content-wrapper -->
    <!--</div>-->
    <?php
    require_once("includes/main-footer.php");
    ?>
</div>
<!-- ./wrapper -->
<?php
require_once("includes/required_js.php");
?>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#manageFaculty').DataTable({
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

        function show() {
            var p = document.getElementById('password');
            p.setAttribute('type', 'text');
        }

        function hide() {
            var p = document.getElementById('password');
            p.setAttribute('type', 'password');
        }

        var pwShown = 0;

        document.getElementById("eye").addEventListener("click", function () {
            if (pwShown == 0) {
                pwShown = 1;
                show();
            } else {
                pwShown = 0;
                hide();
            }
        }, false);

    } );
</script>
</body>
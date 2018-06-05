<?php
	require_once("includes/header.php");
	require_once("includes/config.php");
?>
<script>
	$(document).ready(function() {
		//$("#errlogin").hide();
		$("#signUp").click(function() {
			var pass=document.getElementById("Stu-Password").value;
			var name=document.getElementById("Stu-Name").value;
			var CMS=document.getElementById("Stu-CMS").value;
			var phone=document.getElementById("Stu-Phone").value;
			var batch=document.getElementById("Stu-Batch").value;
			
			var input=$("#Stu-Email");
			var re = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
			var is_email=re.test(input.val());
			if(is_email && (pass!="" && name!="" && CMS!="" && phone!="" && batch!="")){
				input.removeClass("invalid").addClass("valid");
				window.location.href = 'includes/student.php';
			}
			else{
				//$("#errlogin").fadeIn().slow();
				input.removeClass("valid").addClass("invalid");
				alert("Invalid");
			}		
		});
		
	});
	
</script>
</head>
<body>
<div id="signupPanel">
	<div class="panel panel-primary">
		<div class="panel-heading">Student Sign Up</div>
			  
			  <div id="signupbody" class="panel-body">
				
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1">Name</span>
				  <input type="text" id="Stu-Name" name="stName" class="form-control" placeholder="User Name" aria-describedby="basic-addon1">
				</div>
				
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1">CMS</span>
				  <input type="text" id="Stu-CMS" name="stCMS" class="form-control" placeholder="CMS" aria-describedby="basic-addon1">
				</div>
				
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1">Phone</span>
				  <input type="text" id="Stu-Phone" name="stPhone" class="form-control" placeholder="Phone Number" aria-describedby="basic-addon1">
				</div>
				
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1">Email</span>
				  <input type="text" id="Stu-Email" name="stEmail" class="form-control" placeholder="Email" aria-describedby="basic-addon1">
				</div>
				
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1">Password</span>
				  <input type="text" id="Stu-Password" name="stPasswrod" class="form-control" placeholder="Password" aria-describedby="basic-addon1">
				</div>
				
				<div class="input-group">
				  <span class="input-group-addon" id="basic-addon1">Batch</span>
				  <input type="text" id="Stu-Batch" name="stBatch" class="form-control" placeholder="Batch" aria-describedby="basic-addon1">
				</div>
				
				<button type="button" id="signUp" name="signUp" class="btn btn-default navbar-btn">Sign Up</button>
	</div>
</div>
</body>
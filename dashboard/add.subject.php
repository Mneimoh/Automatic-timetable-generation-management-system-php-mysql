<?php
   session_start();
 
if(@$_SESSION['user_id']){
	
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/timetable/header.php";
   include_once($path);
   
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/timetable/class.database.php";
   include_once($path);
   
   include_once("navbar.php");
   
	function GetSubjectInfo($subcode,$user_id){
			$db_connection = new dbConnection();
			$link = $db_connection->connect(); 
			$query = $link->query("SELECT * FROM subject WHERE subject_code = '$subcode' AND user_id='$user_id'");
			$rowCount = $query->rowCount();
			if($rowCount ==1)
			{
				$result = $query->fetchAll();
				return $result;
			}
			else
			{
				return $rowCount;
			}
		}
	
	function add_subjects($user_id,$code,$name,$lecture,$tutorial,$practicle,$teacher,$faculty,$course){
			$db_connection = new dbConnection();
			$link = $db_connection->connect(); 
			$query = $link->prepare("INSERT INTO subject (user_id,subject_code,subject_name,l,t,p,teacher,faculty,course) VALUES(?,?,?,?,?,?,?,?,?)");
			$values = array ($user_id,$code,$name,$lecture,$tutorial,$practicle,$teacher,$faculty,$course);
			$query->execute($values);
			$count = $query->rowCount();
			return $count;
		}

	function add_tablesheet($user_id,$code,$name,$lecture,$tutorial,$practicle,$teacher,$faculty,$course){
			
			$total = 0;
			$total += $lecture + $tutorial + $practicle;
			for ($i=0; $i < $total; $i++) { 
				$ran = rand(0,47);
				$db_connection = new dbConnection();
				$link = $db_connection->connect(); 
				$query = $link->query("SELECT * FROM tablesheet WHERE faculty_name='$faculty' AND course='$course' AND cell='$ran' AND user_id='$user_id'");
				$rowCount = $query->rowCount();
				if($rowCount ==0)
				{
					$query = $link->prepare("INSERT INTO tablesheet (cell,data,faculty_name,user_id,teacher,course) VALUES(?,?,?,?,?,?)");
					$values = array ($ran,$name,$faculty,$user_id,$teacher,$course);
					$query->execute($values);
				}
				else
				{
					while ($rowCount != 0) {
						$ran = rand(0,47);
						$query = $link->query("SELECT * FROM tablesheet WHERE faculty_name='$faculty' AND course='$course' AND cell='$ran' AND user_id='$user_id'");
						$rowCount = $query->rowCount();
					}
					
					$query = $link->prepare("INSERT INTO tablesheet (cell,data,faculty_name,user_id,teacher,course) VALUES(?,?,?,?,?,?)");
					$values = array ($ran,$name,$faculty,$user_id,$teacher,$course);
					$query->execute($values);
				}	
				/*if ($cell) {
					$ran = rand(1,48);
					$query = $link->prepare("INSERT INTO tablesheet (cell,data,faculty_name,user_id,teacher,course) VALUES(?,?,?,?,?,?)");
					$values = array ($ran,$name,$faculty_name,$user_id,$teacher,$course);
					$query->execute($values);
				}else{
					$query = $link->prepare("INSERT INTO tablesheet (cell,data,faculty_name,user_id,teacher,course) VALUES(?,?,?,?,?,?)");
					$values = array ($ran,$name,$faculty_name,$user_id,$teacher,$course);
					$query->execute($values);
				}*/

			}
	}
	
	$user_id = $_SESSION['user_id'];
	if(isset($_POST['submit']))
	{
			$check_subject = GetSubjectInfo($_POST['subcode'],$_SESSION['user_id']);
		if($check_subject === 0){
			$count= add_subjects($_SESSION['user_id'],$_POST['subcode'],$_POST['name'],$_POST['l'],$_POST['t'],$_POST['p'],$_POST['tname'],$_POST['faculty'], $_POST['course'] );
			if($count === 1){ 
				$tablesheet = add_tablesheet($_SESSION['user_id'],$_POST['subcode'],$_POST['name'],$_POST['l'],$_POST['t'],$_POST['p'],$_POST['tname'],$_POST['faculty'],$_POST['course'] );
			
			echo 	'<div class="alert alert-success">  
					<a class="close" data-dismiss="alert">X</a>  
					<strong>Tada Success! </strong>Added Successfully.  
					</div>'; 
	
			echo 	'<div class="alert alert-success">  
					<a class="close" data-dismiss="alert">X</a>  
					<strong>Tada Success! </strong>Added Datasheet Successfully.  
					</div>'; 
				
			}
			else{
				echo '<div class="alert alert-block">  
					<a class="close" data-dismiss="alert">X</a>  
					<strong>Opps Error!</strong>Not Added.  
					</div>';  
			}
		}
		else{
			echo '<div class="alert alert-block">  
					<a class="close" data-dismiss="alert">X</a>  
					<strong>Opps Error!</strong>Subject Already Exists.  
					</div>'; 			
		}
		
	}
	
}
else{
	echo "You are not logged in yet. please go back and login again";
	exit();
}
?>


<div class="container"> 
  <div class="row">
    <div class="col-lg-6">
		<div class="jumbotron">

				<form class="form-horizontal" method= "post" action="">
				<fieldset>

				<!-- Form Name -->
				<legend>Add Subjects</legend>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="subcode">Subject Code</label>  
				  <div class="col-md-8">
				  <input id="subcode" name="subcode" type="text" placeholder="" class="form-control input-md" required="">
					
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="name">Subject Name</label>  
				  <div class="col-md-8">
				  <input id="name" name="name" type="text" placeholder="" class="form-control input-md" required="">
					
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="name">Teacher Name</label>  
				  <div class="col-md-8">
				  <input id="name" name="tname" type="text" placeholder="" class="form-control input-md" required="">
					
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="l">Total Lesson</label>  
				  <div class="col-md-8">
				  <input id="l" name="l" type="text" placeholder="L" class="form-control input-md" required="">
				  <span class="help-block">Total lecture for this subject</span>  
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="t">Total Tutorial</label>  
				  <div class="col-md-8">
				  <input id="t" name="t" type="text" placeholder="T" class="form-control input-md" required="">
				  <span class="help-block">Total tutorial for this subject</span>  
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="p">Total Practical</label>  
				  <div class="col-md-8">
				  <input id="p" name="p" type="text" placeholder="P" class="form-control input-md" required="">
				  <span class="help-block">Total Practical for this subject</span>  
				  </div>
				</div>

				<!-- Select Basic -->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="Course">School Name</label>
				  <div class="col-md-8">
					<select id="facultyname" name="faculty" class="form-control" required="">
					<?php
				    $db_connection = new dbConnection();
					$link = $db_connection->connect(); 
					$user_id= $_SESSION['user_id'];
					$query = $link->query("SELECT * FROM faculty WHERE user_id='$user_id'");
					$query->setFetchMode(PDO::FETCH_ASSOC); 				
				while($result = $query->fetch()){
					echo '<option value="'.$result['faculty_name'].'">'.$result['faculty_name'].'</option>';
				  }?>
					  
					</select>
					
				  </div>
				</div>

				<!-- Select Basic -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="Course">Class Name</label>
			  <div class="col-md-8">
				<select id="coursefullname" name="course" class="form-control" required="">
				<?php
				// lists the course on drop down button course
				$db_connection = new dbConnection();
				$link = $db_connection->connect(); 
				$user_id= $_SESSION['user_id'];
				$query = $link->query("SELECT DISTINCT course_full_name FROM course WHERE user_id='$user_id'"); 
				$query->setFetchMode(PDO::FETCH_ASSOC);	
				while($result = $query->fetch()){
				   echo "<option value='".$result['course_full_name']."'>".$result['course_full_name']."</option>";
				}

				?>
				  
				</select>
				
			  </div>
			</div>

							<!-- Button -->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="submit"></label>
				  <div class="col-md-4">
					<button id="submit" name="submit" class="btn btn-primary">Add Subject</button>
				  </div>
				</div>

				</fieldset>
				</form>
				
		</div>		
    </div>
    <div class="col-lg-6">
		<div class="jumbotron">
		<?php
			if($_SESSION['user_id']){
				
				function deletesub($subcode, $user_id,$faculty,$course){
					$db_connection = new dbConnection();
					$link = $db_connection->connect(); 
					$link->query("DELETE FROM `timetable`.`subject` WHERE `subject`.`subject_id` = '$subcode' AND `subject`.`user_id`='$user_id' AND `subject`.`faculty` = '$faculty' AND `subject`.`course` = '$course'");
					//$link->query("DELETE FROM `timetable`.`tablesheet` WHERE `tablesheet`.`data` = '$subject' AND `tablesheet`.`faculty_name` = '$faculty' AND `tablesheet`.`user_id`='$user_id' AND `tablesheet`.`course` = '$course'");
					return true;
				}
				function deletesheet($user_id,$faculty,$course,$subject){
					$db_connection = new dbConnection();
					$link = $db_connection->connect(); 
					$link->query("DELETE FROM `timetable`.`tablesheet` WHERE `tablesheet`.`data` = '$subject' AND `tablesheet`.`faculty_name` = '$faculty' AND `tablesheet`.`user_id`='$user_id' AND `tablesheet`.`course` = '$course'");
				}
				//if(isset($_GET['delete'])){
				if(isset($_POST['delete'])){


					 $my = deletesub($_POST['id'],$_SESSION['user_id'],$_POST['faculty'],$_POST['course']);
					 if ($my == true) {
					 	echo 	'<div class="alert alert-success">  
							<a class="close" data-dismiss="alert">X</a>  
							<strong>Tada Success! </strong>Successfully Deleted.  
							</div>'; 
					 	deletesheet($_SESSION['user_id'],$_POST['faculty'],$_POST['course'],$_POST['subject']);
					 }
					 
				}
				
				// This function lists all the timetable created till now.. with options like delete, edit
				function Subjectlist($user_id){
					$db_connection = new dbConnection();
					$link = $db_connection->connect(); 
					$query = $link->query("SELECT * FROM subject WHERE user_id= '$user_id'");
					$query->setFetchMode(PDO::FETCH_ASSOC);
					
					
					echo
						  "<h2>List of Subjects Already Added</h2>".          
						  "<table class='table'>".
							"<thead>".
							  "<tr>".
							   "<th>Subject Id</th>".
								"<th>Subject Code</th>".
								"<th>Subject Name</th>".
								"<th>L</th>".
								"<th>T</th>".
								"<th>P</th>".
								"<th>TEACHER</th>".
								"<th>Options</th>".
							  "</tr>".
							"</thead>".
							"<tbody>";
							
							while($result = $query->fetch()){
							  echo "<tr>"
									."<td>".$result['subject_id']."</td>"
									 ."<td>".$result['subject_code']."</td>"
									 ."<td>".$result['subject_name']."</td>"
									 ."<td>".$result['l']."</td>"
									 ."<td>".$result['t']."</td>"
									 ."<td>".$result['p']."</td>"
									 ."<td>".$result['teacher']."</td>"
									 ."<td>".$result['faculty']."</td>"
									 ."<td>".$result['course']."</td>"
									 ."<td><form class='form-horizontal' method= 'post' action=''>
				
				
				  <input id='id' name='id' type='hidden' placeholder='' value='".$result['subject_id']."' class='form-control input-md' required=''>
				
				  <input id='subject' name='subject' type='hidden' value='".$result['subject_name']."' placeholder='' class='form-control input-md' required=''>
				  <input id='faculty' name='faculty' type='hidden' value='".$result['faculty']."' placeholder='' class='form-control input-md' required=''>
				  <input id='course' name='course' type='hidden' value='".$result['course']."' placeholder='' class='form-control input-md' required=''>
							<!-- Button -->
				<div class='form-group'>
				  <label class='col-md-4 control-label' for='submit'></label>
				  <div class='col-md-4'>
					<button id='delete' name='delete' class='btn btn-primary'>Delete</button>
				  </div>
				</div>

				
				</form></td>"
									 ."</tr>".
							  "</tr>";
							}  
					echo	"</tbody>".
						  "</table>".
						"</div>";
						
				}
				
				Subjectlist($_SESSION['user_id']);
			}
			else{
				echo "You are not logged in yet. Please go back and login again";
			}
		?>
		
		</div>
    </div>
  </div>
  
</div>
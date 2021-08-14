<?php
   session_start();
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/timetable/header.php";
   include_once($path);
   
   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/timetable/class.database.php";
   include_once($path);
   
   include_once("navbar.php");
   
if($_SESSION['user_id']){
	
	function add_course($user_id,$course_name,$course_full_name, $semester, $section, $faculty_code){
			$db_connection = new dbConnection();
			$link = $db_connection->connect(); 
			$query = $link->prepare("INSERT INTO course (user_id,course_name,course_full_name,semester,section,faculty_id) VALUES(?,?,?,?,?,?)");
			$values = array ($user_id,$course_name,$course_full_name, $semester, $section, $faculty_code);
			$query->execute($values);
			$count = $query->rowCount();
			return $count;
		}
	
	if(isset($_POST['submit']))
	{
			$count= add_course($_SESSION['user_id'],$_POST['name'],$_POST['coursefullname'],$_POST['semester'],$_POST['section'],$_POST['faculty']);
			if($count){ 
			
			echo 	'<div class="alert alert-success">  
					<a class="close" data-dismiss="alert">X</a>  
					<strong>Tada Success! </strong>Added Successfully.  
					</div>'; 
			}
			else{
				echo '<div class="alert alert-block">  
					<a class="close" data-dismiss="alert">X</a>  
					<strong>Opps Error!</strong>Not Added.  
					</div>';  
			}
		
	}
	
}
?>


<div class="container">
	
  <div class="row">
    <div class="col-lg-6">
		<div class="jumbotron">
		Here you will Assign Class, Term, Section and Subject to a school that you added.
		<form class="form-horizontal" method= "post" action = "">
			<fieldset>

			<!-- Form Name -->
			<legend>Add Class</legend>

			
			<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="name">Class Code</label>  
				  <div class="col-md-8">
				  <input id="name" name="name" type="text" placeholder="" class="form-control input-md" required="">
				<span class="help-block">e.g EE1, AC2, BC3</span>  	
				  </div>
				</div>
				
			
			<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="name">Class Name</label>  
				  <div class="col-md-8">
				  <input id="coursefullname" name="coursefullname" type="text" placeholder="" class="form-control input-md" required="">
				<span class="help-block">e.g Form1</span>  	
				  </div>
				</div>
				

			<!-- Select Basic -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="semester">In Terms</label>
			  <div class="col-md-8">
				<select id="semester" name="semester" class="form-control">
				  <option value="I">I</option>
				  <option value="II">II</option>
				  <option value="III">III</option>
				</select>
			  </div>
			</div>

			<!-- Select Basic -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="section">In Section</label>
			  <div class="col-md-8">
				<select id="section" name="section" class="form-control">
				  <option value="A">A</option>
				  <option value="B">B</option>
				  <option value="C">C</option>
				</select>
			  </div>
			</div>

			<!-- Select Basic -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="faculty">School Teaching</label>
			  <div class="col-md-8">
				<select id="faculty" name="faculty" class="form-control">
				  <?php
				    $db_connection = new dbConnection();
					$link = $db_connection->connect(); 
					$user_id= $_SESSION['user_id'];
					$query = $link->query("SELECT * FROM faculty WHERE user_id='$user_id'");
					$query->setFetchMode(PDO::FETCH_ASSOC); 				
				while($result = $query->fetch()){
					echo '<option value="'.$result['faculty_id'].'">'.$result['faculty_name'].'</option>';
				  }?>
				</select>
			  </div>
			</div>

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-4">
				<button id="submit" name="submit" class="btn btn-success">Add Class</button>
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
				
				function deletecourse($course_name,$user_id){
					$db_connection = new dbConnection();
					$link = $db_connection->connect(); 
					$link->query("DELETE FROM `timetable`.`course` WHERE `course`.`course_id` = '$course_name' AND `course`.`user_id`='$user_id'");
				}
				if(isset($_GET['delete'])){
					 deletecourse($_GET['id'],$_SESSION['user_id']);
					 echo 	'<div class="alert alert-success">  
							<a class="close" data-dismiss="alert">X</a>  
							<strong>Tada Success! </strong>Successfully Deleted.  
							</div>'; 
				}
				
				// This function lists all the timetable created till now.. with options like delete, edit
				function Courselist($user_id){
					$db_connection = new dbConnection();
					$link = $db_connection->connect(); 
					$query = $link->query("SELECT * FROM course WHERE user_id= '$user_id'");
					$query->setFetchMode(PDO::FETCH_ASSOC);
					
					
					echo
						  "<h2>List of Class Already Added</h2>".          
						  "<table class='table'>".
							"<thead>".
							  "<tr>".
								"<th>Class Code</th>".
								"<th>Term</th>".
								"<th>Section</th>".
								"<th>School Id</th>".
							  "</tr>".
							"</thead>".
							"<tbody>";
							
							while($result = $query->fetch()){
							  echo "<tr>"
									 ."<td>".$result['course_name']."</td>"
									 ."<td>".$result['semester']."</td>"
									 ."<td>".$result['section']."</td>"
									 ."<td>".$result['faculty_id']."</td>"
									 ."<td><a href='add.course.php?delete=true&id=".$result['course_id']."'>Delete</a></td>"
									 ."</tr>".
							  "</tr>";
							}  
					echo	"</tbody>".
						  "</table>".
						"</div>";
						
				}
				
				Courselist($_SESSION['user_id']);
			}
			else{
				echo "You are not logged in yet. Please go back and login again";
			}
		?>
		
		</div>
    </div>
  </div>
  
</div>
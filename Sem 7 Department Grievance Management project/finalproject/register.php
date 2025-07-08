<?php 
include("php/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Registration</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
        <?php
include("php/config.php");
if(isset($_POST['submit'])){
    
    $firstname=$_POST['firstname'];
    $lastname=$_POST['lastname'];
    $email_id=$_POST['email'];
    $password=$_POST['password'];
    $usercat_id=$_POST['usercat_id'];

    // Verifying the uniqueness of the email
    $verify_query=mysqli_query($con,"SELECT email FROM users WHERE email='$email_id'");

    // Check if the query was executed successfully
    if($verify_query !== false){
        if(mysqli_num_rows($verify_query)!=0){
            echo"<div class='message'>
                    <p>This email is used, please try another one.</p>
                </div><br>";
            echo "<a href='javascript:self.history.back()'><button class='btn' > Go Back</button>";
        } else {
            // Inserting user data into the 'users' table
            $insert_user_query = mysqli_query($con, "INSERT INTO users (firstname, lastname, email, Password, usercat_id) VALUES ('$firstname','$lastname','$email_id','$password','$usercat_id')");

            
          
            // Check if the insertion was successful
            if($insert_user_query !== false){
                $user_id = mysqli_insert_id($con);

                // Inserting user data into respective role tables based on user category
                switch($usercat_id) {
                    case 1:
                        // Insert into student table
                        mysqli_query($con,"INSERT INTO faculty (firstname, lastname,password,Email,user_id) VALUES ('$firstname','$lastname','$password','$email_id',$user_id)");
                        
                       
                        break;
                    case 2:
                        // Insert into faculty table
                        mysqli_query($con,"INSERT INTO student (firstname, lastname, password,Email,user_id) VALUES ('$firstname','$lastname','$password','$email_id',$user_id)");
                        break;

                   
                    default:
                        // Handle default case if necessary
                        echo "Invalid user category selected";
                    }

                   

                echo"<div class='message'>
                        <p>Registration successful!</p>
                    </div><br>";
                echo "<a href='index.php'><button class='btn' >Login Now</button>";
            } else {
                echo "Error Occurred: " . mysqli_error($con);
            }
        }

        
    } else {
        echo "Error Occurred: " . mysqli_error($con);
    }
} else {
?>

            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" required>
                </div>
                <div class="field input">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" required>
                </div>
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="usercat_id">User Category</label>
                    <select name="usercat_id" id="usercat_id" required>
                        <option value="" selected disabled>Select User Category</option>
                        <?php
                            $usercat_query=mysqli_query($con,"SELECT * FROM usercategory");
                            while($row=mysqli_fetch_assoc($usercat_query)){
                                echo "<option value='".$row['usercat_id']."'>".$row['category_name']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <!-- <div class="field input" id="enrollmentIdField" style="display:none;">
                    <label for="enrollmentId">Enrollment ID:</label>
                    <input type="text" id="enrollmentId" name="enrollmentId">
                </div> -->
                     
                <div class="field">
                    <input type="submit" name="submit" class="btn" value="Register">
                </div>
                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>



</body>
</html>
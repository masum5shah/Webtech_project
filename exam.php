
<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
</head>
<body>

<h2>Registration Form</h2>

<form action="showdata.php" method="post">
    First Name: <input type="text" name="fname"><br><br>
    Last Name: <input type="text" name="lname"><br><br>
    DOB: <input type="text" name="dob"><br><br>

    Gender:
    <input type="radio" name="gender" value="Male"> Male
    <input type="radio" name="gender" value="Female"> Female
    <br><br>

    Phone: <input type="text" name="phone"><br><br>
    Email ID: <input type="text" name="email"><br><br>

    Password: <input type="password" name="password"><br><br>
    Confirm Password: <input type="password" name="cpassword"><br><br>

    <input type="submit" value="Register">
</form>

</body>
</html>


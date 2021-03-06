<?php

    session_start();

    include('connect.php');

    // Create a new calendar and admin user (register.php)
    if(isset($_POST['registerCal'])) {
        $calName = mysqli_real_escape_string($conn, $_POST['calName']);
        $calID = mysqli_real_escape_string($conn, $_POST['calID']);

        $adminFirstName = mysqli_real_escape_string($conn, $_POST['adminFirstName']);
        $adminLastName = mysqli_real_escape_string($conn, $_POST['adminLastName']);
        $adminUsername = mysqli_real_escape_string($conn, $_POST['adminUsername']);
        $adminPassword = mysqli_real_escape_string($conn, $_POST['adminPassword']);
        $password = md5($adminPassword);

        $query = "SELECT * FROM calendars WHERE Tag = '$calID'";
        $result = mysqli_query($conn, $query);

        $query2 = "SELECT * FROM users WHERE Username = '$adminUsername'";
        $result2 = mysqli_query($conn, $query2);
        
        if(mysqli_num_rows($result) >= 1) {
            $_SESSION["error"] = "Calendar ID already exists.";
        }
        else if(mysqli_num_rows($result2) >= 1) {
            $_SESSION["error"] = "Username already exists";
        }
        else {
            $query = "INSERT INTO calendars (Tag, Name, Admin) VALUES ('$calID', '$calName', '$adminUsername')";
            $query2 = "INSERT INTO users (Account, Username, FirstName, LastName, Password, Calendar, color) VALUES ('admin', '$adminUsername', '$adminFirstName', '$adminLastName', '$password', '$calID', '#0080ff')";
    
            $result = mysqli_query($conn, $query);
            $result2 = mysqli_query($conn, $query2);
    
            $_SESSION['username'] = $adminUsername;
            $_SESSION['firstname'] = $adminFirstName;
            $_SESSION['lastname'] = $adminLastName;
            $_SESSION['calendarID'] = $calID;
            $_SESSION['color'] = '#0080ff';
    
            header('location: index.php');
        }
    }

    // Login user page (login.php)
    if(isset($_POST['login'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $password = md5($password);

        $query = "SELECT * FROM users WHERE  Username = '$username' AND Password = '$password'";

        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) == 1) {
            
            while($row = mysqli_fetch_assoc($result)) {
                $_SESSION['username'] = $row['Username'];
                $_SESSION['firstname'] = $row['FirstName'];
                $_SESSION['lastname'] = $row['LastName'];
                $_SESSION['calendarID'] = $row['Calendar'];
                $_SESSION['color'] = $row['color'];
            }
            
            header('location: index.php');
        }else {
            $error = "Incorrect Username/Password";
            $_SESSION["error"] = $error;
        }
    }

    // Add user to calendar page (adduser.php)
    if(isset($_POST['addUser'])) {
        $calendarID = mysqli_real_escape_string($conn, $_POST['calID']);
        $firstname = mysqli_real_escape_string($conn, $_POST['userFirstName']);
        $lastname = mysqli_real_escape_string($conn, $_POST['userLastName']);
        $username = mysqli_real_escape_string($conn, $_POST['userUsername']);
        $password = mysqli_real_escape_string($conn, $_POST['userPassword']); 
        $userColor = mysqli_real_escape_string($conn, $_POST['userColor']); 
        $password = md5($password);

        $query = "SELECT * FROM calendars WHERE Tag = '$calendarID'";
        $result = mysqli_query($conn, $query);

        $query2 = "SELECT * FROM users WHERE Username = '$username'";
        $result2 = mysqli_query($conn, $query2);
        
        if(mysqli_num_rows($result) != 1) {
            $_SESSION["error"] = "Calendar ID does not exist";
        }
        else if(mysqli_num_rows($result2) >= 1) {
            $_SESSION["error"] = "Username already exists";
        }
        else {
            $query2 = "INSERT INTO users (Account, Username, FirstName, LastName, Password, Calendar, color) VALUES ('user', '$username', '$firstname', '$lastname', '$password', '$calendarID', '$userColor')";
            $result = mysqli_query($conn, $query2);

            $_SESSION['username'] = $username;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['calendarID'] = $calendarID;
            $_SESSION['color'] = $userColor;

            header('location: index.php');
        }
    }

    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }



?>
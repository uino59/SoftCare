<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

// Include config file
require_once "config.php";
$link = get_db();


// fetch variables from post
$username = $_POST['uname'];
$password = $_POST['psw'];
$username_err = $password_err = $login_err = "";


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {

  // Check if username is empty
  if (empty(trim($username))) {
    $username_err = "Please enter username.";
  } else {
    $username = trim($username);
  }

  if (empty(trim($username))) {
    $username_err = "Please enter username.";
  } else {
    $username = trim($username);
  }

  // Validate credentials
  if (empty($username_err) && empty($password_err)) {
    //get stored password from db
    $db_password = "";

    $stmt = $link->prepare("SELECT password FROM credentials WHERE username = ?");
    $stmt->bind_param('s', $_POST['uname']);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();
    $stmt->close();
    echo $db_password;

    if (!$db_password) {
      //no user matching username
      //header("Location: index.php");
      //end();
      echo "User not found";
    }
    if ($password == $db_password) {
      echo "Successfully logged in";
      header("Location: dashboard.php");
      $_SESSION["loggedin"] = true;
      $_SESSION["usrname"] = $username; //Replace this with the patient's name pulled from database
    } else {
      header("Location: index.php");
    }

  }
}
// Close connection
  mysqli_close($link);

?>

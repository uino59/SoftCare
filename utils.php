<?php
function testLoggedIn() {
  session_start();

  if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)){
    header("location: index.html");
    exit;
  }

}
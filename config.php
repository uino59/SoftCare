<?php

function get_login_db() {
  $link = mysqli_connect('localhost', 'root', '', 'softcare_users');

  // Check connection
  if($link === false){
      die("ERROR: Could not connect. " . mysqli_connect_error());
  }

  return $link;

}

function get_main_db() {
  $link = mysqli_connect('localhost', 'root', '', 'softcare_data');

  // Check connection
  if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
  }

  return $link;

}

?>

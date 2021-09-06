<?php
require_once "utils.php";

testLoggedIn();

?>
<html>
<head>
  <title><?php echo 'mytitle'; ?></title>
</head>

<body>
  <?php if (isset($_SESSION["usrname"])) { echo "Hello {$_SESSION["usrname"]}"; } ?>
</body>
</html>

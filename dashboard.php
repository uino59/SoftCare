<?php
require_once "utils.php";

testLoggedIn(); // Checks if loggedin variable in $_SESSION is true, redirects to login page if false

?>
<!doctype html>
<title>Site Maintenance</title>
<style>
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>

<article>
  <h1>Page under construction</h1>
  <div>
    <p>Sorry for the inconvenience but we&rsquo;re still constructing this page. </p>
  </div>
</article>

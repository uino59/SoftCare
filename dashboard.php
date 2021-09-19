<?php
require_once "utils.php";

testLoggedIn(); // Checks if loggedin variable in $_SESSION is true, redirects to login page if false

require_once "config.php";
$db = get_main_db();

$patient_id = "";
$patient_title = "";
$patient_first_name = "";
$patient_last_name = "";
$patient_phn = "";
$patient_gender = "";
$patient_dob = "";
$patient_address = "";
$patient_city = "";
$patient_country = "";

//appointment info
$appointment_time ="";
$appointment_location_id = "";
$appointment_location_address = "";
$appointment_location_city = "";
$appointment_location_postal = "";
$appointment_location_name = "";
$appointment_doctor_id= "";
$appointment_doctor_title = "";
$appointment_doctor_first_name = "";
$appointment_doctor_last_name = "";

//prescription info
$prescription_medication_id = "";
$prescription_doctor_id = "";
$prescription_medication_name = "";
$prescription_dosage = "";
$prescription_amount = "";
$prescription_instructions = "";
$prescription_refills_authorized = "";
$prescription_refills_date = "";
$prescription_doctor_title = "";
$prescription_doctor_last_name = "";

//messages info
$message_thread_id = "";
$message_patient_id = "";
$message_doctor_id = "";
$message_body = "";
$message_time_written = "";
$message_sender_type = "";
$message_sender_title = "";
$message_sender_name = "";
$message_reciever_title = "";
$message_reciever_name = "";

//populates patient info
$stmt = $db->prepare("SELECT patientId, firstName, lastName FROM patient WHERE phn = ?");
$stmt->bind_param('s', $_SESSION["usrname"]);
$stmt->execute();
$stmt->bind_result($patient_id, $patient_first_name, $patient_last_name);
$stmt->fetch();
$stmt->close();

//Populates the appointment info

$stmt1 = $db->prepare("SELECT time, doctor_doctorId, location_locationId FROM appointment WHERE patient_patientId = ?");
$stmt1->bind_param('i', $patient_id);
$stmt1->execute();
$stmt1->bind_result($appointment_time, $appointment_doctor_id, $appointment_location_id);
$stmt1->fetch();
$stmt1->close();

$stmt2 = $db->prepare("SELECT address, city, postal, name FROM location WHERE locationId = ?");
$stmt2->bind_param('i', $appointment_location_id);
$stmt2->execute();
$stmt2->bind_result($appointment_location_address, $appointment_location_city, $appointment_location_postal, $appointment_location_name);
$stmt2->fetch();
$stmt2->close();

$stmt3 = $db->prepare("SELECT title, firstName, lastName  FROM doctor WHERE doctorId = ?");
$stmt3->bind_param('i', $appointment_doctor_id);
$stmt3->execute();
$stmt3->bind_result($appointment_doctor_title, $appointment_doctor_first_name, $appointment_doctor_last_name);
$stmt3->fetch();
$stmt3->close();

// populates the prescription info
$stmt4 = $db->prepare("SELECT medication_medicationId, dosage, amount, instructions, refillsAuthorized, refillDate, doctor_doctorId  FROM prescription WHERE patient_patientId = ?");
$stmt4->bind_param('i', $patient_id);
$stmt4->execute();
$stmt4->bind_result($prescription_medication_id, $prescription_dosage, $prescription_amount, $prescription_instructions, $prescription_refills_authorized, $prescription_refills_date, $prescription_doctor_id);
$stmt4->fetch();
$stmt4->close();

$stmt5 = $db->prepare("SELECT name  FROM medication WHERE medicationId = ?");
$stmt5->bind_param('i', $prescription_medication_id);
$stmt5->execute();
$stmt5->bind_result($prescription_medication_name);
$stmt5->fetch();
$stmt5->close();

$stmt6 = $db->prepare("SELECT title, lastName  FROM doctor WHERE doctorId = ?");
$stmt6->bind_param('i', $prescription_doctor_id);
$stmt6->execute();
$stmt6->bind_result($prescription_doctor_title, $prescription_doctor_last_name);
$stmt6->fetch();
$stmt6->close();

//populates the messages info

$stmt7 = $db->prepare("SELECT threadId, patient_patientId, doctor_doctorId FROM thread WHERE patient_patientId = ?");
$stmt7->bind_param('i', $patient_id);
$stmt7->execute();
//$result = $stmt7->get_result();
//while ($row = $result->fetch_assoc()) {
//  array_push($message_thread_id, "7");
//}
$stmt7->bind_result($message_thread_id, $message_patient_id, $message_doctor_id);
$stmt7->fetch();
$stmt7->close();

$stmt8 = $db->prepare("SELECT body, timeWritten, sender FROM message WHERE thread_threadId = ?");
$stmt8->bind_param('i', $message_thread_id);
$stmt8->execute();
$stmt8->bind_result($message_body, $message_time_written, $message_sender_type);
$stmt8->fetch();
$stmt8->close();

$stmt9 = $db->prepare("SELECT title, lastName  FROM doctor WHERE doctorId = ?");
$stmt9->bind_param('i', $message_doctor_id);
$stmt9->execute();
$stmt9->bind_result($message_sender_title, $message_sender_name);
$stmt9->fetch();
$stmt9->close();

$stmt10 = $db->prepare("SELECT title, lastName  FROM patient WHERE patientId = ?");
$stmt10->bind_param('i', $message_patient_id);
$stmt10->execute();
$stmt10->bind_result($message_reciever_title, $message_reciever_name);
$stmt10->fetch();
$stmt10->close();

?>

<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>Softcare Dashboard</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">


  <link rel="manifest" href="site.webmanifest">
  <link rel="apple-touch-icon" href="icon.png">

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <!-- Popper JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

  <!-- Latest compiled JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Place favicon.ico in the root directory -->

  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/dashboard.css">

  <meta name="theme-color" content="#fafafa">

  <!-- Jquery script that hides and shows the content depending on which nav option is clicked -->
  <script>
    $(document).ready(function () {
      $("#home").click(function (e) {
        $("#home_content").show();
        $("#appointments_content").hide();
        $("#messages_content").hide();
        $("#prescriptions_content").hide();
        $("#medicaldocs_content").hide();
        e.preventDefault();
      });
      $("#appointments").click(function (e) {
        $("#home_content").hide();
        $("#appointments_content").show();
        $("#messages_content").hide();
        $("#prescriptions_content").hide();
        $("#medicaldocs_content").hide();
        e.preventDefault();
      });
      $("#messages").click(function (e) {
        $("#home_content").hide();
        $("#appointments_content").hide();
        $("#messages_content").show();
        $("#prescriptions_content").hide();
        $("#medicaldocs_content").hide();
        e.preventDefault();
      });
      $("#prescriptions").click(function (e) {
        $("#home_content").hide();
        $("#appointments_content").hide();
        $("#messages_content").hide();
        $("#prescriptions_content").show();
        $("#medicaldocs_content").hide();
        e.preventDefault();
      });
      $("#medicaldocs").click(function (e) {
        $("#home_content").hide();
        $("#appointments_content").hide();
        $("#messages_content").hide();
        $("#prescriptions_content").hide();
        $("#medicaldocs_content").show();
        e.preventDefault();
      });
    });
  </script>
  <script>
    $(window).on("load", function () {
      $("#home_content").show();
      $("#appointments_content").hide();
      $("#messages_content").hide();
      $("#prescriptions_content").hide();
      $("#medicaldocs_content").hide();
    });
  </script>
</head>

<!-- Begin header/navbar section -->
<svg style="display:none;">
  <symbol id="down" viewBox="0 0 16 16">
    <polygon points="3.81 4.38 8 8.57 12.19 4.38 13.71 5.91 8 11.62 2.29 5.91 3.81 4.38" />
  </symbol>
  <symbol id="home_icon" xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 0 24 24" width="22px" fill="#000000">
    <path d="M0 0h24v24H0z" fill="none"/><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
  </symbol>
  <symbol id="users" xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 0 24 24" width="22px" fill="#000000">
    <path d="M0 0h24v24H0z" fill="none"/><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
  </symbol>
  <symbol id="appointments_icon" xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 0 24 24" width="22px" fill="#000000">
    <path d="M0 0h24v24H0z" fill="none"/><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
  </symbol>
  <symbol id="messages_icon" xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 0 24 24" width="22px" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/>
    <path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-4 6V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10c.55 0 1-.45 1-1z"/>
  </symbol>
  <symbol id="prescriptions_icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
    <path d="M0 0h24v24H0zm18.31 6l-2.76 5z" fill="none"/><path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"/>
  </symbol>
  <symbol id="medicaldocs_icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/>
    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
  </symbol>
  <symbol id="collapse" viewBox="0 0 16 16">
    <polygon points="11.62 3.81 7.43 8 11.62 12.19 10.09 13.71 4.38 8 10.09 2.29 11.62 3.81" />
  </symbol>
  <symbol id="search" viewBox="0 0 16 16">
    <path d="M6.57,1A5.57,5.57,0,1,1,1,6.57,5.57,5.57,0,0,1,6.57,1m0-1a6.57,6.57,0,1,0,6.57,6.57A6.57,6.57,0,0,0,6.57,0Z" />
    <rect x="11.84" y="9.87" width="2" height="5.93" transform="translate(-5.32 12.84) rotate(-45)" />
  </symbol>
</svg>

<header class="page-header">
  <nav>
    <div>
      <a href="#0" aria-label="logo" class="logo">
          <img src="img/full-logo.PNG" alt="logo" class="logo" width="140" height="auto">
      </a>
    </div>
    <button class="toggle-mob-menu" aria-expanded="false" aria-label="open menu">
      <svg width="20" height="20" aria-hidden="true">
        <use xlink:href="#down"></use>
      </svg>
    </button>
    <ul class="admin-menu">
      <li class="menu-heading">
        <h3><?php echo $patient_first_name, " ", $patient_last_name; ?></h3>
      </li>
      <li>
        <a href="#" id="home">
          <svg>
            <use xlink:href="#home_icon"></use>
          </svg>
          <span>Home</span>
        </a>
      </li>
      <li>
        <a href="#" id="appointments">
          <svg>
            <use xlink:href="#appointments_icon"></use>
          </svg>
          <span>Appointments</span>
        </a>
      </li>
      <li>
        <a href="#" id="messages">
          <svg>
            <use xlink:href="#messages_icon"></use>
          </svg>
          <span>Messages</span>
        </a>
      </li>
      <li>
        <a href="#" id="prescriptions">
          <svg>
            <use xlink:href="#prescriptions_icon"></use>
          </svg>
          <span>Prescriptions</span>
        </a>
      </li>
      <li>
        <a href="#" id="medicaldocs">
          <svg>
            <use xlink:href="#medicaldocs_icon"></use>
          </svg>
          <span>Medical Documents</span>
        </a>
      </li>

      <! -- Lazy spacing -->
      <li>
      </li>

      <li>
        <div class="switch">
          <input type="checkbox" id="mode" unchecked>
          <label for="mode">
            <span></span>
            <span>Dark</span>
          </label>
        </div>
        <button class="collapse-btn" aria-expanded="true" aria-label="collapse menu">
          <svg aria-hidden="true">
            <use xlink:href="#collapse"></use>
          </svg>
          <span>Collapse</span>
        </button>
      </li>
    </ul>
  </nav>
</header>



<section class="page-content">
  <section class="search-and-user">
    <form>
      <input type="search" placeholder="Search...">
      <button type="submit" aria-label="submit form">
        <svg aria-hidden="true">
          <use xlink:href="#search"></use>
        </svg>
      </button>
    </form>
    <div class="admin-profile">
      <span class="greeting">Hello <?php echo $patient_first_name ?></span>
      <div class="notifications">
        <span class="badge">1</span>
        <svg>
          <use xlink:href="#users"></use>
        </svg>
      </div>
    </div>
  </section>
  <section class="grid" id="home_content">
    <article>Upcoming appointments</article>
    <article>Messages</article>
    <article>Prescriptions</article>
    <article>Documents</article>
  </section>
  <section class="grid" id="appointments_content">
    <article><h1>Upcoming Appointments</h1></article>
    <article>
      <h1> <?php echo $appointment_time?></h1>
      <h2> <?php echo $appointment_location_name ?></h2>
      <h2> <?php echo $appointment_location_address?></h2>
      <h2> <?php echo $appointment_location_city . " " . $appointment_location_postal ?></h2>
      <h2> <?php echo $appointment_doctor_title . " " . $appointment_doctor_last_name?> </h2>
    </article>
    <article></article>
    <article></article>
  </section>

  <section class="grid" id="messages_content">
    <article><h1>Your Messages</h1></article>
    <article class="message">
      <h2> <?php echo $message_sender_title . " " . $message_sender_name?> </h2>
      <h2> <?php echo $message_reciever_title . " " . $message_reciever_name?> </h2>
      <h2> <?php echo $message_body?> </h2>
      <h2> <?php echo $message_time_written?> </h2>
    </article>
    <article class="message">Message 2</article>
    <article class="message">Message 3</article>
    <article class="message">Message 4</article>
  </section>

  <section class="grid" id="prescriptions_content">
    <article><h1>Your Prescriptions</h1></article>
    <article class="prescription">
      <h2> <?php echo $prescription_medication_name . " " . $prescription_dosage ?></h2>
      <h2> <?php echo $prescription_amount?></h2>
      <h2> <?php echo $prescription_instructions ?></h2>
      <h2> <?php echo $prescription_refills_authorized ?></h2>
      <h2> <?php echo $prescription_refills_date ?></h2>
      <h2> <?php echo $prescription_doctor_title. " " . $prescription_doctor_last_name?> </h2>
    </article>
    <article class="prescription">Prescription 2</article>
  </section>

  <section class="grid" id="medicaldocs_content">
    <article></article>
    <article></article>
  </section>



  <footer class="page-footer">

  </footer>
</section>











<script src="js/vendor/modernizr-3.11.2.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
<script src="js/dashboard.js"></script>

<!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
<script>
  window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
  ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
</script>
<script src="https://www.google-analytics.com/analytics.js" async></script>
</body>

</html>

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
//      appointments[x][0] = patientId
//                     [1] = doctorId
//                     [2] = locationId
//                     [3] = appointmentTime
//                     [4] = reasonForVisit
//                     [5] = dateScheduled
//                     [6] = cancellationRequested
//                     [7] = rescheduleRequested
$appointments = array();
//         locations[x][0] = address
//                     [1] = city
//                     [2] = province
//                     [3] = country
//                     [4] = postal
//                     [5] = name
$locations = array();


//prescription info
//     prescriptions[x][0] = patientId
//                     [1] = doctorId
//                     [2] = medicationId
//                     [3] = amount
//                     [4] = dosage
//                     [5] = instructions
//                     [6] = datePrescribed
//                     [7] = refillsAuthorized
//                     [8] = refillDate
//                     [9] = refillRequested
$prescriptions = array();


//messages info
//           threads[x][0] = threadId
//                     [1] = patientId
//                     [2] = doctorId
$threads = array();
//           threadMessages[x] = x thread
//           threadMessages[x][y] = y message in x thread
//           threadMessages[x][y][0] = body of y message in x thread
//           threadMessages[x][y][1] = timeWritten of y message in x thread
//           threadMessages[x][y][2] = sender of y message in x thread
//
$threadMessages = array();

//medical document info
//  medicalDocuments[x][0] = patientId
//                     [1] = doctorId
//                     [2] = title
//                     [3] = info
//                     [4] = blobinfo
//                     [5] = dateUploaded
$medicalDocuments = array();

//populates patient info
$stmt = $db->prepare("SELECT patientId, title, firstName, lastName FROM patient WHERE phn = ?");
$stmt->bind_param('s', $_SESSION["usrname"]);
$stmt->execute();
$stmt->bind_result($patient_id, $patient_title, $patient_first_name, $patient_last_name);
$stmt->fetch();
$stmt->close();



//Populates the appointment info
$stmt1 = $db->prepare("SELECT patient_patientId, doctor_doctorId, location_locationId, appointmentTime, reasonForVisit, dateScheduled, cancellationRequested, rescheduleRequested FROM appointment WHERE patient_patientId = ?");
$stmt1->bind_param('i', $patient_id);
$stmt1->execute();
$resultAppointments = $stmt1->get_result();
while ($rowAppointments = $resultAppointments->fetch_array(MYSQLI_NUM)) {
  $appointments[] = $rowAppointments;
}
$stmt1->fetch();
$stmt1->close();

for($i = 0; $i <count($appointments); $i++) {
  $query = $appointments[$i][2];

  $stmt = $db->prepare("SELECT address, city, province, country, postal, name FROM location WHERE locationId = ?");
  $stmt->bind_param('i', $query);
  $stmt->execute();
  $resultLocations = $stmt->get_result();
  while($rowLocations = $resultLocations->fetch_array((MYSQLI_NUM))){
    $locations[] = $rowLocations;
  }
  $stmt->fetch();
  $stmt->close();
}

for($i = 0; $i < count($appointments); $i++) {
  $query = $appointments[$i][1];
  $doctor_title = "";
  $doctor_name = "";

  $stmt = $db->prepare("SELECT title, lastName  FROM doctor WHERE doctorId = ?");
  $stmt->bind_param('i', $query);
  $stmt->execute();
  $stmt->bind_result($doctor_title, $doctor_name);
  $stmt->fetch();
  $stmt->close();

  $appointments[$i][1] = $doctor_title . " " . $doctor_name;
}




//populates the prescriptions info
$stmt2 = $db->prepare("SELECT patient_patientId, doctor_doctorId, medication_medicationId, amount, dosage, instructions, datePrescribed, refillsAuthorized, refillDate, refillRequested  FROM prescription WHERE patient_patientId = ?");
$stmt2->bind_param('i', $patient_id);
$stmt2->execute();
$resultPrescriptions = $stmt2->get_result();
while ($rowPrescriptions = $resultPrescriptions->fetch_array(MYSQLI_NUM)) {
  $prescriptions[] = $rowPrescriptions;
}
$stmt2->fetch();
$stmt2->close();

  //changes the medication_id to medication name
for($i = 0; $i < count($prescriptions); $i++){
  $query = $prescriptions[$i][2];
  $prescription_medication_name = "";

  $stmt = $db->prepare("SELECT name  FROM medication WHERE medicationId = ?");
  $stmt->bind_param('i', $query);
  $stmt->execute();
  $stmt->bind_result($prescription_medication_name);
  $stmt->fetch();
  $stmt->close();

  $prescriptions[$i][2] = $prescription_medication_name;
}

  //changes the doctor_id to doctor name
for($i = 0; $i < count($prescriptions); $i++) {
  $query = $prescriptions[$i][1];
  $doctor_title = "";
  $doctor_name = "";

  $stmt = $db->prepare("SELECT title, lastName  FROM doctor WHERE doctorId = ?");
  $stmt->bind_param('i', $query);
  $stmt->execute();
  $stmt->bind_result($doctor_title, $doctor_name);
  $stmt->fetch();
  $stmt->close();

  $prescriptions[$i][1] = $doctor_title . " " . $doctor_name;
  $prescriptions[$i][0] = $patient_title . " " . $patient_last_name;
}


//populates the messages info



$stmt3 = $db->prepare("SELECT threadId, patient_patientId, doctor_doctorId FROM thread WHERE patient_patientId = ?");
$stmt3->bind_param('i', $patient_id);
$stmt3->execute();
$resultThreads = $stmt3->get_result();
  while($rowThreads = $resultThreads->fetch_array(MYSQLI_NUM)){
    $threads[] = $rowThreads;
  }
$stmt3->fetch();
$stmt3->close();

for($i = 0; $i < count($threads); $i++){
  $query = $threads[$i][2];
  $doctor_title = "";
  $doctor_name = "";

  $stmt = $db->prepare("SELECT title, lastName  FROM doctor WHERE doctorId = ?");
  $stmt->bind_param('i', $query);
  $stmt->execute();
  $stmt->bind_result($doctor_title, $doctor_name);
  $stmt->fetch();
  $stmt->close();

  $threads[$i][1] = $patient_title . " " . $patient_last_name;
  $threads[$i][2] = $doctor_title . " " . $doctor_name;

}


for($i = 0; $i < count($threads); $i++){
  $query = $threads[$i][0];
  $messages = array();

  $stmt = $db->prepare("SELECT body, timeWritten, sender FROM message WHERE thread_threadId = ?");
  $stmt->bind_param('i', $query);
  $stmt->execute();
  $resultMessages = $stmt->get_result();
  while($rowMessages = $resultMessages->fetch_array(MYSQLI_NUM)){
    $messages[] = $rowMessages;
  }
  $stmt->fetch();
  $stmt->close();

  array_push($threadMessages, $messages);

}

//populates medical document info
$stmt11 = $db->prepare("SELECT patient_patientId, doctor_doctorId, title, info, file, dateUploaded FROM medicaldocument WHERE patient_patientId = ?");
$stmt11->bind_param('i', $patient_id);
$stmt11->execute();
$resultmedicalDocuments = $stmt11->get_result();
  while ($rowmedicalDocuments = $resultmedicalDocuments->fetch_array(MYSQLI_NUM)) {
      $medicalDocuments[] = $rowmedicalDocuments;
  }
$stmt11->fetch();
$stmt11->close();

  //changes the value in the array from the doctor id to the dr. name
for($i = 0; $i < count($medicalDocuments); $i++) {
  $query = $medicalDocuments[$i][1];
  $doctor_title = "";
  $doctor_name = "";

  $stmt = $db->prepare("SELECT title, lastName  FROM doctor WHERE doctorId = ?");
  $stmt->bind_param('i', $query);
  $stmt->execute();
  $stmt->bind_result($doctor_title, $doctor_name);
  $stmt->fetch();
  $stmt->close();

  $medicalDocuments[$i][1] = $doctor_title . " " . $doctor_name;
  $medicalDocuments[$i][0] = $patient_title . " " . $patient_last_name;
}
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
    <h1>Upcoming Appointments</h1>
    <article class="appointments-table">
      <div class="limiter">
        <div class="table100">
          <table>
            <thead>
              <tr class="table100-head">
                <th class="column1">Date & Time</th>
                <th class="column2">Doctor</th>
                <th class="column3">Reason For Visit</th>
                <th class="column4">Location</th>
                <th class="column6">Date Scheduled</th>
              </tr>
            </thead>
            <tbody>
            <?php for($i = 0; $i < count($appointments); $i++){ ?>
              <tr>
                <td class="column1"><?php echo $appointments[$i][3] ?></td>
                <td class="column2"><?php echo $appointments[$i][1] ?></td>
                <td class="column3"><?php echo $appointments[$i][4] ?></td>
                <td class="column4"><?php echo $locations[$i][0] . ",<br> " . $locations[$i][1] . ",<br> " . $locations[$i][2] . ",<br>" .
                    $locations[$i][3] . ",<br>" . $locations[$i][4] . ",<br>" . $locations[$i][5];   ?></td>
                <td class="column6"><?php echo $appointments[$i][5] ?></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </article>
  </section>

  <section class="grid" id="messages_content">
    <h1>Your Messages</h1>
    <?php
    for($i = 0; $i < count($threadMessages); $i++)
    { ?>
      <article>
        <?php
        for($v = 0; $v < count($threadMessages[$i]); $v++)
        { ?>
          <?php if($threadMessages[$i][$v][2] == "patient")
          { ?>
            <h2>Sender <?php echo $threads[$i][1] ?> </h2>
            <h2>Reciever <?php echo $threads[$i][2] ?> </h2>
            <h2><?php echo $threadMessages[$i][$v][0] ?></h2>
            <h2><?php echo $threadMessages[$i][$v][1] ?></h2>
          <?php } ?>

          <?php if($threadMessages[$i][$v][2] == "doctor")
          { ?>
            <h2>Sender <?php echo $threads[$i][2] ?> </h2>
            <h2>Reciever <?php echo $threads[$i][1] ?> </h2>
            <h2><?php echo $threadMessages[$i][$v][0] ?></h2>
            <h2><?php echo $threadMessages[$i][$v][1] ?></h2>
           <?php  } ?>
        <?php } ?>
      </article>
    <?php } ?>
  </section>

  <section class="grid" id="prescriptions_content">
    <h1>Your Prescriptions</h1>
    <article class="prescription-table">
      <div class="limiter">
        <div class="table100">
          <table>
            <thead>
            <tr class="table100-head">
              <th class="column1">Prescribing Doctor</th>
              <th class="column2">Medication</th>
              <th class="column3">Directions</th>
              <th class="column4">Dosage</th>
              <th class="column5">Course</th>
              <th class="column6">Renew</th>
            </tr>
            </thead>
            <tbody>
            <?php for($i = 0; $i < count($prescriptions); $i++){ ?>
              <tr>
                <td class="column1"><?php echo $prescriptions[$i][1] ?></td>
                <td class="column2"><?php echo $prescriptions[$i][2] . " x" . $prescriptions[$i][3]  ?></td>
                <td class="column3"><?php echo $prescriptions[$i][5] ?></td>
                <td class="column4"><?php echo $prescriptions[$i][4] ?></td>
                <td class="column5"><?php echo $prescriptions[$i][6] . " -<br>" . $prescriptions[$i][8]  ?></td>
                <td class="column6"><button type="button">Renew</button></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </article>
  </section>

  <section class="grid" id="medicaldocs_content">
    <h1>Your Medical Documents</h1>
    <article class="med-docs-table">
      <div class="limiter">
        <div class="table100">
          <table>
            <thead>
            <tr class="table100-head">
              <th class="column1">Doctor</th>
              <th class="column2">File Description</th>
              <th class="column3">Notes</th>
              <th class="column4">Date</th>
              <th class="column6">File</th>
            </tr>
            </thead>
            <tbody>
            <?php for($i = 0; $i < count($medicalDocuments); $i++){ ?>
              <tr>
                <td class="column1"><?php echo $medicalDocuments[$i][1] ?></td>
                <td class="column2"><?php echo $medicalDocuments[$i][2] ?></td>
                <td class="column3"><?php echo $medicalDocuments[$i][3] ?> </h2></td>
                <td class="column4"><?php echo $medicalDocuments[$i][5] ?></td>
                <td class="column6"><button type="button">Download</button></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </article>
  </section>
<!--<?php echo '<img src="data:image/jpeg;base64,'.base64_encode( $medicalDocuments[$i][4] ).'"/>'  ?> Need to work out how to make this file downloadable-->




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

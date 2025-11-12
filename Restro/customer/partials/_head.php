<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Restaurant Point of Sale System">
  <meta name="author" content="MartDevelopers Inc">
  <title>Restaurant Point Of Sale</title>

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="../admin/assets/img/icons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../admin/assets/img/icons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../admin/assets/img/icons/favicon-16x16.png">
  <link rel="manifest" href="../admin/assets/img/icons/site.webmanifest">
  <link rel="mask-icon" href="../admin/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="theme-color" content="#ffffff">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

  <!-- Icons -->
  <link href="assets/vendor/nucleo/css/nucleo.css" rel="stylesheet">
  <link href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">

  <!-- Argon CSS -->
  <link type="text/css" href="assets/css/argon.css?v=1.0.0" rel="stylesheet">

  <!-- SweetAlert -->
  <script src="assets/js/swal.js"></script>

  <!-- ============================= -->
  <!--  SMART ALERT HANDLER LOGIC   -->
  <!-- ============================= -->
  <?php
  // Display SweetAlerts only when non-empty values exist
  if (!empty($success)) {
      echo "
      <script>
        setTimeout(function() {
          swal({
            title: 'Success',
            text: " . json_encode($success) . ",
            icon: 'success',
            button: 'OK'
          });
        }, 150);
      </script>";
  }

  if (!empty($err)) {
      echo "
      <script>
        setTimeout(function() {
          swal({
            title: 'Failed',
            text: " . json_encode($err) . ",
            icon: 'error',
            button: 'OK'
          });
        }, 150);
      </script>";
  }

  if (!empty($info)) {
      echo "
      <script>
        setTimeout(function() {
          swal({
            title: 'Notice',
            text: " . json_encode($info) . ",
            icon: 'info',
            button: 'Got it'
          });
        }, 150);
      </script>";
  }
  ?>
</head>

<!-- ============================= -->
<!-- GLOBAL AJAX FUNCTION SUPPORT -->
<!-- ============================= -->
<script>
  function getCustomer(val) {
    $.ajax({
      type: "POST",
      url: "customer_ajax.php",
      data: { custName: val },
      success: function(data) {
        $('#customerID').val(data);
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
      }
    });
  }
</script>

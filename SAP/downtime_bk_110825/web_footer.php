<?php
$file_name = basename($_SERVER['PHP_SELF']);
if($file_name==''){
	$file_name = "index.php";
}
?>
<!-- Toast-style Alert -->
 
 
<script>
  $(document).ready(function () {
    // Show the popup alert
    $("#popup-alert").fadeIn().delay(3000).fadeOut();
  });
</script>
<style>
    #popup-alert {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1050;
      display: none;
      min-width: 250px;
    }
  </style>
 <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>


    <!-- Slimscroll Plugin Js -->
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="plugins/raphael/raphael.min.js"></script>
    <script src="plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="plugins/chartjs/Chart.bundle.js"></script>


    <!-- Sparkline Chart Plugin Js -->
    <script src="plugins/jquery-sparkline/jquery.sparkline.js"></script>
    
    <!-- Moment Plugin Js -->
    <script src="plugins/momentjs/moment-with-locales.min.js"></script>
    <!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    <!-- Custom Js -->
    <script src="js/admin.js"></script>
  
    <?php
if($file_name=="index.php" || $file_name=="main.php"){
?>
    
     <script src="js/pages/index.js"></script>
<?php
}
?>

    <!-- Demo Js -->
    <script src="js/demo.js"></script>   
</body>
</html>
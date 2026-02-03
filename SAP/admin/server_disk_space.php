<?php
ob_start();
session_start();
include "web_header.php";
include "web_check.php";
include "star_connection.php";

include "web_header.php";
?>
<script type="text/javascript">
jQuery(function () {
	
});
</script>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>Server Disk Space

                          </h2>
                            
<span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">

                        <?php
$dfOutput = shell_exec('df -h'); // Execute the df command

// Split the output into lines
$lines = explode("\n", $dfOutput);

$totalAvailableBytes = 0;

// Iterate through each line starting from the second line (skip the header)
for ($i = 1; $i < count($lines); $i++) {
    // Split the line into columns
    $columns = preg_split('/\s+/', $lines[$i]);

    // Get the available space (column 3) and convert to bytes
    $available = $columns[3];
    $availableBytes = convertToBytes($available);

    // Add to the total available space in bytes
    $totalAvailableBytes += $availableBytes;
}

// Convert the total available space from bytes to GB with one decimal place
$totalAvailableGB = round($totalAvailableBytes / (1024 * 1024 * 1024), 1);

echo "Remaining Free Space in Disk: " . $totalAvailableGB . " GB";

// Function to convert human-readable size to bytes
function convertToBytes($size)
{
    $units = ['B', 'K', 'M', 'G', 'T'];
    $unit = strtoupper(substr($size, -1));
    $size = (float) $size;

    if (in_array($unit, $units)) {
        $size *= pow(1024, array_search($unit, $units));
    }

    return $size;
}
?>

</div>
<?php
    include "web_footer.php";
?>


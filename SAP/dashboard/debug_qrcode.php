<?php
// =======================================================================
// QR CODE DEBUG FILE — Star Cement
// Upload this file next to generate_invoice_pdf.php
// Open in browser: yoursite.com/portal/debug_qrcode.php?invoice_no=INV123
// DELETE THIS FILE after debugging is done!
// =======================================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(session_status() == PHP_SESSION_NONE){ session_start(); }

$invoice_no = isset($_GET['invoice_no']) ? trim($_GET['invoice_no']) : '';

echo '<html><body style="font-family:monospace;font-size:13px;padding:20px;">';
echo '<h2>QR Code Debug — Star Cement Invoice PDF</h2>';

// -----------------------------------------------------------------------
// STEP 1: PHP version & GD info
// -----------------------------------------------------------------------
echo '<h3 style="background:#333;color:#fff;padding:5px;">STEP 1: PHP & GD Info</h3>';
echo '<b>PHP Version:</b> ' . phpversion() . '<br/>';
echo '<b>imagecreatefrombmp() exists:</b> ' . (function_exists('imagecreatefrombmp') ? '<span style="color:green">YES (PHP 7.2+)</span>' : '<span style="color:orange">NO — will use manual decoder (PHP 5.6)</span>') . '<br/>';
echo '<b>GD loaded:</b> ' . (extension_loaded('gd') ? '<span style="color:green">YES</span>' : '<span style="color:red">NO — GD is NOT installed! PDF images will not work.</span>') . '<br/>';
echo '<b>sys_get_temp_dir():</b> ' . sys_get_temp_dir() . '<br/>';
echo '<b>Temp dir writable:</b> ' . (is_writable(sys_get_temp_dir()) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO — cannot write temp files!</span>') . '<br/>';

// -----------------------------------------------------------------------
// STEP 2: Session data check
// -----------------------------------------------------------------------
echo '<h3 style="background:#333;color:#fff;padding:5px;">STEP 2: Session Data</h3>';
if($invoice_no == ''){
    echo '<span style="color:red">No invoice_no in URL. Add ?invoice_no=YOUR_INVOICE_NO to the URL.</span><br/>';
} else {
    echo '<b>Looking for session key:</b> invoice_print_array_' . htmlspecialchars($invoice_no) . '<br/>';
    $session_key = 'invoice_print_array_' . $invoice_no;
    if(!isset($_SESSION[$session_key])){
        echo '<span style="color:red">SESSION KEY NOT FOUND. Session may have expired. Go back to invoice list page first.</span><br/>';
        echo '<br/><b>All session keys currently set:</b><br/><pre>';
        foreach($_SESSION as $k => $v){ echo htmlspecialchars($k) . '<br/>'; }
        echo '</pre>';
    } else {
        echo '<span style="color:green">Session key found! Row count: ' . count($_SESSION[$session_key]) . '</span><br/>';
        $inv = $_SESSION[$session_key][0]; // first row

        // -----------------------------------------------------------------------
        // STEP 3: QrCode1 value inspection
        // -----------------------------------------------------------------------
        echo '<h3 style="background:#333;color:#fff;padding:5px;">STEP 3: QrCode1 Raw Value</h3>';
        $QrCode1 = isset($inv['QrCode1']) ? $inv['QrCode1'] : '';

        echo '<b>QrCode1 key exists in session data:</b> ' . (isset($inv['QrCode1']) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO — QrCode1 key missing from invoice array!</span>') . '<br/>';
        echo '<b>QrCode1 length (raw):</b> ' . strlen($QrCode1) . ' chars<br/>';
        echo '<b>QrCode1 empty:</b> ' . ($QrCode1 == '' ? '<span style="color:red">YES — value is empty!</span>' : '<span style="color:green">NO</span>') . '<br/>';

        if($QrCode1 != ''){
            echo '<b>First 100 chars of QrCode1:</b><br/><div style="background:#f0f0f0;padding:5px;word-break:break-all;">' . htmlspecialchars(substr($QrCode1,0,100)) . '</div><br/>';
            echo '<b>Last 20 chars:</b> <span style="background:#f0f0f0;">' . htmlspecialchars(substr($QrCode1,-20)) . '</span><br/>';

            // -----------------------------------------------------------------------
            // STEP 4: base64 decode check
            // -----------------------------------------------------------------------
            echo '<h3 style="background:#333;color:#fff;padding:5px;">STEP 4: Base64 Decode</h3>';
            $b64_clean = str_replace(array("\r","\n","\t"," "), '', $QrCode1);
            echo '<b>After whitespace strip, length:</b> ' . strlen($b64_clean) . ' chars<br/>';

            $bmp_data = base64_decode($b64_clean);
            if($bmp_data === false){
                echo '<span style="color:red">base64_decode() FAILED — QrCode1 is not valid base64!</span><br/>';
            } else {
                echo '<b>Decoded binary size:</b> ' . strlen($bmp_data) . ' bytes<br/>';
                echo '<b>First 2 bytes (BMP magic):</b> ' . bin2hex(substr($bmp_data,0,2)) . ' (should be <b>424d</b> for BMP)<br/>';

                $magic = bin2hex(substr($bmp_data,0,2));
                if($magic === '424d'){
                    echo '<span style="color:green">Valid BMP signature detected!</span><br/>';
                } elseif($magic === '8950'){
                    echo '<span style="color:blue">This is actually a PNG file (not BMP). Will still work.</span><br/>';
                } elseif($magic === 'ffd8'){
                    echo '<span style="color:blue">This is actually a JPEG file (not BMP). Will still work.</span><br/>';
                } else {
                    echo '<span style="color:red">Unknown file format. Magic bytes: ' . $magic . '. This may not be a valid image.</span><br/>';
                }

                // BMP header info
                if($magic === '424d' && strlen($bmp_data) >= 54){
                    $info = unpack('VheaderSize/Vwidth/Vheight/vplanes/vbits', substr($bmp_data,14,16));
                    echo '<b>BMP width:</b> '   . $info['width']  . 'px<br/>';
                    echo '<b>BMP height:</b> '  . abs((int)$info['height']) . 'px<br/>';
                    echo '<b>BMP bit depth:</b> ' . $info['bits'] . ' bit<br/>';
                }

                // -----------------------------------------------------------------------
                // STEP 5: GD conversion test
                // -----------------------------------------------------------------------
                echo '<h3 style="background:#333;color:#fff;padding:5px;">STEP 5: GD Conversion Test</h3>';
                if(!extension_loaded('gd')){
                    echo '<span style="color:red">GD not loaded — cannot convert image!</span><br/>';
                } else {
                    $tmp_bmp = sys_get_temp_dir() . '/debug_qr_test.bmp';
                    $tmp_png = sys_get_temp_dir() . '/debug_qr_test.png';
                    file_put_contents($tmp_bmp, $bmp_data);
                    echo '<b>Wrote BMP to:</b> ' . $tmp_bmp . '<br/>';

                    if(function_exists('imagecreatefrombmp')){
                        $gd = @imagecreatefrombmp($tmp_bmp);
                        echo '<b>Used:</b> imagecreatefrombmp() — PHP 7.2+ built-in<br/>';
                    } else {
                        // Manual decoder (same as in main file)
                        $gd = manual_bmp_decode($bmp_data);
                        echo '<b>Used:</b> manual BMP decoder (PHP 5.6 path)<br/>';
                    }

                    if($gd === false){
                        echo '<span style="color:red">GD conversion FAILED — could not create image resource from BMP!</span><br/>';
                        echo 'Possible reasons: unsupported BMP compression, corrupt data, or unsupported bit depth.<br/>';
                    } else {
                        echo '<span style="color:green">GD image resource created successfully!</span><br/>';
                        imagepng($gd, $tmp_png);
                        imagedestroy($gd);
                        echo '<b>PNG saved to:</b> ' . $tmp_png . '<br/>';
                        echo '<b>PNG file size:</b> ' . (file_exists($tmp_png) ? filesize($tmp_png) . ' bytes' : 'FILE NOT CREATED') . '<br/>';

                        if(file_exists($tmp_png) && filesize($tmp_png) > 0){
                            echo '<span style="color:green">PNG created successfully!</span><br/>';
                            // Show the image inline
                            $png_b64 = base64_encode(file_get_contents($tmp_png));
                            echo '<br/><b>QR Code preview (rendered from your data):</b><br/>';
                            echo '<img src="data:image/png;base64,' . $png_b64 . '" style="border:1px solid red;width:120px;height:120px;" />';
                            echo '<br/><span style="color:green">✓ If you can see a QR code above, the conversion works! The PDF should show it too.</span><br/>';
                        } else {
                            echo '<span style="color:red">PNG file was not created or is empty!</span><br/>';
                        }

                        @unlink($tmp_bmp);
                        @unlink($tmp_png);
                    }
                }
            }
        }
    }
}

// Manual BMP decoder (copy from main file for testing)
function manual_bmp_decode($bmp_data){
    if(strlen($bmp_data) < 54) return false;
    $header = unpack('vtype/Vfilesize/vreserved1/vreserved2/Voffset', substr($bmp_data,0,14));
    if($header['type'] != 0x4D42) return false;
    $info = unpack('VheaderSize/Vwidth/Vheight/vplanes/vbits/Vcompression/VimageSize/VxPPM/VyPPM/VclrUsed/VclrImportant', substr($bmp_data,14,40));
    $width = $info['width'];
    $height = $info['height'];
    $bits = $info['bits'];
    $offset = $header['offset'];
    $flip = true;
    if($height < 0){ $height = -$height; $flip = false; }
    $img = imagecreatetruecolor($width, $height);
    if(!$img) return false;
    $pos = $offset;
    if($bits == 1){
        $palette = array();
        for($i=0;$i<2;$i++){
            $ppos = 54+$i*4;
            $palette[$i] = array(ord($bmp_data[$ppos+2]),ord($bmp_data[$ppos+1]),ord($bmp_data[$ppos]));
        }
        $rowsize = floor(($width+31)/32)*4;
        for($y=0;$y<$height;$y++){
            $row_y = $flip?($height-1-$y):$y;
            for($x=0;$x<$width;$x++){
                $pp=$pos+$row_y*$rowsize+(int)floor($x/8);
                if($pp>=strlen($bmp_data)) continue;
                $byte=ord($bmp_data[$pp]);
                $bit=($byte>>(7-($x%8)))&1;
                $c=isset($palette[$bit])?$palette[$bit]:array(0,0,0);
                imagesetpixel($img,$x,$y,imagecolorallocate($img,$c[0],$c[1],$c[2]));
            }
        }
    } elseif($bits == 24){
        $rowsize=floor(($bits*$width+31)/32)*4;
        for($y=0;$y<$height;$y++){
            $row_y=$flip?($height-1-$y):$y;
            for($x=0;$x<$width;$x++){
                $pp=$pos+$row_y*$rowsize+$x*3;
                if($pp+2>=strlen($bmp_data)) continue;
                $b=ord($bmp_data[$pp]);$g=ord($bmp_data[$pp+1]);$r=ord($bmp_data[$pp+2]);
                imagesetpixel($img,$x,$y,imagecolorallocate($img,$r,$g,$b));
            }
        }
    } else { imagedestroy($img); return false; }
    return $img;
}

echo '</body></html>';
?>

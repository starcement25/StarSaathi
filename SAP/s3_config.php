<?php 
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$s3 = S3Client::factory(array(
    'version' => 'latest',
    'region'  => 'ap-south-1',
    'credentials' => array(
        'key'    => 'YOUR_ACCESS_KEY',
        'secret' => 'YOUR_SECRET_KEY'
    )
));

$bucket = "your-bucket-name";
?>
<?php
include "web_check.php";
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
$start_slider = "start_slider";

$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$max_image_size = 10;
$max_image_count = 4;

$file_dir = "../slider/";
$file_url_prefix = $server_url."slider/";
$new_qry_string_filtered = "";

$new_qry_string_filtered = "";
if(@$_POST["sld_upld"]=="Upload"){
/*echo "<pre>";
print_r($_FILES);
echo "</pre>";*/
$type_err = "NO";
$type_err_text = " Image type should be jpeg/jpg/png.";
$size_err = "NO";
$size_err_text = " Image size should be less than $max_image_size MB.";
$default_err = "NO";
$default_err_text = "";	
$tot_file_count = count($_FILES['slider_img_file']['name']);
$un_upload_file_cnt = 0;
if($tot_file_count>0){
	foreach($_FILES['slider_img_file']['name'] as $key=>$val){
		$the_image_name = $_FILES['slider_img_file']['name'][$key];
		$the_tmp_name 	= $_FILES['slider_img_file']['tmp_name'][$key];
		$the_size 		= $_FILES['slider_img_file']['size'][$key];
		$the_size_in_mb = ($the_size/(1024*1024));
		$the_type 		= $_FILES['slider_img_file']['type'][$key];
		$the_error 		= $_FILES['slider_img_file']['error'][$key];

if(!in_array($the_type,$supported_mime_type)){
		$un_upload_file_cnt++;
		$type_err = "YES";
		$default_err = "YES";
		}else{

if($the_size_in_mb<=$max_image_size){		
$the_image_name = str_replace(" ","_",$the_image_name);
$the_image_name = str_replace("'","",$the_image_name);
$the_image_name = str_replace("-","",$the_image_name);
$unid = uniqid();
$prod_other_img_rand_number = rand(1,9).rand(0,9).rand(0,9).rand(1,9).rand(1,9);
$the_image_name = "slider_".$unid.time().$prod_other_img_rand_number."_".$the_image_name;
$upload_the_ad_file = move_uploaded_file($the_tmp_name,$file_dir.$the_image_name);
if($upload_the_ad_file){
$sql_dp_oimg = "insert into $start_slider (`image_name`) values ('$the_image_name')";
$res_dp_oimg = mysql_query($sql_dp_oimg);
if($res_dp_oimg){	
}else{
$un_upload_file_cnt++;
$default_err = "YES";	
}
}else{
$default_err = "YES";
$un_upload_file_cnt++;
}
}else{
$un_upload_file_cnt++;
$size_err = "YES";
$default_err = "YES";	
}
		}

	}

$process_msg = "";
if($default_err=="YES"){
$process_msg .= "Failed to upload $un_upload_file_cnt files.";
if($type_err=="YES"){
$process_msg .= $type_err_text;	
}
if($size_err=="YES"){
$process_msg .= $size_err_text;	
}
}else{
$process_msg .= "Successfully uploaded.";	
}

$submsg = $process_msg;
}else{
$submsg = 'Please choose at least one image.';	
}	
}




if(isset($_GET["dlt_slider_id"]) && @$_GET["dlt_slider_id"]!=''){
 $dlt_slider_id = $_GET["dlt_slider_id"];
 
$pgsqlck = "select `image_name` from $start_slider where `id`='$dlt_slider_id'";
$pgresck = mysql_query($pgsqlck);
$total_pgresck = mysql_num_rows($pgresck);
if($total_pgresck>0){
$rowck = mysql_fetch_assoc($pgresck);
$the_slider_imageck = $rowck["image_name"] ? trim($rowck["image_name"]) : "";

$news_img_sql = "delete from $start_slider where `id`='$dlt_slider_id'";
$news_img_res = mysql_query($news_img_sql);
if($the_slider_imageck!=""){
	if(file_exists($file_dir.$the_slider_imageck)){
		unlink($file_dir.$the_slider_imageck);
	}
}
}


$submsg = 'The slider successfully deleted.';
}

$add_page_name = "home_page_slider_list.php";
$page_name = "home_page_slider_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `id` from $start_slider";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/


include "web_header.php";
?>
<style>
.table-bordered thead tr th{
	padding:5px;
}
.teEachField{
	display:block;
	width:200px;
	word-wrap: break-word;
	font-size: 12px;
}
.add_button{
  background-color:#2196F3;
  background-image:linear-gradient(#2196F3, #2196F3);
  border-color:#21759B #21759B #1E6A8D;
  box-shadow:rgba(120, 200, 230, 0.5) 0 1px 0 inset;
  color:#FFFFFF;
  cursor:pointer;
  display:block;
  float:right;
  font-size:12px;
  margin-right:7px;
  padding:7px;
  text-decoration-line:none;
  text-decoration-style:solid;
  text-shadow:rgba(0, 0, 0, 0.1) 0 1px 0;
  font-weight:bold;
}

[type="radio"]:checked + label:after,
[type="radio"].with-gap:checked + label:after {
  background-color: <?php echo $colour; ?> !important;
}
[type="radio"]:not(:checked) + label:before,
[type="radio"]:not(:checked) + label:after {
  border: 2px solid <?php echo $colour; ?> !important;
}
[type="radio"]:checked + label:after,
[type="radio"].with-gap:checked + label:before,
[type="radio"].with-gap:checked + label:after {
  border: 2px solid <?php echo $colour; ?> !important;
}
.edt_btn_cls{
	float:left;
}
.dlt_btn_cls{
	float:left;
	margin-left:10px;
}
.clear_span{
	clear:both;
	display:block;
}
.wrapper_scrl{
border: none;
overflow-x: scroll;
overflow-y:hidden;
height: 20px;
}
.wrapper_scrl_div{
height: 20px;	
}
.evntImg{
	width:150px;
	height:100px;
}
</style>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="card">
<div class="header">
<h2>App Slider(<?php echo $total_pgres;?>)&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;
<span style="text-align: left;font-size: 12px;width: 246px;display: inline-block;" id="success_msg" ><?php echo $submsg; ?></span>
</h2>
<span style="clear:both;display:block;"></span>
<div class="row clearfix">
<form action="" method="POST" enctype="multipart/form-data" name="slider_upld_frm" class="slider_upld_frm" id="slider_upld_frm">
<div class="row clearfix">
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
Choose Slider Images:<br>
(size/ratio: 1024x500)
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="file" class="form-control" name="slider_img_file[]" id="slider_img_file" multiple>
</div>
<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="submit" class="btn bg-red waves-effect srch_btn" name="sld_upld" value="Upload"  />
</div>
<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 add_top_bottom_padding">
</div>
</div>
</form>   
</div>
<span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
 <span style="display:block; clear:both;"></span>
<div class="wrapper_scrl">
    <div class="wrapper_scrl_div">
    </div>
</div>

<div class="table-responsive tr_for_scroll">
<table class="table table-bordered table-striped table-hover table_for_scroll">
<thead>
<tr>
<th>Slider&nbsp;Image</th>
<th style="width:120px;">Action</th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "select * from $start_slider order by `id` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		
$the_slider_id = $row1["id"] ? trim($row1["id"]) : "";

$the_slider_image = $row1["image_name"] ? trim($row1["image_name"]) : "";
if($the_slider_image!=""){
	if(file_exists($file_dir.$the_slider_image)){
		$slider_image_url = $file_url_prefix.$the_slider_image;
	}else{
		$slider_image_url = "";
	}
}else{
	$slider_image_url = "";
}
?>
<tr>
<td>
<?php
if($slider_image_url!=""){ ?>
	<img src="<?php echo $slider_image_url;?>" class="evntImg" />
<?php }else{
	echo "Not found";
}
?>
</td>
<td style="padding:5px;width:120px;" class="td_action">
<a href="javascript:void(0);" class="btn bg-red waves-effect dlt_slider_cls" dlt_slider_id="<?php echo $the_slider_id;?>" style="padding:5px;display:inline-block;">DELETE</a>
<span class="clear_span"></span>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="2">No slider found.</td>
</tr>
<?php
}
?>

</tbody>
</table>
                            </div>
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
                        </div>
                   
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->
            
            <!-- #END# Exportable Table -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){

var tr_for_scroll = jQuery(".tr_for_scroll").width();
var table_for_scroll = jQuery(".table_for_scroll").width();
jQuery(".wrapper_scrl").css("width",tr_for_scroll+"px");
jQuery(".wrapper_scrl_div").css("width",table_for_scroll+"px");

jQuery(".wrapper_scrl").scroll(function(){
jQuery(".tr_for_scroll")
.scrollLeft(jQuery(".wrapper_scrl").scrollLeft());
});
jQuery(".tr_for_scroll").scroll(function(){
jQuery(".wrapper_scrl")
.scrollLeft(jQuery(".tr_for_scroll").scrollLeft());
});		

var imgs = '<img src="images/ajax-loader.gif"/>';
var done_img = '<img src="images/success_tick.png"/>';


jQuery("form#slider_upld_frm").submit(function(){
var no_of_img = 4;
slider_img_file_element = jQuery("#slider_img_file");
var slider_img_file = slider_img_file_element.val();
if(slider_img_file==""){
alert("Please choose at least one image.");
return false;
}else if( slider_img_file_element.get(0).files.length > no_of_img ){
alert("Maximum image upload limit is "+no_of_img+".");
return false;
}else{
var count = 0;
var img = "";
var img_siz = "";
var img_siz_in_mb = "";
var mx_sz_cnt = 0;
var ext_cnt = 0;
var mx_img_siz = 10;
var extension ="";
exc_msg = "";
for (var i = 0; i < slider_img_file_element.get(0).files.length; ++i) {
img = slider_img_file_element.get(0).files[i].name;
img_siz = slider_img_file_element.get(0).files[i].size;
img_siz_in_mb = (img_siz/(1024*1024));
if(img_siz_in_mb>mx_img_siz){
mx_sz_cnt = mx_sz_cnt+ 1;
count= count+ 1
}
extension = img.split('.').pop().toUpperCase();
if(extension!="PNG" && extension!="JPG" && extension!="JPEG"){
count= count+ 1
ext_cnt = ext_cnt+ 1;
}
}

if( count> 0){
if(ext_cnt>0){
exc_msg = " Please select valid images(PNG/JPG/JPEG).";
alert(exc_msg);
return false;
}else if(mx_sz_cnt>0){
exc_msg = " Image size should be less than "+mx_img_siz+".";
alert(exc_msg);
return false;
}else{
return true;	
}
}else{
return true;	
}
}
});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});

 jQuery(document).on('click', '.dlt_slider_cls', function(event){
		var dlt_slider_id = jQuery(this).attr("dlt_slider_id");
		if(dlt_slider_id!=''){
			var r = confirm("Do you want to delete the slider image?");
			if (r == true) {
			window.location = '<?php echo $page_name;?>?dlt_slider_id='+dlt_slider_id+'&paged=<?php echo $page;?>';
			} else {
			return false;
			}
		}
	}); 


setTimeout(function(){
    jQuery("#success_msg").html("");
},15000);

	
});
	</script>
<?php
include "web_footer.php";
?>
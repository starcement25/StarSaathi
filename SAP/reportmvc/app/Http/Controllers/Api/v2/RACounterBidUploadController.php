<?php
namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Database\DbOnTheFly;
use App\Helpers\Apicommonfunction;
use Session;
use DB;


class RACounterBidUploadController extends Controller{

	/**
     * [dydb this function use to connect database on the flay]
     * @param  [varcar] $dbname [database name]
     * @return [object]         [databse connection object]
     */
    public function dydb($dbname){
      $otf = new DbOnTheFly(['database' => $dbname]);
      return $otf;
    }
    public function uploadcounterbidstatus(Request $request){
        $nick_name=Apicommonfunction::decrypt($request->nickname);
		$emp_code=Apicommonfunction::decrypt($request->emp_code);
        $db_name='acedns_'.strtoupper($nick_name);
        $dydb =$this->dydb($db_name);
        $CUTDB = $dydb->getConnection();
        $flag='';
        //$device_id=Apicommonfunction::decrypt($request->deviceid);
        //$verificationcode=Apicommonfunction::decrypt($request->verificationcode);
		 //$isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
        //if($isverify==1){
			//$body=file_get_contents('php://input');
					$body=Apicommonfunction::decrypt($request->xmldata);

					$date=gmdate('d',strtotime('+330 minute'));
					$month=gmdate('m',strtotime('+330 minute'));
					$year=gmdate('Y',strtotime('+330 minute'));

					$hour=gmdate('H',strtotime('+330 minute'));
					$minute=gmdate('i',strtotime('+330 minute'));
					$second=gmdate('s',strtotime('+330 minute'));
					$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
					$plant_name_array=array();
					$bid_id_array=array();
					$prod_code_array=array();
					$indicative_rate_array=array();
					$bid_rate_array=array();
					
					$CUTDB->beginTransaction();
					$xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
					foreach ($xml as $counter_bid_rate) {
						foreach ($counter_bid_rate->location as $location) {
							$emp_code=$location->emp_code;
							$trans_id=$location->trans_id;
							$latt=$location->latt;
							$longi=$location->longi;
							$date=$location->date;

							$sqlselectlocation=$CUTDB->table('location')
									->select('location.trans_id')
									  ->where('location.trans_id', '=' ,$trans_id)
									  ->first();
						   if(count($sqlselectlocation) >0){
							   $sqlupdatelocation=$CUTDB->table('location')
												   ->where('trans_id', $trans_id)
												   ->limit(1)
												   ->update(array('emp_code'=>$emp_code,'latt'=>$latt,'longi'=>$longi));
						   }
						   else{
							   $sqlInsertlocation=$CUTDB->table('location')
											->insert(array('emp_code'=>$emp_code,
											'trans_id'=>$trans_id,
											'latt'=>$latt,
											'longi'=>$longi,
											'date'=>$date,
											'updatetime'=>$location_date,
											));
							  if($sqlInsertlocation){
									$flag=5;
							  }
							  else{
									$CUTDB->rollBack();
 								  echo $flag=Apicommonfunction::encrypt('fail');
 								  return;
							  }
						   }
						}
						foreach ($counter_bid_rate->counter_bid_rate_data as $counter_bid_rate_data) {
							foreach($counter_bid_rate_data->counter_bid_rate_details as $counter_bid_rate_details){
									$bid_id=$counter_bid_rate_details->bid_id;
									$prod_code=$counter_bid_rate_details->prod_code;
									$bid_status=$counter_bid_rate_details->bid_status;
									$counter_bid_id=$counter_bid_rate_details->counter_bid_id;
									
									$sqlupdatecounterbidstatus=$CUTDB->table('RA_bid_rate_details')
													   ->where('bid_id', $bid_id)
													   ->where('prod_code', $prod_code)
													   ->update(array('bid_status'=>$bid_status,'counter_bid_id'=>$counter_bid_id));
									  if($sqlupdatecounterbidstatus){
											$flag=5;
										}
									  else {
										$CUTDB->rollBack();
										echo $flag=Apicommonfunction::encrypt('fail');
										return;
									  }
									}
								}
						}// End of foreach of xml
					if($flag==5){
						$CUTDB->commit();
						echo Apicommonfunction::encrypt('success');
					}
				/*}// End of verify if
				else{
					echo Apicommonfunction::encrypt('404');
				}*/
		    $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        	$url = url('/api/v1/counterbidstatusupload?nick_name='.$nick_name.'&emp_code='.$emp_code);
        	Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
    }
}

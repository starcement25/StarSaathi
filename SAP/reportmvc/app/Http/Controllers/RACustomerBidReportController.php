<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Helpers\Reverseauction;
use App\Helpers\Route;
use App\Helpers\Commonfunctions;
use Session;

class RACustomerBidReportController extends Controller
{
    private $sdbname;

    public function databasename()
    {
        $sdbname =Session::get('DBNAME');
        return $sdbname;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $sdbname =$this->databasename();
        return view('route.index',compact('routes'));
    }

    /**
     * [customerbidreports This function return the customer and date wise bid details]
     * @return [type] [description]
     */
    public function customerbidreports(){
      $dbname =$this->databasename();
      $customerlist =Route::getCustomerlist($dbname);
	  //$customerlist ='';
      $reoprtdata='';
      return view('RAbid.RAcustomerbidreport',compact('customerlist','reoprtdata'));
    }
    public function showcustomerbidreports(){
      $dbname =$this->databasename();

      $empid=Input::get('emp_list');
      $newempid=implode("','",$empid);
      $startdate=date('Y-m-d',strtotime(Input::get('start_date')));
      $enddate=date('Y-m-d',strtotime(Input::get('end_date')));
      $emplist =Route::getEmplist($dbname);
      $reoprtdata=Route::getRouteReport($dbname,$newempid,$startdate,$enddate);
      return view('route.routeplanreport',compact('emplist','reoprtdata'));
    }
	public function bidcenterreports(){
      $dbname =$this->databasename();
      $reoprtdata='';
      return view('RAbid.bidcenterreport',compact('reoprtdata'));
    }
	public function showbidcenterreports(){
      $dbname =$this->databasename();
	  $startdate=date('Y-m-d',strtotime(Input::get('start_date')));
	  //$customerlist =Route::getCustomerlist($dbname);
      $reoprtdata=Reverseauction::getBidCenterReport($dbname,$startdate);
      return view('RAbid.bidcenterreport',compact('reoprtdata'));
    }
   public function bidcenterexportcsv(Request $request)
    {
        $dbname =$this->databasename();
      	$CUTDB = Reverseauction::dydb($dbname);

 		 $startdate=date('Y-m-d',strtotime(Input::get('start_date')));		
		 $reportlist=$CUTDB->select("SELECT DISTINCT RAD.*,PM.prod_desc,CM.customer_name,DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%d-%m-%Y') AS bid_date 
									FROM `product_master` PM,RA_bid_rate_details RAD,customer_master CM 
		 							WHERE RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code 
									AND DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%Y-%m-%d')='".$startdate."' ORDER BY CM.customer_name ASC");

        $data = "Date,customer name,Prod Desc,Bid qty,Bid rate,Base rate,Freight,GST,counter bid rate,status"."\n";
        foreach($reportlist as $report) {
                $bid_date=$report->bid_date;
				$customer_name=$report->customer_name;
				$prod_desc=$report->prod_desc;
				$qty=$report->qty;
				$bid_rate=$report->bid_rate;
				$base_rate=$report->base_rate;
				$freight=($report->primary_freight+$report->secondary_freight+$report->depot_cost);
				$GST_value=$report->GST_value;
				$counter_bid_rate=$report->counter_bid_rate;
				$bid_status=$report->bid_status;
				if($bid_status =='') $bid_status='CB';
				else				  $bid_status=$bid_status;
				
                $data .=$bid_date.",".$customer_name.",".$prod_desc.",".$qty.",".$bid_rate.",".$base_rate.",".$freight.",".$GST_value.",".$counter_bid_rate.",".
				$bid_status."\n";
        }
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=Bidcenter.csv');
        echo $data;
        exit();
    }
	public function bidstatuschange(Request $request){
      $dbname =$this->databasename();
	  $CUTDB = Reverseauction::dydb($dbname);
	  
	  $startdate=date('Y-m-d',strtotime(Input::get('startdateval')));
	  $bid_id=$request->input('bid_id');
	  $prod_code=$request->input('prod_code');
	  $bidstatus=$request->input('bidstatus');
	  //$customerlist =Route::getCustomerlist($dbname);
	  $date=gmdate('d',strtotime('+330 minute'));
	  $month=gmdate('m',strtotime('+330 minute'));
	  $year=gmdate('Y',strtotime('+330 minute'));
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  $datetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

	  $CUTDB->table('RA_bid_rate_details')
					->where('bid_id', $bid_id)
					->where('prod_code', $prod_code)
					->update(array('bid_status' => $bidstatus,'download_time'  => $datetime));
     $reoprtdata=Reverseauction::getBidCenterReport($dbname,$startdate);
     return view('RAbid.bidcenterreport',compact('reoprtdata'))->with('successMsg','Updated Successfully!.');
    }
}

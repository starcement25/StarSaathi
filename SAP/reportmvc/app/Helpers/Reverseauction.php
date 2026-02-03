<?php
namespace App\Helpers;
use App\Database\DbOnTheFly;
class Reverseauction {

   /**
     *  @param  string  $dbname
     *
     * @return databse object
    */
    public static function dydb($dbname)
    {
       $otf = new DbOnTheFly(['database' => $dbname]);
       $CUTDBOBJ = $otf->getConnection();
       return $CUTDBOBJ;
    }

    /**
      *  @param  string  $dbname
      *
      * @return userlist object
     */

    public static function getAllEmployeeList($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $employeelists = $CUTDB->table('employee_master')
                               ->select('dns_emp_code','emp_name','branch_code','email','phone_no','reporting_to','acedns','emp_code')
                               ->paginate(500000000000000);
        return $employeelists;

    }

    public static function getBrnlist($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $brnlists = $CUTDB->table('branch_master')
                ->where('acedns', 'Y')
                ->lists('branch_name','branch_code');
        return $brnlists;

    }

    public static function getEmplist($dbname)
    {
  		  $CUTDB = self::dydb($dbname);
        $emplists = $CUTDB->table('employee_master')
                ->where('acedns', 'Y')
                ->lists('emp_name','emp_code');
        return $emplists;

    }

    public static function getVarticallist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $emplists = $CUTDB->table('vartical_master')
                ->where('acedns', 'Y')
                ->lists('vartical_name','vartical_name');
        return $emplists;

    }
	public static function getOilgrouplist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $oilgrouplists = $CUTDB->table('product_group_master')
                ->where('acedns', 'Y')
                ->lists('product_group_code','product_group_name');
        return $oilgrouplists;

    }
	public static function getPlantlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $plantlists = $CUTDB->table('branch_master')
				->distinct()
                ->where('acedns', 'Y')
                ->lists('plant_name');
        return $plantlists;
    }
	public static function getproductlist($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $prodlists = $CUTDB->table('product_master')
				->distinct()
                ->where('acedns', 'Y')
                ->lists('prod_desc');
        return $prodlists;
    }
	public static function getproductlistconverion($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $prodlistsconversion = $CUTDB->table('product_unit_coversion_matrix')
				->distinct()
				->select('mapped_prod_code','mapped_prod_desc')
				->orderBy('mapped_prod_desc', 'asc')
				->paginate(500000000000000);
        return $prodlistsconversion;
    }
	public static function getplantwisereleasedrate($dbname)
    {
        $CUTDB = self::dydb($dbname);
        $plantwisereleasedrate = $CUTDB->select("SELECT DISTINCT PR.plant_name,PR.prod_code,PR.release_rate,PR.counter_bid_limit,PM.prod_desc,PR.base_rate,PR.indicative_rate,PR.addition,PR.multiply,PR.conversion_one,PR.conversion_two
							FROM plant_product_wise_RA_rate PR INNER JOIN product_master PM ON PM.dns_prod_code=PR.prod_code AND PR.acedns='Y' ORDER BY PR.plant_name ASC");
        return $plantwisereleasedrate;
    }
	public static function getBidCenterReport($dbname,$startdate)
    {
        $CUTDB = self::dydb($dbname);
        $reportlist=$CUTDB->select("SELECT DISTINCT RAD.*,PM.prod_desc,CM.customer_name,DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%d-%m-%Y') AS bid_date 
									FROM `product_master` PM,RA_bid_rate_details RAD,customer_master CM 
		 							WHERE RAD.prod_code=PM.dns_prod_code AND RAD.customer_code=CM.customer_code 
									AND DATE_FORMAT(SUBSTRING(RAD.bid_id,-14,8),'%Y-%m-%d')='".$startdate."' ORDER BY RAD.bid_status ASC,CM.customer_name ASC");

        return $reportlist;
    }
	public static function getlastreleasedrate($dbname,$plant_name,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$plantwisereleasedrate=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.release_rate')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->where('plant_product_wise_RA_rate.plant_name', '=' ,$plant_name)
			->first();
			 if(count($plantwisereleasedrate)>0){
				$lastreleasedrate=$plantwisereleasedrate->release_rate;
			 }
			 else $lastreleasedrate='';
        return $lastreleasedrate;
    }
	public static function getlastreleasedratecounterbidjump($dbname,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$plantwisereleasedratecounterbidjump=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.counter_bid_jump')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->first();
			 if(count($plantwisereleasedratecounterbidjump)>0){
				$counter_bid_jump=$plantwisereleasedratecounterbidjump->counter_bid_jump;
			 }
			 else {
				 $counter_bid_jump='';
			 }
        return $counter_bid_jump;
    }
	public static function getlastreleasedratecounterbidlimit($dbname,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$lastreleasedratecounterbidlimit=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.counter_bid_limit')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->first();
			 if(count($lastreleasedratecounterbidlimit)>0){
				$counter_bid_limit=$lastreleasedratecounterbidlimit->counter_bid_limit;
			 }
			 else {
				 $counter_bid_limit='';
			 }
        return $counter_bid_limit;
    }
	public static function getlastreleasedratejumpir($dbname,$prod_code)
    {
		$CUTDB = self::dydb($dbname);
		$lastreleasedratejumpir=$CUTDB->table('plant_product_wise_RA_rate')
			->select('plant_product_wise_RA_rate.rate_jump_IR')
			->where('plant_product_wise_RA_rate.acedns', '=' ,'Y')
			->where('plant_product_wise_RA_rate.prod_code', '=' ,$prod_code)
			->first();
			 if(count($lastreleasedratejumpir)>0){
				$rate_jump_IR=$lastreleasedratejumpir->rate_jump_IR;
			 }
			 else {
				 $rate_jump_IR='';
			 }
        return $rate_jump_IR;
    }
}

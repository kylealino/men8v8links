<?php
namespace App\Models;
use CodeIgniter\Model;

class MyPOSConnModel extends Model
{
	protected $dbconn;

	public function __construct()
	{
		parent::__construct();
		$this->session = session();
		$this->myusermod = model('App\Models\MyUserModel');
		$this->db_erp = $this->myusermod->mydbname->medb(0);
		$this->myposdbconn = $this->connectdb();
	}
    
	public function connectdb() { 
		$str = "select * from {$this->db_erp}.mst_posdb_conn where `POS_ACTIVE` = 'Y'";
		$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->getRowArray();
		$serverName = $rw['POS_SERVER'];
		$uid = $rw['POS_USER'];
		$pwd = $rw['POS_PASSWD'];
		$databaseName = $rw['POS_DB'];
		$q->freeResult();
		
		$connectionInfo = array( "UID" => $uid,"PWD" => $pwd,"Database" => $databaseName); 
		$connectionInfo = array( "Database"=> $databaseName, "UID" => $uid, "PWD"=>$pwd);
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		$this->dbconn = $conn;
		return $conn;
	}  //end connectdb
    
	public function POS_check_promo_exists($stockno,$bcode,$mstart_datetime,$mend_datetime) { 
		$adata = array();
		if ($this->myposdbconn): 
			$str = "EXEC [diQtech_db].[dbo].[spMyPromoExists]
					@StockNumber = N'{$stockno}',
					@BranchCode = N'{$bcode}',
					@start_date = N'{$mstart_datetime}',
					@end_date = N'{$mend_datetime}'
			";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array(), array("Scrollable"=>"buffered") );
			if ( $stmt === false):
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			endif;
			$row_count = sqlsrv_num_rows( $stmt );
			$meproditems = '';
			if ($row_count > 0):
				$nn = 1;
				while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
					$adata['code'] = $row['code'];
					$adata['start_date'] = $row['start_date'];
					$adata['end_date'] = $row['end_date'];
					$adata['me_promo_type'] = $row['me_promo_type'];
				}
				$adata['nrecsme'] = $row_count;
			endif;
			sqlsrv_free_stmt( $stmt);
			return $adata;
		endif;
	} //end POS_check_promo_exists
	
	public function POS_Check_Branch_Price($B_CODE='E0023',$POS_PRODID=0) {
		$nprice = 0;
		if ($this->myposdbconn): 
			$str = "
			select aa.[price] from [diQtech_db].[dbo].[diQt_Pricing] aa where aa.[branch_id] = (select top 1 [id] from [diQtech_db].[dbo].[diQt_Branch] where [code] = ?) 
			and aa.[product_id] = ?";
			$stmt = sqlsrv_query( $this->myposdbconn, $str,array($B_CODE,$POS_PRODID), array("Scrollable"=>"buffered") );
			if ( $stmt === false):
				die( print_r( sqlsrv_errors(), true) . chr(13) . chr(10) . 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			endif;
			$row_count = sqlsrv_num_rows( $stmt );
			$meproditems = '';
			if ($row_count > 0):
				$nn = 1;
				$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
				$nprice = $row['price'];
			endif;
			sqlsrv_free_stmt( $stmt);
			return $nprice;
		endif;
	} //end POS_Check_Branch_Price
	    
} //end main class

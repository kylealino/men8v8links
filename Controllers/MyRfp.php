<?php

namespace App\Controllers;

class MyRfp extends BaseController
{
	public function __construct()
	{
		
	}
	
	public function index() { 
		echo view('transactions/ap/rfp-pcf/myrfp');
	} //end index

    public function entry() { 
		echo view('transactions/ap/rfp-pcf/myrfp-entry');
	} //end entry
	
	public function myrfp_view() { 
		echo view('transactions/ap/rfp-pcf/myrfp-view');
	} //end entry

	public function mypcf_view() { 
		echo view('transactions/ap/rfp-pcf/mypcf-view');
	} //end entry

	public function rfpcf_expense_type(){ 
		$term    = $this->request->getVar('term');
		$autoCompleteResult = array();
		
		$str = "
		SELECT
		  `Name`
		FROM `mst_ap_expense_type`
		WHERE `Name` like '%{$term}%'
		GROUP BY `Name`
		ORDER BY `Name` limit 10 
		";			
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            if($q->getNumRows() > 0) { 
            	$rrec = $q->getResultArray();
            	foreach($rrec as $row):
            		array_push($autoCompleteResult,array(
            			"value" => $row['Name'],
            		));
            	endforeach;
            }
            $q->freeResult();

            echo json_encode($autoCompleteResult);
		
		}  //end mat_art_section  

		public function company_search(){

			$term    = $this->request->getVar('term');
	
			$autoCompleteResult = array();
	
			$str = "
			SELECT
			aa.`recid`,
			aa.`BRNCH_OCODE2`,
			aa.`BRNCH_NAME`,
			bb.`COMP_NAME`
	
			FROM 
			`mst_companyBranch` aa
			JOIN
			mst_company bb
			ON
			aa.`COMP_ID` = bb.`recid`
			
	
			WHERE aa.`BRNCH_NAME` like '%{$term}%'
			";
	
			$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() > 0) {
				$rrec = $q->getResultArray();
				foreach($rrec as $row):
					$mtkn_rid = hash('sha384', $row['recid'] . $mpw_tkn);
					array_push($autoCompleteResult,array("value" => $row['BRNCH_NAME'],
						"mtkn_rid" => $mtkn_rid,
						"BRNCH_NAME"=>$row['BRNCH_NAME']
					));
				endforeach;
			}
	
			$q->freeResult();
			echo json_encode($autoCompleteResult);
			
		}// end companybranch		
} //end main class
?>
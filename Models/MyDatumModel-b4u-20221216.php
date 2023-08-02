<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;

class MyDatumModel extends Model
{
	public function __construct()
	{
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->session = session();
	}
	
	
	public function lk_Active_Store_or_Mem($dbname) { 
		$adata = array();
		$str = "select concat(rcv_code,'xOx',trim(rcv_desc)) __mdata from {$dbname}.mst_manrecs_rcvng_tag order by recid";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end lk_Active_Store_or_Mem
	
	public function lk_Active_DF($dbname) { 
		$cuserrema=$this->mysys_userrema();
		$adata=array();
		if($cuserrema ==='B'){
			$adata[]="D" . "xOx" . "Draft";
		}
		else{
			$adata[]="D" . "xOx" . "Draft";
			$adata[]="F" . "xOx" . "Final";	
		}
		return $adata;		
	}	//lk_Active_DF
	
	public function mysys_usergrp() { 
		return $this->session->__xsys_myidusergroup__;
	}
	public function mysys_userlvl() { 
		return $this->session->__xsys_myiduserlevel__;
	}
	
	public function mysys_userdept() { 
		return $this->session->__xsys_myiduserdept__;
	}
	public function mysys_userrema() { 
		return $this->session->__xsys_myiduserrema__;
	}	
	
	public function get_prod_line() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$adata = array();
		$str = "
		SELECT
			aa.	recid,concat(trim(aa.`PRODL_CODE`),'xOx',trim(aa.`PRODL_DESC`)) __mdata,  
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
		FROM {$this->db_erp}.`mst_product_line` aa
		WHERE `PRODL_RFLAG` = 'Y' ORDER BY aa.`PRODL_DESC`
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$adata[] = $row['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end get_prod_line
	
	public function get_mst_gender() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$adata = array();
		$str = "
		SELECT
			aa.	recid,concat(trim(aa.`GNDR_CODE`),'xOx',trim(aa.`GNDR_NAME`)) __mdata,  
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
		FROM {$this->db_erp}.`mst_gender` aa
		WHERE `MREC_FLAG` = 'A' ORDER BY aa.`GNDR_NAME`
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$adata[] = $row['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end get_prod_line	
	
} //end main class

<?php
namespace App\Models;
use CodeIgniter\Model;
use DateTime;

class MyInventoryModel extends Model
{
	// .. other member variables
	protected $db;
	public function __construct()
	{
		parent::__construct();
		$this->session = session();
		$this->request = \Config\Services::request();
		$this->mydbname = model('App\Models\MyDBNamesModel');
		$this->db_erp = $this->mydbname->medb(0);
		$this->db_br = $this->mydbname->medb(1);
		$this->db_temp = $this->mydbname->medb(2);
		$this->mylibzdb = model('App\Models\MyLibzDBModel');
		$this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->myusermod = model('App\Models\MyUserModel');
		$this->mydataz = model('App\Models\MyDatumModel');
	}
	
	public function lk_Active_Cycle_Tag($dbname) { 
		$adata=array();
		$adata[]="R" . "xOx" . "Regular";
		$adata[]="A" . "xOx" . "Adjustment (+/-)";
		return $adata;			
	} //end lk_Active_Cycle_Tag 
	
	public function lk_Active_Months($dbname) { 
		$adata=array();
		$adata[]="1" . "xOx" . "Jan";
		$adata[]="2" . "xOx" . "Feb";
		$adata[]="3" . "xOx" . "Mar";	
		$adata[]="4" . "xOx" . "Apr";	
		$adata[]="5" . "xOx" . "May";	
		$adata[]="6" . "xOx" . "Jun";	
		$adata[]="7" . "xOx" . "Jul";	
		$adata[]="8" . "xOx" . "Aug";	
		$adata[]="9" . "xOx" . "Sept";	
		$adata[]="10" . "xOx" . "Oct";	
		$adata[]="11" . "xOx" . "Nov";	
		$adata[]="12" . "xOx" . "Dec"; 
		return $adata;		
	} //end lk_Active_Months
	
	public function lk_Active_Year($dbname) { 
		$adata=array();
		$str = "
		CREATE TABLE if not exists {$dbname}.`mst_year` (
		  `recid` int(4) NOT NULL AUTO_INCREMENT,
		  `CTR_YEAR` varchar(4) DEFAULT '0000',
		  PRIMARY KEY (`recid`),
		  UNIQUE KEY `CTR_YEAR` (`CTR_YEAR`)
		) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8

		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "select year(now()) XSYSYEAR";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$ryear = $q->getRowArray();
		$xsysyear = $ryear['XSYSYEAR'];
		$q->freeResult();	
		
		$str = "select `CTR_YEAR` from {$dbname}.`mst_year` WHERE CTR_YEAR = '$xsysyear' limit 1";
		$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qctr->getNumRows() == 0) {
			$str = "insert into {$dbname}.`mst_year`
			(`CTR_YEAR`)
			values
			('$xsysyear')
			";
			$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
		}
		$qctr->freeResult();
		
		$str = "select concat(CTR_YEAR,'xOx',trim(CTR_YEAR)) __mdata from {$dbname}.mst_year order by CTR_YEAR DESC";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end  lk_Active_Year
	
	public function cycle_count_proc_uploaded_files() { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$fld_upld_cyctag = $this->request->getVar('fld_upld_cyctag');
		$fld_months = $this->request->getVar('fld_months');
		$fld_years = $this->request->getVar('fld_years');
		$fld_cycbranch = $this->request->getVar('fld_cycbranch');
		$mtknbrid = $this->request->getVar('mtknbrid');
		$fld_cycdate = $this->request->getVar('fld_cycdate') ; //insert date
		$fld_cyctag = $this->request->getVar('fld_upld_cyctag');
		//SOURCE
		if($fld_months =='') { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Required</strong> Month is required.!!!</div>";
			die();
		}//SOURCE
		if($fld_years =='') { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Required</strong> Year is required.!!!</div>";
			die();
		}
		//BRANCH
		if(!empty($fld_cycbranch) && !empty($mtknbrid)) { 
			$str = "select recid,BRNCH_CODE,BRNCH_NAME,concat('E',trim(BRNCH_OCODE2)) B_OCODE2  
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_cycbranch' and sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtknbrid' ";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'BRNCH','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Inalid Company Data!!!</div>";
				die();
			}

			$rw = $q->getRowArray();
			$fld_cycbranch_id = $rw['recid'];
			$BRNCH_CODE = $rw['BRNCH_CODE'];
			$B_OCODE2 = $rw['B_OCODE2'];
			$q->freeResult();
			//if the branch does not completely set it up for mandatory internal CODING 
			if (trim($B_OCODE2) == 'E'):
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Internal Branch CODING is not defined yet!!!</div>";
				die();
			endif;
			//END BRANCH
		}
		else{
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Branch is Required!!!</div>";
			die();
		} //end if 
		
		
		//TAG
		if($fld_cyctag =='') { 
			$fld_cyctag ='E';
		}
		
		$str = "SELECT
		aa.`CL_YEAR`,
		aa.`CL_MONTHS`, 
		aa.`CL_BRANCH`
		FROM {$this->db_erp}.`trx_manrecs_cyc_upld_logs` aa 
		WHERE `CL_ISPOSTED` = 'Y'
		AND DATE(aa.`CL_DATE`) = date('$fld_cycdate') 
		AND aa.`CL_BRANCH`= '$fld_cycbranch_id'
		";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$dateObj   = DateTime::createFromFormat('!m', $fld_months);
			$monthName_beg = $dateObj->format('F');
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed </strong>" . $this->mylibzsys->mydate_mmddyyyy($fld_cycdate) . " has already uploaded for <strong>{$fld_cycbranch}</strong> branch!!!</div>";
			die();
		}		
		
		
		 $str = "CREATE TABLE IF NOT EXISTS {$this->db_erp}.`trx_manrecs_cyc_hd` (
		  `recid` int(25) NOT NULL AUTO_INCREMENT,
		  `C_CTRLNO` varchar(30) NOT NULL,
		  `C_YEAR` varchar(4) DEFAULT '',
		  `C_MONTHS` varchar(2) DEFAULT '',
		  `C_COMPANY` varchar(50) DEFAULT '',
		  `C_BRANCH` varchar(50) DEFAULT '',
		  `C_ITEMC` varchar(35) DEFAULT '',
		  `C_QTY` varchar(15) DEFAULT '',
		  `C_SOURCE` varchar(1) DEFAULT '',
		  `C_TAG` varchar(1) DEFAULT '',
		  `C_FLAG` varchar(1) DEFAULT '',
		  `C_MUSER` varchar(20) DEFAULT NULL,
		  `C_ENCD` datetime DEFAULT NULL,
		  PRIMARY KEY (`recid`),
		  UNIQUE KEY `idx01` (`C_ITEMC`,`C_BRANCH`,`C_COMPANY`,`C_YEAR`,`C_MONTHS`),
		  KEY `idx02` (`C_FLAG`),
		  KEY `idx03` (`C_CTRLNO`),
		  KEY `idx04` (`C_YEAR`,`C_MONTHS`),
		  KEY `idx05` (`C_MUSER`,`C_ENCD`),
		  KEY `idx06` (`C_SOURCE`),
		  KEY `idx07` (`C_TAG`)
		) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);


		$str = "CREATE TABLE IF NOT EXISTS {$this->db_erp}.`trx_acct_cyc_upld` (
		  `recid` int(25) NOT NULL AUTO_INCREMENT,
		  `MU_CTRLNO` varchar(30) NOT NULL,
		  `MU_YEAR` varchar(4) DEFAULT '',
		  `MU_MONTHS` varchar(2) DEFAULT '',
		  `MU_COMPANY` varchar(50) DEFAULT '',
		  `MU_BRANCH` varchar(50) DEFAULT '',
		  `MU_ITEMC` varchar(35) DEFAULT '',
		  `MU_QTY` varchar(15) DEFAULT '',
		  `MU_SOURCE` varchar(1) DEFAULT '',
		  `MU_TAG` varchar(1) DEFAULT '',
		  `MU_FLAG` varchar(1) DEFAULT '',
		  `MU_MUSER` varchar(20) DEFAULT NULL,
		  `MU_ENCD` datetime DEFAULT NULL,
		  PRIMARY KEY (`recid`),
		  KEY `idx01` (`MU_ITEMC`,`MU_BRANCH`,`MU_COMPANY`),
		  KEY `idx02` (`MU_FLAG`),
		  KEY `idx03` (`MU_CTRLNO`),
		  KEY `idx04` (`MU_YEAR`,`MU_MONTHS`),
		  KEY `idx05` (`MU_MUSER`,`MU_ENCD`),
		  KEY `idx06` (`MU_SOURCE`),
		  KEY `idx07` (`MU_TAG`)
		) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$mefiles_path = ROOTPATH . 'public/uploads/endingcyc_csv/';
		$mefiles_upath = 'uploads/endingcyc_csv/';
		if(!is_dir($mefiles_path)) mkdir($mefiles_path, '0755', true);
		
		//file uploading 
		$lfileupld = 0;
		if ($mefiles = $this->request->getFiles()) { 
			foreach ($mefiles['mefiles'] as $mfile) { 
				if ($mfile->isValid() && ! $mfile->hasMoved()) { 
					$newName = $mfile->getRandomName();
					$__upld_filename = $cuser . '_' . $BRNCH_CODE . '_' . $mfile->getName();
					
					$mfilext = $this->mylibzdb->me_escapeString($__upld_filename);
					
					if (file_exists($mefiles_path . $__upld_filename)) { 
						unlink($mefiles_path . $__upld_filename);
					}
					
					$mfile->move($mefiles_path, $__upld_filename);
					
					if (file_exists($mefiles_path . $__upld_filename)) { 
						$lfileupld = 1;
					}
				}
			}  //end foreach 
		} //end if 
		//file uploading end 
		
		if ($lfileupld == 0): 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error </strong> File uploading...</div>";
			die();
		endif;
		
		$tbltemp = $this->db_temp . ".`artm_upld_temp_" . $this->mylibzsys->random_string(15) . "`";
		//$tbltemp = $this->db_br . ".`trx_{$B_OCODE2}_cyc_artm_upld_temp`";
		//$str = "drop table if exists {$tbltemp}";
		//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$cfile = $mefiles_path . $__upld_filename;
		
		//create table if not exists {$this->db_erp}.trx_acct_cyc_upld_logs ( 
		$str = "
		create table if not exists {$this->db_br}.`trx_{$B_OCODE2}_acct_cyc_upld_logs` ( 
		`recid` int(25) NOT NULL AUTO_INCREMENT,
		M_CTRLNO varchar(30) NOT NULL,
		M_YEAR varchar(4) default '',
		M_MONTHS varchar(2) default '',
		M_COMPANY varchar(50) default '',
		M_BRANCH varchar(50) default '',
		M_ITEMC varchar(35) default '',
		M_QTY varchar(15) default '',
		M_SOURCE varchar(1) DEFAULT '',
		M_CL_DATE date,
		M_TAG varchar(1) DEFAULT '',
		M_FLAG varchar(1) default '',
		M_MUSER varchar(20) default NULL,
		M_ENCD datetime default NULL,
		PRIMARY KEY (`recid`),
		key `idx01` (`M_ITEMC`,`M_BRANCH`,`M_COMPANY`),
		key `idx02` (`M_FLAG`),
		key `idx03` (`M_CTRLNO`),
		key `idx04` (`M_YEAR`,`M_MONTHS`),
		key `idx05` (`M_MUSER`,`M_ENCD`),
		key `idx06` (`M_SOURCE`),
		key `idx07` (`M_TAG`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
		";
		
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "
		create table if not exists {$tbltemp} ( 
		M_YEAR varchar(4) default '',
		M_MONTHS varchar(2) default '',
		M_COMPANY varchar(50) default '',
		M_BRANCH varchar(50) default '',
		M_ITEMC varchar(35) default '',
		M_ITM_C varchar(35) default '',
		M_QTY double(15,4) default '0.0000',
		M_SOURCE varchar(1) default '',
		M_TAG varchar(1) DEFAULT '',
		M_FLAG varchar(1) default '',
		M_MUSER varchar(20) default NULL,
		M_ENCD datetime default NULL,
		key `idx01` (`M_ITEMC`) 
		)
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
		$str = "
		LOAD DATA LOCAL INFILE '$cfile' INTO TABLE {$tbltemp} 
		FIELDS TERMINATED BY '\t' 
		  LINES TERMINATED BY '\n' 
		  IGNORE 1 LINES 
		(
		M_ITEMC,M_QTY
		) ";			
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "select count(*) __nrecs from {$tbltemp}";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->getRow();
		$nrec = (($rw->__nrecs + 0) > 0 ? ($rw->__nrecs + 0) : 0);
		if($nrec == 0) { 
			$str = "
			LOAD DATA LOCAL INFILE '$cfile' INTO TABLE {$tbltemp} 
			FIELDS TERMINATED BY '\t' 
			  LINES TERMINATED BY '\r\n' 
			  IGNORE 1 LINES 
			(
			M_ITEMC,M_QTY
			) ";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			$str = "select count(*) __nrecs from {$tbltemp}";
			$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $qq->getRow();
			$nrec = (($rw->__nrecs + 0) > 0 ? ($rw->__nrecs + 0) : 0);
			if($nrec == 0) { 
				$str = "
				LOAD DATA LOCAL INFILE '$cfile' INTO TABLE {$tbltemp} 
				FIELDS TERMINATED BY '\t' 
				  LINES TERMINATED BY '\r' 
				  IGNORE 1 LINES 
				(
				M_ITEMC,M_QTY
				) ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$qq->freeResult();
		}
		$q->freeResult();
		
		$str = "select count(M_ITEMC) __nrecs from {$tbltemp}";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->getRowArray();
		$nrecs = $rw['__nrecs'];
		$q->freeResult();
		
		$str = "update {$tbltemp} set 
		M_ITEMC = REPLACE(REPLACE(REPLACE(`M_ITEMC`,'\t',''),'\n',''),'\r',''),
		M_MUSER = '$cuser',
		M_ENCD = now()
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		
		//IF NO POSTED INSERT LOGS
		$strqry = "
		INSERT INTO {$this->db_erp}.`trx_manrecs_cyc_upld_logs`
			(`CL_YEAR`,
			 `CL_MONTHS`,
			 `CL_COMPANY`,
			 `CL_BRANCH`,
			 `CL_ISPOSTED`,
			  `CL_DATE`,
			  `CL_CTAG`,
			 `CL_MUSER`)
		VALUES (
		'$fld_years',
		'$fld_months',
		'1',
		'$fld_cycbranch_id',
		'Y',
		'$fld_cycdate',
		'$fld_cyctag',
		'$cuser'
		)";
		$this->mylibzdb->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$cseqn = $this->mydataz->get_ctr($this->db_erp,'CTRL_NO35');
		
		$fld_cycsource = "CYC_UPLD";
		
		// insert into {$this->db_erp}.trx_acct_cyc_upld_logs (
		$str = "insert into {$this->db_br}.`trx_{$B_OCODE2}_acct_cyc_upld_logs` (
		`M_CTRLNO`,
		`M_YEAR`,
		`M_MONTHS`,
		`M_COMPANY`,
		`M_BRANCH`,
		`M_ITEMC`,
		`M_QTY`,
		`M_SOURCE`,
		`M_TAG`,
		`M_MUSER`,
		`M_ENCD`,
		`M_CL_DATE` 
		) select 
		'$cseqn',
		'$fld_years',
		'$fld_months',
		`M_COMPANY`,
		'$fld_cycbranch_id',
		`M_ITEMC`,
		`M_QTY`,
		'$fld_cycsource',
		'$fld_cyctag',
		'$cuser',
		now(),date('$fld_cycdate') from {$tbltemp} where (!(trim(M_ITEMC) = '') or (M_ITEMC is not null)) 
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "INSERT INTO 
		{$this->db_erp}.trx_cyc_posting_logs (
		 `ML_CTRLNO`,
		 `ML_RECS`,
		 `ML_TEMPTBL`,
		 `ML_YEAR`,
		 `ML_MONTH`,
		 `ML_BRANCH_ID`,
		 `ML_CYCDATE`,
		 `ML_CYCTAG`,
		 `ML_USER`
		)
		VALUES ( 
		'$cseqn',
		'$nrecs',
		'$tbltemp',
		'$fld_years',
		'$fld_months',
		'$fld_cycbranch_id',
		'$fld_cycdate',
		'$fld_upld_cyctag',
		'$cuser'
		)
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'ART_UPLD_TEMP_TBL_CYC',$cseqn,$cuser,$str . chr(13) . chr(10) . $__upld_filename,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>File uploaded successfuly done... [{$__upld_filename}] reference control number: {$cseqn}</strong></div>";
		
		$this->cyc_upldproc_dl($nrecs,$tbltemp,$cseqn,$fld_years,$fld_months,$fld_cycbranch_id,$fld_cycdate);
		
		$str = "drop table if exists {$tbltemp}";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	} //end cycle_count_proc_uploaded_files
	
	
	public function cyc_upldproc_dl($nrecs='',$tbltemp ='',$cseqn ='',$fld_years ='' ,$fld_months ='',$fld_cycbranch_id ='',$fld_cycdate='') { 
		$chtmljs ='';
		$chtmljs2 ='';
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$file_name = 'cyc_upldproc_complete_dl' . '_' . $this->mylibzsys->random_string(9);
		$mpathdn = ROOTPATH;
		$_csv_path = 'public/downloads/me/';
		$filepath = $mpathdn.$_csv_path.$file_name.'.csv';
		$cfilelnk = site_url() . '/downloads/me/' . $file_name.'.csv';

		$str="  
		SELECT oa.* INTO OUTFILE '{$filepath}'
		FIELDS TERMINATED BY '\t' 
		LINES TERMINATED BY '\r\n'  
		FROM (
			SELECT
			'Control No',
			'Year',
			'Month',
			'Company',
			'Branch',
			'Item Code',
			'Qty',
			'Encoded User',
			'Encoded Date'
			UNION ALL
			SELECT
			  '$cseqn',
			  	M_YEAR,
				M_MONTHS,
				M_COMPANY,
				M_BRANCH,
				M_ITEMC,
				M_QTY,
				M_MUSER,
				M_ENCD
			FROM {$tbltemp}
			WHERE !(M_ITEMC = '')
		) oa       
		";

		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'cyc_upldroc_c_dl',$cseqn,$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		//COUNT RECORD COMPLETE AT INCOMPLETE
		$str = "SELECT count(*) __nrecs 
		FROM {$tbltemp}
		WHERE !(M_ITEMC = '')
		";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$nrecs_c = 0;
		if($q->getNumRows() > 0) { 
			$rw = $q->getRowArray();
			$nrecs_c = $rw['__nrecs'];
		} 
		$q->freeResult();

		$chtmljsxxx = "<div class=\"alert alert-success mb-0\" role=\"alert\">
		$nrecs_c records valid...
		</div>
		";
		
		$chtmljs .= "
		<div class=\"row mt-1 mb-0\" id=\"__mtoexport\">
			<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel\"><i class=\"btn btn-success fa fa-download\"> Download for VALID Records [{$nrecs_c}]</i></a></span>
		</div>
		<script type=\"text/javascript\">
			//window.parent.document.getElementById('myscrloading').innerHTML = '';
			jQuery('#lnkexportmsexcel').click(function() { 
				window.location = '{$cfilelnk}';
				jQuery('#__mtoexport').css({display:'none'});
			});
		</script>";
		echo $chtmljs;
		$q->freeResult();
	} //END cyc_upldproc_dl
	
	
	//VIEW NG POSTING LOGS
	public function cyc_upldpost_hd_view_recs($npages = 1,$npagelimit = 30,$msearchrec='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$data = array();
		$data['npage_count'] = 1;
		$data['npage_curr'] = 1;
		$data['rlist'] = '';
		$str_optn = "";
		if(!empty($msearchrec)) { 
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " WHERE (a.`ML_CTRLNO` like '%$msearchrec%' or a.`ML_USER` like '%$msearchrec%')";
		}
		$strqry = "
		SELECT
		  b.`BRNCH_NAME`,
		  a.`recid`,
		  a.`ML_CTRLNO`,
		  a.`ML_RECS`,
		  a.`ML_PROC_RECS`,
		  a.`ML_TEMPTBL`,
		  a.`ML_YEAR`,
		  a.`ML_MONTH`,
		  a.`ML_BRANCH_ID`,
		  a.`ML_CYCDATE`,
		  a.`ML_CYCTAG`,
		  a.`ML_ISPOSTED`,
		  a.`ML_USER`,
		  a.`ML_ENCD`,
		  a.`ML_POST_USER`,
		  a.`ML_POST_ENCD`,
		  a.`ML_TAG`,
		  a.`ML_PROC_USER`,
		  a.`ML_PROC_ENCD`
		FROM {$this->db_erp}.`trx_cyc_posting_logs` a
		JOIN {$this->db_erp}.`mst_companyBranch` b
		ON (a.`ML_BRANCH_ID` = b.`recid`)
		{$str_optn}";
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = (($npagelimit * ($npages - 1)) > 0 ? ($npagelimit * ($npages - 1)) : 0);
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa ORDER BY recid DESC limit {$nstart},{$npagelimit}";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qry->getNumRows() > 0) { 
			$data['rlist'] = $qry->getResultArray();
		}
		$qry->freeResult();
		return $data;
	}  //end cyc_upldpost_hd_view_recs
	
	//POSTING
	public function cyc_simpleupld_post($mtkn_seqn) {
		$fld_cycsource = '';
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();

		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00040104')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted - DEL_GENIVTY_PCOUNT_POSTREC.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		} 
				
		//$tbltemp ='',$cseqn ='',$fld_cycbranch_id ='',$fld_cycsource='',$fld_cycdate='',$fld_cyctag='',$fld_years = '',$fld_months = '',$fld_upld_cyctag = ''
		$fld_cyctag = "E"; //$this->input->get_post('fld_cyctag');//GET id
		$recid_plogs = 0;
		$str = "SELECT
		  b.`BRNCH_NAME`,
		  a.`recid`,
		  a.`ML_CTRLNO`,
		  a.`ML_RECS`,
		  a.`ML_PROC_RECS`,
		  a.`ML_TEMPTBL`,
		  a.`ML_YEAR`,
		  a.`ML_MONTH`,
		  a.`ML_BRANCH_ID`,
		  a.`ML_CYCDATE`,
		  a.`ML_CYCTAG`,
		  a.`ML_ISPOSTED`,
		  a.`ML_USER`,
		  a.`ML_ENCD`,
		  a.`ML_POST_USER`,
		  a.`ML_POST_ENCD`,
		  a.`ML_TAG`,
		  a.`ML_PROC_USER`,
		  a.`ML_PROC_ENCD`
		FROM {$this->db_erp}.`trx_cyc_posting_logs` a
		JOIN {$this->db_erp}.`mst_companyBranch` b
		ON (a.`ML_BRANCH_ID` = b.`recid`) 
		where (sha2(concat(a.`ML_CTRLNO`,'{$mpw_tkn}'),384)) = '{$mtkn_seqn}'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if ($q->getNumRows() > 0):
			$rw = $q->getRow();
			$fld_cycbranch_id = $rw->ML_BRANCH_ID;
			$fld_cycdate = $rw->ML_CYCDATE;
			$fld_upld_cyctag = $rw->ML_CYCTAG;
			$fld_months = $rw->ML_MONTH;
			$fld_years = $rw->ML_YEAR;
			$recid_plogs = $rw->recid;
			$cseqn = $rw->ML_CTRLNO;
			
			
			$str = "select recid,BRNCH_CODE,BRNCH_NAME,concat('E',trim(BRNCH_OCODE2)) B_OCODE2  
			from {$this->db_erp}.`mst_companyBranch` aa where aa.recid = '{$fld_cycbranch_id}' ";
			$qbr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($qbr->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Inalid Branch Data!!!</div>";
				die();
			}

			$rwbr = $qbr->getRowArray();
			$BRNCH_CODE = $rwbr['BRNCH_CODE'];
			$B_OCODE2 = $rwbr['B_OCODE2'];
			$qbr->freeResult();
			//if the branch does not completely set it up for mandatory internal CODING 
			if (trim($B_OCODE2) == 'E'):
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Internal Branch CODING is not defined yet!!!</div>";
				die();
			endif;
			//END BRANCH
			
			$tbltemp = "{$this->db_br}.`trx_{$B_OCODE2}_acct_cyc_upld_logs`";
			
		else:
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed </strong> transaction is invalid!!!</div>"; 
			die();
		endif;
		$q->freeResult();
		
		$str = "SELECT
		aa.`CL_YEAR`,
		aa.`CL_MONTHS`,
		aa.`CL_BRANCH`,
		b.`BRNCH_NAME`
		FROM {$this->db_erp}.`trx_manrecs_cyc_upld_logs` aa 
		JOIN {$this->db_erp}.`mst_companyBranch` b
		ON(aa.`CL_BRANCH` = b.`recid`)
		WHERE aa.`CL_ISPOSTED` = 'Y'
		AND aa.`CL_DATE`='$fld_cycdate'
		AND aa.`CL_BRANCH`='$fld_cycbranch_id'
		";
		//$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//if($q->getNumRows() > 0) { 
		//	$rw = $q->getRowArray();
		//	$fld_cycbranch =  $rw['BRNCH_NAME']; 
		//	echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed </strong>{$fld_cycdate} has already uploaded for <strong>{$fld_cycbranch}</strong> branch!!!.</div>";
		//	die();
		//}
		//$q->freeResult();

		//IF NO POSTED INSERT LOGS
		$strqry = "
		INSERT INTO {$this->db_erp}.`trx_manrecs_cyc_upld_logs`
			(`CL_YEAR`,
			 `CL_MONTHS`,
			 `CL_COMPANY`,
			 `CL_BRANCH`,
			 `CL_ISPOSTED`,
			 `CL_DATE`,
			 `CL_CTAG`,
			 `CL_MUSER`)
		VALUES (
		'$fld_years',
		'$fld_months',
		'1',
		'$fld_cycbranch_id',
		'Y',
		'$fld_cycdate',
		'$fld_upld_cyctag',
		'$cuser'
		)";
		//$this->mylibzdb->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			$str = "select `ML_ISPOSTED`,`ML_POST_USER`,`ML_POST_ENCD` from {$this->db_erp}.`trx_cyc_posting_logs` where `ML_ISPOSTED` = 'Y' and `ML_CTRLNO` = '$cseqn'";
			$qp = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($qp->getNumRows() > 0) { 
				$rwp = $qp->getRow();
				if (!empty($rwp->ML_POST_ENCD)): 
					echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed </strong>{$fld_cycdate} has already posted by {$rwp->ML_POST_USER} last {$rwp->ML_POST_ENCD}</div>";
					die();
				endif;
			}
			$qp->freeResult();

			//POSTING
			//insert/append newly records
			$str = "UPDATE {$this->db_erp}.`trx_manrecs_cyc_hd` aa,
			(SELECT 
				`M_ITEMC`,
				SUM(`M_QTY`) __mqty 
				FROM {$tbltemp} where `M_CTRLNO` = '$cseqn' 
				GROUP BY `M_ITEMC`
			) bb SET 
			aa.`C_QTY` = (aa.`C_QTY` + `__mqty`) 
			WHERE aa.`C_ITEMC` = bb.`M_ITEMC` 
			AND aa.`C_BRANCH` = '$fld_cycbranch_id' 
			AND aa.`C_MONTHS`= '$fld_months' 
			AND aa.`C_YEAR` = '$fld_years' 
			";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);			
			
			//new uploading approached to speed up processing due to mass and bulk of data 2019.12.18
			$str = "insert ignore into {$this->db_erp}.`trx_manrecs_cyc_hd` ( 
			`C_CTRLNO`,
			`C_YEAR`,
			`C_MONTHS`,
			`C_COMPANY`,
			`C_BRANCH`,
			`C_ITEMC`,
			`C_QTY`,
			`C_SOURCE`,
			`C_DATE`,
			`C_TAG`,
			`C_MUSER`,
			`C_ENCD`
			) select
			'$cseqn',
			'$fld_years',
			'$fld_months',
			`M_COMPANY`,
			'$fld_cycbranch_id',
			`M_ITEMC`,
			 SUM(`M_QTY`),
			'$fld_cycsource',
			'$fld_cycdate',
			'$fld_cyctag',
			'$cuser',
			now() from {$tbltemp} where `M_CTRLNO` = '$cseqn' and (!(trim(M_ITEMC) = '') or (M_ITEMC is not null)) 
			GROUP BY `M_ITEMC`
			";

			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);			
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'C_UPLD_LOGS','FILENAME:'.$tbltemp,$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			$str = "select count(M_ITEMC) __nrecs from {$tbltemp} where M_CTRLNO = '$cseqn' and (!(trim(M_ITEMC) = '') or (M_ITEMC is not null)) ";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$nrecs_u = 0;
			if($q->getNumRows() > 0) { 
				$rw = $q->getRowArray();
				$nrecs_u = $rw['__nrecs'];
			} 
			$q->freeResult();
			
			$str = "UPDATE {$this->db_erp}.`trx_cyc_posting_logs`
				SET
				  `ML_ISPOSTED` = 'Y',
				  `ML_POST_USER` = '$cuser',
				  `ML_POST_ENCD` = NOW()
				WHERE `ML_CTRLNO` = '$cseqn' AND `ML_ISPOSTED` = 'N'";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'CYC_SIMPLEUPLD_POSTED','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			//$str = "drop table if exists {$tbltemp}"; 
			//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong> Successfullt posted {$nrecs_u} record/s <br/>[Ref. Trx.: {$cseqn}] </div>";
	} //end cyc_simpleupld_post
	
	
	public function uploaded_view_recs($npages = 1,$npagelimit = 30,$msearchrec='',$myear='',$mmonths='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$fld_cycbranch = $this->mylibzdb->me_escapeString($this->request->getVar('fld_cycbranch'));
		$mtknbrid = $this->mylibzdb->me_escapeString($this->request->getVar('mtknbrid'));
		$_cycbranch_id = '';
		$__flag="C";
		$str_optn = "";
		$str_optn1 = "";
		$str_branch ='';
		if(!empty($msearchrec)) { 
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " AND (a.`M_CTRLNO` like '%$msearchrec%' or a.`M_COMPANY` like '%$msearchrec%' or a.`M_ITEMC` like '%$msearchrec%' or c.`ART_DESC` like '%$msearchrec%')";
		}
		if((!empty($myear)) && (!empty($mmonths))) { 
			$str_optn1 = " where (a.`M_YEAR` ='$myear' AND a.`M_MONTHS` ='$mmonths')";
		}
		//BRANCH
		if(!empty($fld_cycbranch) && !empty($mtknbrid)) { 
			$str = "select recid,BRNCH_NAME,concat('E',trim(BRNCH_OCODE2)) B_OCODE2  
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_cycbranch' and sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$mtknbrid'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'BRNCH','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> invalid Branch Data!!!</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$_cycbranch_id = $rw['recid'];
			$q->freeResult();
			$str_branch = " AND (a.`M_BRANCH` ='$_cycbranch_id')";
			$B_OCODE2 = $rw['B_OCODE2'];
			//END BRANCH
		} else {
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Branch is required!!!</div>";
			die();
		}
		$str = "SELECT
			  aa.`ML_YEAR`,
			  aa.`ML_MONTH`,
			  aa.`ML_BRANCH_ID`
			  FROM {$this->db_erp}.`trx_cyc_posting_logs` aa 
			  WHERE aa.`ML_ISPOSTED` = 'Y'
			  AND aa.`ML_YEAR`='$myear'
			  AND aa.`ML_MONTH`='$mmonths'
			  AND aa.`ML_BRANCH_ID`='$_cycbranch_id'
			  ";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
		if($q->getNumRows() > 0) { 
			$dateObj   = DateTime::createFromFormat('!m', $mmonths);
			$monthNameg = $dateObj->format('F');
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed </strong>{$monthNameg} has already posted for <strong>{$fld_cycbranch}</strong> branch!!!</div>";
			die();
		}
		 
		$strqry = "
		select 
		  a.`recid`,
		  a.`M_CTRLNO`,
		  a.`M_YEAR`,
		  a.`M_MONTHS`,
		  a.`M_COMPANY`,
		  a.`M_BRANCH`,
		  a.`M_ITEMC`,
		  a.`M_QTY`,
		  a.`M_SOURCE`,
		  a.`M_TAG`,
		  a.`M_FLAG`,
		  a.`M_MUSER`,
		  a.`M_ENCD`,
		b.`BRNCH_NAME`,
		IFNULL(c.`ART_DESC`,'NO_ITEM_FOUND') ART_DESC,
		IFNULL(c.`ART_UCOST`,0) ART_UCOST,
		IFNULL(c.`ART_UPRICE`,0) ART_UPRICE,
		IFNULL(c.`ART_UCOST` * a.`M_QTY`,0) TCOST,
		IFNULL(c.`ART_UPRICE` * a.`M_QTY`,0) TSRP
		from {$this->db_br}.`trx_{$B_OCODE2}_acct_cyc_upld_logs` a
		JOIN {$this->db_erp}.`mst_companyBranch` b
		ON(a.`M_BRANCH` = b.`recid`)
		LEFT JOIN {$this->db_erp}.`mst_article` c
		ON(a.`M_ITEMC` = c.`ART_CODE`)
		{$str_optn1}
		{$str_optn} 
		{$str_branch}
		GROUP BY a.`M_ITEMC`
		";
		
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = (($npagelimit * ($npages - 1)) > 0 ? ($npagelimit * ($npages - 1)) : 0);
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$strqry_count = "
		SELECT 
		SUM(a.`M_QTY`) M_QTY,
		IFNULL(SUM(c.`ART_UCOST`* a.`M_QTY`),0) M_TCOST,
		IFNULL(SUM(c.`ART_UPRICE`* a.`M_QTY`),0) M_TSRP
		from {$this->db_br}.`trx_{$B_OCODE2}_acct_cyc_upld_logs` a
		JOIN {$this->db_erp}.`mst_companyBranch` b
		ON(a.`M_BRANCH` = b.`recid`)
		LEFT JOIN {$this->db_erp}.`mst_article` c
		ON(a.`M_ITEMC` = c.`ART_CODE`) 
		{$str_optn1} 
		{$str_optn} 
		{$str_branch}
		";
		$qry_count = $this->mylibzdb->myoa_sql_exec($strqry_count,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry_count->getRowArray();
		$M_QTY = $rw['M_QTY'];
		$M_TCOST = $rw['M_TCOST'];
		$M_TSRP = $rw['M_TSRP'];
		
		$qry_count->freeResult();

		if($qry->getNumRows() > 0) { 
			$data['rlist'] = $qry->getResultArray();
			$data['myear'] = $myear;
			$data['mmonths'] = $mmonths;
			$data['fld_cycbranch'] = $fld_cycbranch;
			$data['mtknbrid'] = $mtknbrid;
			$data['M_QTY'] = $M_QTY;
			$data['M_TCOST'] = $M_TCOST;
			$data['M_TSRP'] = $M_TSRP; 
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['mmonths'] = $mmonths;
			$data['myear'] = $myear;
			$data['fld_cycbranch'] = $fld_cycbranch;
			$data['mtknbrid'] = $mtknbrid;
			$data['M_QTY'] = $M_QTY;
			$data['M_TCOST'] = $M_TCOST;
			$data['M_TSRP'] = $M_TSRP;
		} 
		$qry->freeResult();
		return $data;
	}  //end temp_view_recs
	
	public function proc_balance($ldispreportonly=FALSE) { 
		$adata = array();
		$msearchrec = '';
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$bid_mtknattr = $this->request->getVar('bid_mtknattr');
		$fld_branch = $this->mylibzdb->me_escapeString($this->request->getVar('fld_branch'));
		$mdateinq = $this->request->getVar('mdateinq');
		$mevalfilter = $this->request->getVar('mevalfilter');
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00040105')) { 
			//when lreportonly is TRUE skip this validation to allow previous date inventory data processing (mod date: 2023.07.03) 
			if ($ldispreportonly == FALSE):
				echo "
				<script>
					jQuery('#memsgme_bod').html('<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Access RESTRICTED!!!</strong></div>');
					jQuery('#memsgme').modal('show');
				</script>
				";
				die();
			endif;
		}  //end if
		
		$data = array();
		$str_optn = "";
		if (!empty($msearchrec)): 
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " where (`ITEMC` = '$msearchrec'  or `ITEM_BARCODE` = '$msearchrec' or  `ITEM_DESC` like '%{$msearchrec}%') ";
		endif;
		
		if(!empty($fld_branch) && !empty($bid_mtknattr)) { 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_branch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$bid_mtknattr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$B_CODE = 'E'.$rw['B_OCODE2'];
			$B_RECID = $rw['recid'];
			$tblivty = "{$this->db_br}.`trx_E{$br_ocode2}_myivty_lb_dtl`";
			$myivty_lb_dtl = $this->db_br . ".`trx_{$B_CODE}_myivty_lb_dtl`";
			$myivty_lb_dtl_pmo = $this->db_br . ".`trx_{$B_CODE}_myivty_lb_dtl_pmo`";
			$tbl_ivty_lb_dtl_pmo = "trx_{$B_CODE}_myivty_lb_dtl_pmo";
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$tblbegupld = $this->db_br . ".`trx_E{$br_ocode2}_acct_cyc_upld_logs`";  #uploaded begginning balance 
			$tblsaleso_nw = $this->db_erp . ".`trx_{$B_CODE}_salesout`";
			$q->freeResult();
			//END BRANCH
		} else { 
			echo "Branch is INVALID!!!";
			die();
			
		}// end if
		
		$meqry_L_DAY_PREV_MO = "date_add(DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))-1 DAY),interval -1 day)";
		$str = " SELECT current_date() `nowme`,DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))-1 DAY) AS 'F_DAY_CURR_MO',{$meqry_L_DAY_PREV_MO} L_DAY_PREV_MO,
		DATE_SUB(LAST_DAY({$meqry_L_DAY_PREV_MO}),INTERVAL DAY(LAST_DAY({$meqry_L_DAY_PREV_MO}))-1 DAY) F_DAY_CURR_MO_BEG,DATE_FORMAT(NOW(),'%Y%m') ME_YRMO";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->getRowArray();
		$fld_lbdate = $rw['nowme'];
		$metkntmp = '';
		$fld_lbdate_first = $rw['F_DAY_CURR_MO'];
		$fld_lbdate_beg = $rw['L_DAY_PREV_MO'];
		$fld_lbdate_beg_first = $rw['F_DAY_CURR_MO_BEG'];
		$ME_YRMO = $rw['ME_YRMO'];
		if ($mevalfilter == 'M_PREV'):
			$metkntmp = $this->mylibzsys->random_string(15);
			$metmptblme = $this->db_temp . ".`meivtytmp_{$metkntmp}`";
			$str = "create table if not exists {$metmptblme} like {$myivty_lb_dtl}";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$adate = explode("-",$mdateinq);
			$str = "SHOW TABLES FROM {$this->db_br} LIKE '{$tbl_ivty_lb_dtl_pmo}'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() > 0): 
				$M_YRMO = $adate[0] . $adate[1];
				//if date process is covered to current mo
				if($ME_YRMO  == $M_YRMO): 
					$str = "insert into {$metmptblme} select * from {$myivty_lb_dtl} where `MTYPE` = 'BEG-BAL'"; 
				else: 
					$mmo_pmo = ($adate[1] + 0);
					$myr_pmo = $adate[0];
					if ($mmo_pmo == 1):
						$mmo_pmo = 12;
						$myr_pmo = ($myr_pmo - 1);
					else:
						$mmo_pmo = ($adate[1] - 1);
					endif;
					
					$str = "
					insert into {$metmptblme} (
					`MBRANCH_ID`,
					`ITEMC`,
					`ITEM_BARCODE`,
					`ITEM_DESC`,
					`MQTY`,
					`MQTY_CORRECTED`,
					`MCOST`,
					`MSRP`,
					`SO_GROSS`,
					`SO_NET`,
					`MARTM_COST`,
					`MARTM_PRICE`,
					`MTYPE`,
					`MFORM_SIGN`,
					`MUSER`,
					`MLASTDELVD`,
					`MPROCDATE`				
					)
					select   `MBRANCH_ID`,
					`ITEMC`,
					`ITEM_BARCODE`,
					`ITEM_DESC`,
					`MQTY`,
					`MQTY_CORRECTED`,
					`MCOST`,
					`MSRP`,
					`SO_GROSS`,
					`SO_NET`,
					`MARTM_COST`,
					`MARTM_PRICE`,
					`MTYPE`,
					`MFORM_SIGN`,
					`MUSER`,
					`MLASTDELVD`,
					`MPROCDATE` from {$myivty_lb_dtl_pmo} where (`MTYPE` = 'BEG-BAL' or `MTYPE` = 'GEN-IVTYC') and `MYEAR` = {$myr_pmo} and `MMONTH` = {$mmo_pmo}";
				endif;
				if ($cuser == '181-1'): 
					//echo $str . '<br />';
				endif;
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			else: 
				$str = "insert into {$metmptblme} select * from {$myivty_lb_dtl} where `MTYPE` = 'BEG-BAL'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			endif;
			$q->freeResult();
			$myivty_lb_dtl = $metmptblme;
			$fld_lbdate_first = $adate[0] . '-' . $adate[1] . '-01';
			$fld_lbdate = $mdateinq;
			//if ($mdateinq >= $fld_lbdate_first):
			//	$fld_lbdate = $mdateinq;
			//endif;
			$adata['metkntmp'] = $metkntmp;
		endif;
		$q->freeResult();
		$meday = explode("-",$fld_lbdate);
		
		$str_branch_m = " AND (aa.`branch_id` = {$br_id}) ";
		$str_optn_cyc_recon_adj = " AND (date(cx.`mencd`) >= date('{$fld_lbdate_first}') AND date(cx.`mencd`) <= date('{$fld_lbdate}')) and ch.`ira_posted` = 'Y' ";
		$str_optn_rcv_encd_old = " and ((date(aa.`rcv_date`) >= date('{$fld_lbdate_first}') AND date(aa.`rcv_date`) <= date('{$fld_lbdate}')) or (date(aa.`encd_date`) >= date('{$fld_lbdate_first}') and date(aa.`encd_date`) <= date('{$fld_lbdate}'))) ";
		$str_optn_pullouts_encd_old = " AND ((date(aa.`po_date`) >= date('{$fld_lbdate_first}') AND date(aa.`po_date`) <= date('{$fld_lbdate}')) or (date(aa.`encd_date`) >= date('{$fld_lbdate_first}') and date(aa.`encd_date`) <= date('{$fld_lbdate}'))) ";

		$str_optn_rcv_encd = " AND (date(aa.`encd_date`) >= date('{$fld_lbdate_first}') and date(aa.`encd_date`) <= date('{$fld_lbdate}')) ";
		$str_optn_pullouts_encd = " AND (date(aa.`encd_date`) >= date('{$fld_lbdate_first}') and date(aa.`encd_date`) <= date('{$fld_lbdate}')) ";

		$str_optn_pos = " (DATE(aa.`SO_DATE`) >= date('{$fld_lbdate_first}') AND DATE(aa.`SO_DATE`) <= date('{$fld_lbdate}')) ";
		$str_optn_claims_encd = " and (DATE(aa.final_date) between date('{$fld_lbdate_first}') and date('{$fld_lbdate}')) AND aa.`is_verified` = 'Y' AND aa.`is_reviewed` = 'Y' ";
		
		$metbltmpivty = $this->db_temp . ".`meivty_temp_" . $this->mylibzsys->random_string(15) . "`";
		$str = "drop table if exists {$metbltmpivty}";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "CREATE TABLE IF NOT EXISTS {$metbltmpivty} (
		MITEMC varchar(35) NOT NULL,
		MBEG_QTY double(15,4),
		MCYCDATE date,
		PRIMARY KEY (`MITEMC`),
		KEY `idx01` (`MCYCDATE`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "select MAX(ML_CTRLNO) M_ML_CTRLNO,max(ML_CYCDATE) M_CL_DATE,MAX(ML_YEAR) M_ML_YEAR,max(ML_MONTH) M_ML_MONTH from {$this->db_erp}.trx_cyc_posting_logs WHERE ML_BRANCH_ID = {$br_id}
		and DATE(ML_CYCDATE) >= DATE('{$fld_lbdate_first}') AND DATE(ML_CYCDATE) <= DATE('{$fld_lbdate}') 
		and ML_ISPOSTED = 'Y' AND ML_YEAR = YEAR(DATE(ML_CYCDATE)) AND ML_MONTH = MONTH(DATE(ML_CYCDATE)) and ML_CYCTAG = 'R' ";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$lbeg = 0;
		if($q->getNumRows() > 0):
			$rw = $q->getRowArray();
			if (!empty($rw['M_ML_CTRLNO'])):
				$str = "insert into {$metbltmpivty} (
				MITEMC,MBEG_QTY,MCYCDATE
				)
				select `M_ITEMC`,SUM(`M_QTY`),DATE('{$rw['M_CL_DATE']}') FROM {$tblbegupld} where `M_CTRLNO` = {$rw['M_ML_CTRLNO']} and 
				`M_MONTHS` = {$rw['M_ML_MONTH']} and `M_YEAR` = {$rw['M_ML_YEAR']} group by `M_ITEMC`";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$str = "select `MITEMC` from {$metbltmpivty} limit 10";
				$qr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$lbeg = (($qr->getNumRows() > 0) ? 1 : 0);
				$qr->freeResult();
			endif;
		endif;
		$q->freeResult();
		
		
		if ($lbeg == 1):
			$str = "insert ignore into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MTYPE`,`MFORM_SIGN`,`MUSER`)
			select {$B_RECID},MITEMC,sum(MBEG_QTY),sum(MBEG_QTY),'GEN-IVTYC','+-','XAUTO-SYS' FROM {$metbltmpivty} aa group by MITEMC";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$str = "update {$myivty_lb_dtl} aa join {$metbltmpivty} bb on(aa.ITEMC = bb.MITEMC and aa.`MTYPE` = 'GEN-IVTYC') 
			set aa.`MQTY` = bb.MBEG_QTY";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		endif;
		
		$str = "delete from {$myivty_lb_dtl} where !(`MTYPE` = 'BEG-BAL' or `MTYPE` = 'GEN-IVTYC')";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		//FETCH RECON/ADJUSTED ITEM FROM CYCLE COUNT
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MTYPE`,`MFORM_SIGN`,`MUSER`
		) SELECT cx.ira_branch_id,cx.ira_artcode,SUM((0 - ira_ded_qty) + ira_add_qty),0,'CYC-ADJ','+-','XAUTO-SYS' FROM {$this->db_erp}.`trx_ivty_reconadj_dt` cx 
		join {$this->db_erp}.`trx_ivty_reconadj_hd` ch on(cx.`ira_hd_id` = ch.`recid`) 
		  where cx.ira_branch_id = {$B_RECID} {$str_optn_cyc_recon_adj} GROUP BY ira_branch_id,ira_artcode";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FECTH RECEIVING DELIVERIES
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER` 
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty`),SUM(bb.`qty_corrected`),
		    bb.`ucost`,bb.`uprice`,'RCV','+',max(aa.`encd_date`),'XAUTO-SYS' 
		  FROM
		    {$this->db_erp}.`trx_manrecs_hd` aa 
		    JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		    AND (aa.`hd_sm_tags` ='D') {$str_branch_m} {$str_optn_rcv_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty` 
			    	end )),SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty_corrected` 
			    	end )),
			    bb.`ucost`,bb.`uprice`,'RCV','+',max(aa.`encd_date`),'XAUTO-SYS' 
			  FROM
			    {$this->db_erp}.`trx_manrecs_hd` aa 
			    JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			    LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			    AND (aa.`hd_sm_tags` ='D') {$str_branch_m} {$str_optn_rcv_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		#FETCH CLAIMS (0 - (`MQTY` - `MQTY_CORRECTED`))
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty`),SUM(bb.`qty_corrected` ),
		    bb.`ucost`,bb.`uprice`,'CLAIMS','+/-',max(aa.`final_date`),'XAUTO-SYS' 
		  FROM
		    {$this->db_erp}.`trx_manrecs_hd` aa 
		    JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`claim_tag`='Y' and (bb.`qty` <> bb.`qty_corrected`) AND (bb.`qty_corrected` <> 0) {$str_branch_m} {$str_optn_claims_encd}
		  GROUP BY bb.`mat_code`";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		#END FETCH CLAIMS
				
		#FETCH STORE USE
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		bb.`mat_code` ART_CODE,
		SUM(bb.`qty`) , SUM(bb.`qty_corrected`),
		bb.`ucost`,bb.`uprice`,'RCV-S','+',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM
		{$this->db_erp}.`trx_manrecs_hd` aa 
		JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
		ON (aa.`recid` = bb.`mrhd_rid`) 
		WHERE aa.`flag` = 'R' 
		AND (aa.`hd_sm_tags` ='S') {$str_branch_m} {$str_optn_rcv_encd} 
		GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			bb.`mat_code` ART_CODE,
			SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty` 
			    	end )),SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty_corrected` 
			    	end )),
			bb.`ucost`,bb.`uprice`,'RCV-S','+',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM
			{$this->db_erp}.`trx_manrecs_hd` aa 
			JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
			ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			WHERE aa.`flag` = 'R' 
			AND (aa.`hd_sm_tags` ='S') {$str_branch_m} {$str_optn_rcv_encd} GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH RECEIVING MEMBERSHIP
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		bb.`mat_code` ART_CODE,
		SUM(bb.`qty`) , SUM(bb.`qty_corrected`),
		bb.`ucost`,bb.`uprice`,'RCV-M','+',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM
		{$this->db_erp}.`trx_manrecs_hd` aa 
		JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
		ON (aa.`recid` = bb.`mrhd_rid`) 
		WHERE aa.`flag` = 'R' 
		AND (aa.`hd_sm_tags` = 'M') {$str_branch_m} {$str_optn_rcv_encd} 
		GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			bb.`mat_code` ART_CODE,
			SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty` 
			    	end )),SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty_corrected` 
			    	end )),
			bb.`ucost`,bb.`uprice`,'RCV-M','+',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM
			{$this->db_erp}.`trx_manrecs_hd` aa 
			JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
			ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			WHERE aa.`flag` = 'R' 
			AND (aa.`hd_sm_tags` = 'M') {$str_branch_m} {$str_optn_rcv_encd} GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH RECEIVING CHANGE PRICE
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		bb.`mat_code` ART_CODE,
		SUM(bb.`qty`) , SUM(bb.`qty_corrected`),
		bb.`ucost`,bb.`uprice`,'RCV-C','+',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM
		{$this->db_erp}.`trx_manrecs_hd` aa 
		JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
		ON (aa.`recid` = bb.`mrhd_rid`) 
		WHERE aa.`flag` = 'R' 
		AND (aa.`hd_sm_tags` = 'C') {$str_branch_m} {$str_optn_rcv_encd} 
		GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			bb.`mat_code` ART_CODE,
			SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty` 
			    	end )),SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty_corrected` 
			    	end )),
			bb.`ucost`,bb.`uprice`,'RCV-C','+',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM
			{$this->db_erp}.`trx_manrecs_hd` aa 
			JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
			ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			WHERE aa.`flag` = 'R' 
			AND (aa.`hd_sm_tags` = 'C') {$str_branch_m} {$str_optn_rcv_encd} GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH RECEIVING FROM PULL OUT 
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		bb.`mat_code` ART_CODE,
		SUM(bb.`qty`) , SUM(bb.`qty_corrected`),
		bb.`ucost`,bb.`uprice`,'RCV-R','+',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM
		{$this->db_erp}.`trx_manrecs_hd` aa 
		JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
		ON (aa.`recid` = bb.`mrhd_rid`) 
		WHERE aa.`flag` = 'R' 
		AND (aa.`hd_sm_tags` = 'R') {$str_branch_m} {$str_optn_rcv_encd} 
		GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			bb.`mat_code` ART_CODE,
			SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty` 
			    	end )),SUM((case when (date(aa.rcv_date) <= date(ivt.MCYCDATE) and !(date(aa.rcv_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
			    	else bb.`qty_corrected` 
			    	end )),
			bb.`ucost`,bb.`uprice`,'RCV-R','+',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM
			{$this->db_erp}.`trx_manrecs_hd` aa 
			JOIN {$this->db_erp}.`trx_manrecs_dt` bb 
			ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			WHERE aa.`flag` = 'R' 
			AND (aa.`hd_sm_tags` = 'R') {$str_branch_m} {$str_optn_rcv_encd} GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH PULLOUTS BUY 1 TAKE 1 
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-B1T1','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='2') {$str_branch_m} {$str_optn_pullouts_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),			    
			    bb.`ucost`,bb.`uprice`,'PO-B1T1','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='2') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH PULLOUTS DISPOSE
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER` 
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-DSP','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='1') {$str_branch_m} {$str_optn_pullouts_encd}  
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-DSP','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='1') {$str_branch_m} {$str_optn_pullouts_encd}  
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH PULLOUTS BARGAIN/SALE ITEM
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-BRG','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='8') {$str_branch_m} {$str_optn_pullouts_encd}  
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-BRG','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='8') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

		#FETCH PULLOUTS GIVE AWAYS
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-GVA','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='3') {$str_branch_m} {$str_optn_pullouts_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-GVA','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='3') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH PULLOUTS INVENTORY TRANSFER OUT
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-TO','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='9') {$str_branch_m} {$str_optn_pullouts_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-TO','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='9') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH PULLOUTS TO OTHER BRANCH 
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-TOB','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='4') {$str_branch_m} {$str_optn_pullouts_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-TOB','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='4') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#FETCH PULLOUTS RETURN TO MAPULANG LUPA 
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-RTML','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='5') {$str_branch_m} {$str_optn_pullouts_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-RTML','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='5') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;   
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		
		#FETCH PULLOUTS STORE USE
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-SU','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='6') {$str_branch_m} {$str_optn_pullouts_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-SU','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='6') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

		#FETCH PULLOUTS OTHERS
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
		) SELECT aa.`branch_id` BRNCH,
		    bb.`mat_code` ART_CODE,
		    SUM(bb.`qty` * - 1) TQTY,
		    SUM(if(bb.`qty_corrected` > 0,bb.`qty_corrected` * - 1,0)),
		    bb.`ucost`,bb.`uprice`,'PO-OTHERS','-',max(aa.`encd_date`),'XAUTO-SYS' 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
		join {$this->db_erp}.`trx_manrecs_po_dt` bb 
		      ON (aa.`recid` = bb.`mrhd_rid`) 
		  WHERE aa.`flag` = 'R' 
		  AND (aa.`po_rsons_id`='7') {$str_branch_m} {$str_optn_pullouts_encd} 
		  GROUP BY bb.`mat_code`";
		if ($lbeg == 1):
			$str = "insert into {$myivty_lb_dtl} (
			`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`		
			) SELECT aa.`branch_id` BRNCH,
			    bb.`mat_code` ART_CODE,
			    SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (bb.`qty` * -1) 
				    	end )),
				SUM((case when (date(aa.po_date) <= date(ivt.MCYCDATE) and !(date(aa.po_date) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else if(bb.`qty_corrected` > 0,bb.`qty_corrected` * -1,0)
				    	end )),
			    bb.`ucost`,bb.`uprice`,'PO-OTHERS','-',max(aa.`encd_date`),'XAUTO-SYS' 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` aa 
			join {$this->db_erp}.`trx_manrecs_po_dt` bb 
			      ON (aa.`recid` = bb.`mrhd_rid`) 
			LEFT join {$metbltmpivty} ivt on(bb.`mat_code` = ivt.MITEMC) 
			  WHERE aa.`flag` = 'R' 
			  AND (aa.`po_rsons_id`='7') {$str_branch_m} {$str_optn_pullouts_encd} 
			  GROUP BY bb.`mat_code`";
		endif;
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		#SALES OUT
		$str = "insert into {$myivty_lb_dtl} (
		`MBRANCH_ID`,`ITEMC`,`MQTY`,`MQTY_CORRECTED`,`MCOST`,`MSRP`,`MTYPE`,`MFORM_SIGN`,`MLASTDELVD`,`MUSER`,`SO_GROSS`,`SO_NET` 
		) SELECT {$B_RECID} BRNCH,
		    SO_ITEMCODE,
		    SUM((case when (date(aa.SO_DATE) <= date(ivt.MCYCDATE) and !(date(aa.SO_DATE) = date('0000-00-00')) and ivt.MCYCDATE is not null) then 0 
				    	else (aa.SO_QTY * -1) 
				    	end )) TQTY,
		    0,
		    SO_COST,SO_SRP,'SALES','-',max(aa.SO_DATE),'XAUTO-SYS',SUM(`SO_GROSS`),SUM(`SO_NET`) 
		FROM {$tblsaleso_nw} aa LEFT join {$metbltmpivty} ivt on(aa.`SO_ITEMCODE` = ivt.MITEMC) WHERE {$str_optn_pos} GROUP BY aa.SO_ITEMCODE";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		$metbltmpivty2 = $this->db_temp . ".`meivty_temp_" . $this->mylibzsys->random_string(15) . "`";
		$str = "drop table if exists {$metbltmpivty2}";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		$str = "create table if not exists {$metbltmpivty2} like {$metbltmpivty}";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  		

		$strxx = "insert into {$metbltmpivty2} (MITEMC,MBEG_QTY)  
		select `ITEMC`,(sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) ME_END_BAL_QTY 
		from {$myivty_lb_dtl} group by `ITEMC`";
		
		$str = "insert into {$metbltmpivty2} (MITEMC,MBEG_QTY)  
		select `ITEMC`,((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) ME_END_BAL_QTY 
		from {$myivty_lb_dtl} group by `ITEMC`";
		
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		$metblivtydlogs = $this->db_br . ".`trx_{$B_CODE}_ivty_bal_daily_logs`";
		//$str = "drop table if exists {$metblivtydlogs}";
		//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		$str = "create table if not exists {$metblivtydlogs} like {$this->db_br}.`trx_ivty_bal_daily_logs`";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		
		$str = "insert ignore into {$metblivtydlogs} ( 
		`MYEAR`,`MMONTH`,`ITEM_CODE`,`ITEM_QTY_DAY{$meday[2]}` 
		) select '{$meday[0]}','{$meday[1]}',MITEMC,MBEG_QTY from {$metbltmpivty2} aa ";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

		if ($lperbr == 1):
			$metbltmpartm = $this->db_temp . ".`meivty_temp_" . $this->mylibzsys->random_string(15) . "`";
			$str = "drop table if exists {$metbltmpartm}";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
			$str = "CREATE TABLE IF NOT EXISTS {$metbltmpartm} (
			`recid` int(25) NOT NULL AUTO_INCREMENT,
			`ITEMC` varchar(35) NOT NULL,
			`ITEM_BARCODE` varchar(18) DEFAULT '',
			`ITEM_DESC` varchar(150) DEFAULT '',
			`ITEM_COST` double(15,4) DEFAULT 0.0000,
			`ITEM_PRICE` double(15,4) DEFAULT 0.0000,
			PRIMARY KEY (`recid`),
			KEY `idx01` (`ITEMC`) 
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
			$str = "
			insert into {$metbltmpartm} (
			`ITEMC`,`ITEM_BARCODE`,`ITEM_DESC`,`ITEM_COST`,`ITEM_PRICE`
			) 
			select itm.ART_CODE,itm.ART_BARCODE1,itm.ART_DESC,kk.art_cost,kk.art_uprice 
			from {$this->db_erp}.mst_article itm join {$this->db_erp}.`mst_article_per_branch` kk ON (itm.`recid` = kk.`artID`) 
			where itm.`ART_HIERC1` = '0600' and kk.`brnchID` = {$B_RECID}";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
			
			$str = "update {$metblivtydlogs} aa join {$metbltmpivty2} bb on(aa.`ITEM_CODE` = bb.`MITEMC`) join {$metbltmpartm} cc on(aa.ITEM_CODE = cc.ITEMC) 
			set aa.`ITEM_QTY_DAY{$meday[2]}` = bb.MBEG_QTY,
			aa.`ITEM_COST_DAY{$meday[2]}` = IF(aa.`ITEM_COST_DAY{$meday[2]}` = 0,IFNULL(cc.ITEM_COST,0),aa.`ITEM_COST_DAY{$meday[2]}`),
			aa.`ITEM_PRICE_DAY{$meday[2]}` = if(aa.`ITEM_PRICE_DAY{$meday[2]}` = 0,IFNULL(cc.ITEM_PRICE,0),aa.`ITEM_PRICE_DAY{$meday[2]}`)  
			WHERE aa.`MYEAR` = '{$meday[0]}' and aa.`MMONTH` = '{$meday[1]}' ";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  			
			$str = "update {$myivty_lb_dtl} aa join {$metbltmpartm} bb on (aa.ITEMC = bb.ITEMC) 
			set aa.MARTM_COST = IFNULL(bb.ITEM_COST,aa.MARTM_COST),
			aa.MARTM_PRICE = IFNULL(bb.ITEM_PRICE,aa.MARTM_PRICE),
			aa.`ITEM_BARCODE` = bb.ITEM_BARCODE,
			aa.`ITEM_DESC` = bb.ITEM_DESC";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
			$str = "drop table if exists {$metbltmpartm}";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  

		else: 
			$str = "update {$metblivtydlogs} aa join {$metbltmpivty2} bb on(aa.`ITEM_CODE` = bb.`MITEMC`) left join {$this->db_erp}.mst_article cc on(aa.ITEM_CODE = cc.ART_CODE) 
			set aa.`ITEM_QTY_DAY{$meday[2]}` = bb.MBEG_QTY,
			aa.`ITEM_COST_DAY{$meday[2]}` = IF(aa.`ITEM_COST_DAY{$meday[2]}` = 0,IFNULL(cc.ART_UCOST,0),aa.`ITEM_COST_DAY{$meday[2]}`),
			aa.`ITEM_PRICE_DAY{$meday[2]}` = if(aa.`ITEM_PRICE_DAY{$meday[2]}` = 0,IFNULL(cc.ART_UPRICE,0),aa.`ITEM_PRICE_DAY{$meday[2]}`)  
			WHERE aa.`MYEAR` = '{$meday[0]}' and aa.`MMONTH` = '{$meday[1]}' ";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
			$str = "update {$myivty_lb_dtl} aa join {$this->db_erp}.mst_article bb on (aa.ITEMC = bb.ART_CODE) 
			set aa.MARTM_COST = IFNULL(bb.ART_UCOST,aa.MARTM_COST),
			aa.MARTM_PRICE = IFNULL(bb.ART_UPRICE,aa.MARTM_PRICE),
			aa.`ITEM_BARCODE` = bb.ART_BARCODE1,
			aa.`ITEM_DESC` = bb.ART_DESC";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);  
		endif;
		$str = "
		select count(*) nrecs from ( 
			select `ITEMC`,((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) ME_END_BAL_QTY 
			from {$myivty_lb_dtl} group by `ITEMC`
		) oa WHERE ME_END_BAL_QTY < 0
		";
		$qnb = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rwnb = $qnb->getRowArray();
		$G_NOBAL_ITEMS = (empty($rwnb['nrecs']) ? 0 : $rwnb['nrecs']);
		$qnb->freeResult();
		$chtml = '';
		if ($G_NOBAL_ITEMS > 0):
			$chtml = "
			<div class=\"row mt-2 m-0 p-1\">
				<div class=\"col-sm-6\">
					<div class=\"alert alert-danger\" role=\"alert\">
					<h4 class=\"alert-heading\">Inventory Alert</h4>
					<p>Detected NEGATIVE Inventory in <span class=\"fw-bold\">" . number_format($G_NOBAL_ITEMS,0,'',',') . "</span> items!!!</p>
						<hr>
						<p class=\"mb-0\">Please Download file and check <span class=\"fw-bold\">Ending Balance QTY</span></p>
					</div> 
				</div>
			</div>
			";
		endif;
		if (count($adata) == 0):
			echo "
			<script>
				//var memechtml = '<div class=\"row mt-2 m-0 p-1\">';
				//memechtml += '<div class=\"col-sm-6\">';
				var memechtml = '<div class=\"alert alert-danger mt-2 mb-0\" role=\"alert\">';
				memechtml += '<h4 class=\"alert-heading\">Inventory Alert</h4>';
				memechtml += '<p>Detected NEGATIVE Inventory in <span class=\"fw-bold\">" . number_format($G_NOBAL_ITEMS,0,'',',') . "</span> items!!!</p><hr>';
				memechtml += '<p class=\"mb-0\">Please Download file and check <span class=\"fw-bold\">Ending Balance QTY</span></p>';
				memechtml += '</div>';
				jQuery('#memsgme_bod').html('<div class=\"alert alert-info mb-0\" role=\"alert\"><span class=\"fw-bol\">Processing done [BALANCE]!!!</span></div>' + memechtml);
				jQuery('#memsgme').modal('show');
			</script>
			";
		endif;
		return $adata;
	} //end proc_balance
	
}  //end main class

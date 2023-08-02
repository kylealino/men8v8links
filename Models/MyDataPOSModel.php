<?php
namespace App\Models;
use CodeIgniter\Model;
use DateTime;
use ZipArchive;

class MyDataPOSModel extends Model
{
	// .. other member variables
	protected $db;
	public function __construct()
	{ 
		parent::__construct();
		$this->session = session();
		$this->request = \Config\Services::request();
		$this->mylibzsys = model('App\Models\MyLibzSysModel');
        $this->myusermod = model('App\Models\MyUserModel');
		$this->mydb_erp = $this->myusermod->mydbname->medb(0);
		$this->mydb_br = $this->myusermod->mydbname->medb(1);
	}
	
	public function get_token($metknkey='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$mtkn_code = '';
		if ($metknkey == '7c2070df9e40e1af1d2ef5ea7b83428ad30d14842bd93945c18efa516926f718ef8338f0d71e4bbfceaa71319caa1c56bf0430e75f86d330ba48e113c63cb0ce'):
			$ctbltkn = $this->mydb_br . ".`trx_POS_tkn`";
			$str = "CREATE TABLE if not exists {$ctbltkn} (
			`recid` int(10) NOT NULL AUTO_INCREMENT,
			`MTKN_CODE` varchar(150) NOT NULL,
			`MPROCDATE` timestamp NULL DEFAULT current_timestamp(),
			`MRECFLAG` varchar(1) NOT NULL DEFAULT '',
			UNIQUE KEY `idx01` (`MTKN_CODE`),
			KEY `recid` (`recid`),
			KEY `idx02` (`MRECFLAG`) 
			) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
			$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$str = "select (max(`recid`) + 1) mecntr from {$ctbltkn}";
			$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRow();
			$mtkn_code = hash('sha384', $rw->mecntr . $mpw_tkn); 
			$q->freeResult();
			$str = "insert into {$ctbltkn} (
			`MTKN_CODE`
			) values('$mtkn_code')";
			$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		endif;
		return $mtkn_code;
	} //end get_token
	
	public function mdata_pos_dload($ctkn ='',$B_CODE='',$mdatadload = '') { 
		$lproc = 0;
		$ldload = 0;
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$ctbltkn = $this->mydb_br . ".`trx_POS_tkn`";
		//verify token to for download 
		$str = "select `MTKN_CODE` from {$ctbltkn} where `MTKN_CODE` = '$ctkn'";
		$q = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0):
			$lproc = 1;
			$str = "update {$ctbltkn} set `MRECFLAG` = 'Y' where `MTKN_CODE` = '$ctkn'";
			$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		endif;
		$q->freeResult();
		$mpath = ROOTPATH . 'public/downloads/mdata-pos';
		if (!is_dir($mpath)) {
			mkdir($mpath, 0777, true);
		}
		//$B_CODE = 'E0023';
		$mpath_br = $mpath . '/' . $B_CODE . '/';
		if (!is_dir($mpath_br)) {
			mkdir($mpath_br, 0777, true);
		}
		$aDataFiles = array();
		if ($mdatadload == 'MDATA-POS-PRODUCT' && $lproc) {
			$aDataFiles[] = 'mdatapos.Product.txt';
			$aDataFiles[] = 'mdatapos.ProductPricing.txt';
			$aDataFiles[] = 'mdatapos.ProducBranch.txt';
			$aDataFiles[] = 'mdatapos.ProductDetail.txt';
			$aDataFiles[] = 'mdatapos.ProductType.txt';
			$aDataFiles[] = 'mdatapos.ProductClass.txt';
			$aDataFiles[] = 'mdatapos.ProductGroup.txt';
			$aDataFiles[] = 'mdatapos.ProductSubGroup.txt';
			foreach($aDataFiles as $mefile):
				if(file_exists($mpath_br . $mefile)):
					unlink($mpath_br . $mefile);
				endif;
			endforeach;		
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/masterdata-export-product.py';
			exec("/usr/bin/python3 $mescript $B_CODE $mpath_br",$output);
			//var_dump($output);
			$zip = new ZipArchive;
			$mezipname = $B_CODE . '_MDATA-POS-PRODUCT.zip';
			//echo $mezipname . '<br/>';
			//echo $mpath_br . '<br/>';
			//die();
			$mefilezip = $mpath . $mezipname;
			if (file_exists($mefilezip)):
				unlink($mefilezip);
			endif;
			if ($zip->open($mefilezip, ZipArchive::CREATE) === TRUE)
			{
				// Add files to the zip file
				$zip->setPassword('m3passw0rd');
				foreach($aDataFiles as $mefile):
					if(file_exists($mpath_br . $mefile)):
						$zip->addFile($mpath_br . $mefile,$mefile);
						$zip->setEncryptionName($mefile, ZipArchive::EM_AES_256, 'm3passw0rd');
					endif;
				endforeach;
				$ldload = 1;
				// All files are added, so close the zip file.
				$zip->close();
			}
		} else if($mdatadload == 'MDATA-POS-PROMOTIONS' && $lproc) {
			$aDataFiles[] = 'mdatapos.PromoDiscount.txt';
			$aDataFiles[] = 'mdatapos.PromoDiscountBranch.txt';
			$aDataFiles[] = 'mdatapos.PromoBuyXTakeY.txt';
			$aDataFiles[] = 'mdatapos.PromoBuyXTakeYBranch.txt';
			$aDataFiles[] = 'mdatapos.PromoThreshold.txt';
			$aDataFiles[] = 'mdatapos.PromoThresholdBranch.txt';
			$aDataFiles[] = 'mdatapos.PromoVoucher.txt';
			$aDataFiles[] = 'mdatapos.PromoVoucherCode.txt';
			$aDataFiles[] = 'mdatapos.PromoBuyAnyXPriceY.txt';
			$aDataFiles[] = 'mdatapos.PromoBuyAnyXPriceYBranch.txt';
			$aDataFiles[] = 'mdatapos.PromoDamage.txt';
			$aDataFiles[] = 'mdatapos.PromoDamageBranch.txt';
			$aDataFiles[] = 'mdatapos.PromoDamageDetail.txt';
			foreach($aDataFiles as $mefile):
				if(file_exists($mpath_br . $mefile)):
					unlink($mpath_br . $mefile);
				endif;
			endforeach;	
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/masterdata-export-promotions.py';
			exec("/usr/bin/python3 $mescript $B_CODE $mpath_br",$output);
			//var_dump($output);
			$zip = new ZipArchive;
			$mezipname = $B_CODE . '_MDATA-POS-PROMOTIONS.zip';
			//echo $mezipname . '<br/>';
			//echo $mpath_br . '<br/>';
			//die();
			$mefilezip = $mpath . $mezipname;
			if (file_exists($mefilezip)):
				unlink($mefilezip);
			endif;
			if ($zip->open($mefilezip, ZipArchive::CREATE) === TRUE)
			{
				// Add files to the zip file
				$zip->setPassword('m3passw0rd');
				foreach($aDataFiles as $mefile):
					if(file_exists($mpath_br . $mefile)):
						$zip->addFile($mpath_br . $mefile,$mefile);
						$zip->setEncryptionName($mefile, ZipArchive::EM_AES_256, 'm3passw0rd');
					endif;
				endforeach;
				$ldload = 1;
				// All files are added, so close the zip file.
				$zip->close();
			} 
		} else if($mdatadload == 'MDATA-POS-TERMINALS' && $lproc) { 
			$aDataFiles[] = 'mdatapos.Branch.txt';
			$aDataFiles[] = 'mdatapos.Terminal.txt';
			foreach($aDataFiles as $mefile):
				if(file_exists($mpath_br . $mefile)):
					unlink($mpath_br . $mefile);
				endif;
			endforeach;	
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/masterdata-export-branch-terminals.py';
			exec("/usr/bin/python3 $mescript $B_CODE $mpath_br",$output);
			//var_dump($output);
			$zip = new ZipArchive;
			$mezipname = $B_CODE . '_MDATA-POS-TERMINALS.zip';
			//echo $mezipname . '<br/>';
			//echo $mpath_br . '<br/>';
			//die();
			$mefilezip = $mpath . $mezipname;
			if (file_exists($mefilezip)):
				unlink($mefilezip);
			endif;
			if ($zip->open($mefilezip, ZipArchive::CREATE) === TRUE)
			{
				// Add files to the zip file
				$zip->setPassword('m3passw0rd');
				foreach($aDataFiles as $mefile):
					if(file_exists($mpath_br . $mefile)):
						$zip->addFile($mpath_br . $mefile,$mefile);
						$zip->setEncryptionName($mefile, ZipArchive::EM_AES_256, 'm3passw0rd');
					endif;
				endforeach;
				$ldload = 1;
				// All files are added, so close the zip file.
				$zip->close();
			} 
		} else if($mdatadload == 'MDATA-POS-USERS' && $lproc) { 
			$aDataFiles[] = 'mdatapos.User.txt';
			$aDataFiles[] = 'mdatapos.UserAccess.txt';
			$aDataFiles[] = 'mdatapos.UserBranch.txt';
			foreach($aDataFiles as $mefile):
				if(file_exists($mpath_br . $mefile)):
					unlink($mpath_br . $mefile);
				endif;
			endforeach;	
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/masterdata-export-users.py';
			exec("/usr/bin/python3 $mescript $B_CODE $mpath_br",$output);
			//var_dump($output);
			$zip = new ZipArchive;
			$mezipname = $B_CODE . '_MDATA-POS-USERS.zip';
			//echo $mezipname . '<br/>';
			//echo $mpath_br . '<br/>';
			//die();
			$mefilezip = $mpath . $mezipname;
			if (file_exists($mefilezip)):
				unlink($mefilezip);
			endif;
			if ($zip->open($mefilezip, ZipArchive::CREATE) === TRUE)
			{
				// Add files to the zip file
				$zip->setPassword('m3passw0rd');
				foreach($aDataFiles as $mefile):
					if(file_exists($mpath_br . $mefile)):
						$zip->addFile($mpath_br . $mefile,$mefile);
						$zip->setEncryptionName($mefile, ZipArchive::EM_AES_256, 'm3passw0rd');
					endif;
				endforeach;
				$ldload = 1;
				// All files are added, so close the zip file.
				$zip->close();
			} 
		} else if($mdatadload == 'MDATA-POS-NRC' && $lproc) { 
			$aDataFiles[] = 'mdatapos.RewardCard.txt';
			foreach($aDataFiles as $mefile):
				if(file_exists($mpath_br . $mefile)):
					unlink($mpath_br . $mefile);
				endif;
			endforeach;	
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/masterdata-export-rewardcards.py';
			exec("/usr/bin/python3 $mescript $B_CODE $mpath_br",$output);
			//var_dump($output);
			$zip = new ZipArchive;
			$mezipname = $B_CODE . '_MDATA-POS-NRC.zip';
			//echo $mezipname . '<br/>';
			//echo $mpath_br . '<br/>';
			//die();
			$mefilezip = $mpath . $mezipname;
			if (file_exists($mefilezip)):
				unlink($mefilezip);
			endif;
			if ($zip->open($mefilezip, ZipArchive::CREATE) === TRUE)
			{
				// Add files to the zip file
				$zip->setPassword('m3passw0rd');
				foreach($aDataFiles as $mefile):
					if(file_exists($mpath_br . $mefile)):
						$zip->addFile($mpath_br . $mefile,$mefile);
						$zip->setEncryptionName($mefile, ZipArchive::EM_AES_256, 'm3passw0rd');
					endif;
				endforeach;
				$ldload = 1;
				// All files are added, so close the zip file.
				$zip->close();
			}  
		} else {
		} //end if  
		
		if($lproc && $ldload):
			//Define header information
			header('Content-Description: File Transfer');
			//header('Content-Type: application/octet-stream');
			header('Content-Type: application/zip');
			//header("Cache-Control: no-cache, must-revalidate");
			header("Expires: 0");
			header('Content-disposition: attachment; filename="' . $mezipname . '"');
			header("Content-Transfer-Encoding: binary");
			header('Content-Length: ' . filesize($mefilezip));
			header("Pragma: no-cache"); 
			//header('Pragma: public');
			//Clear system output buffer
			flush();
			//ob_end_flush();
			
			//Read the size of the file
			@readfile($mefilezip);
		endif; //process download 
	} //end mdata_pos_dload
	
}  // end main class 

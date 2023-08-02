<?php

namespace App\Controllers;
use ZipArchive;

class MyTestMe extends BaseController
{
	
	public function __construct()
	{
		
		$this->myusermod = model('App\Models\MyUserModel');
	}
	
	public function testlangito() { 
		return view('MeTest');
	} //end testlangito
	
	public function index() { 
		
		$mforwhat = $this->request->getVar('forwhat');
		if($mforwhat == 'LexaLexusLexie'):
			for($aa = 0; $aa <= 31; $aa++):
				$menum = str_pad($aa,  2, "0",STR_PAD_LEFT);
				$strme = "
			  `ITEM_COST_DAY{$menum}` DOUBLE(15,4) DEFAULT 0.0000,<br/>
			  `ITEM_PRICE_DAY{$menum}` DOUBLE(15,4) DEFAULT 0.0000,<br/>
			  `ITEM_QTY_DAY{$menum}` DOUBLE(15,4) DEFAULT 0.0000,<br/>
				";
				echo $strme;
			endfor;
		endif;
		
		if($mforwhat == 'IVTYLBTMPL'):
			$meBCODE = $this->request->getVar('meBCODE');
			$d_br = $this->myusermod->mydbname->medb(1);
			$str = "create table if not exists {$d_br}.`trx_{$meBCODE}_myivty_lb_dtl` like {$d_br}.`trx_E0012_myivty_lb_dtl`";
			$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			echo "done..." . $meBCODE;
		endif;
		
		if($mforwhat == 'me-python'):
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/updateProdIDsfromPOS.py';
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/masterdata-export-product.py';
			$mpath = ROOTPATH . 'public/downloads/';
			//echo $mescript . '<br/>';
			//$output = passthru("/usr/bin/python3 $mescript me1 me2");
			//$output = passthru("/usr/bin/python3 $mescript E0023");
			//exec("/usr/bin/python3 $mescript",$output);
			exec("/usr/bin/python3 $mescript E0023 $mpath",$output);
			//var_dump($output);
			
			$zip = new ZipArchive;
			$mezipname = 'mezipme.zip';
			$mefilezip = $mpath . $mezipname;
			if (file_exists($mefilezip)):
				unlink($mefilezip);
			endif;
			if ($zip->open($mefilezip, ZipArchive::CREATE) === TRUE)
			{
				// Add files to the zip file
				$zip->setPassword('m3passw0rd');
				$zip->addFile($mpath . 'mdatapos.Product.txt','mdatapos.Product.txt');
				$zip->addFile( $mpath . 'mdatapos.ProductPricing.txt','mdatapos.ProductPricing.txt');
				$zip->setEncryptionName('mdatapos.Product.txt', ZipArchive::EM_AES_256, 'm3passw0rd');
				$zip->setEncryptionName('mdatapos.ProductPricing.txt', ZipArchive::EM_AES_256, 'm3passw0rd');
				// All files are added, so close the zip file.
				$zip->close();
				
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
			}
		endif;
		
		if($mforwhat == 'me-dload-promotions'): 
			$mescript = ROOTPATH . 'app/ThirdParty/me-python/masterdata-export-promotions.py';
			$mpath = ROOTPATH . 'public/downloads/mdata-pos';
			if (!is_dir($mpath)) {
				mkdir($mpath, 0777, true);
			}
			$B_CODE = 'E0023';
			$mpath_br = $mpath . '/' . $B_CODE . '/';
			if (!is_dir($mpath_br)) {
				mkdir($mpath_br, 0777, true);
			}
			
			
			
			//echo $mescript . '<br/>';
			//$output = passthru("/usr/bin/python3 $mescript me1 me2");
			//$output = passthru("/usr/bin/python3 $mescript E0023");
			//exec("/usr/bin/python3 $mescript",$output);
			exec("/usr/bin/python3 $mescript $B_CODE $mpath_br",$output);
			//var_dump($output);
			
			$zip = new ZipArchive;
			$mezipname = $B_CODE . '_mezippromotions.zip';
			$mefilezip = $mpath . $mezipname;
			if (file_exists($mefilezip)):
				unlink($mefilezip);
			endif;
			if ($zip->open($mefilezip, ZipArchive::CREATE) === TRUE)
			{
				// Add files to the zip file
				$zip->setPassword('m3passw0rd');
				$zip->addFile($mpath_br . 'mdatapos.PromoDiscount.txt','mdatapos.PromoDiscount.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoDiscountBranch.txt','mdatapos.PromoDiscountBranch.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoBuyXTakeY.txt','mdatapos.PromoBuyXTakeY.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoBuyXTakeYBranch.txt','mdatapos.PromoBuyXTakeYBranch.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoThreshold.txt','mdatapos.PromoThreshold.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoThresholdBranch.txt','mdatapos.PromoThresholdBranch.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoVoucher.txt','mdatapos.PromoVoucher.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoVoucherBranch.txt','mdatapos.PromoVoucherBranch.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoVoucherCode.txt','mdatapos.PromoVoucherCode.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoBuyAnyXPriceY.txt','mdatapos.PromoBuyAnyXPriceY.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoBuyAnyXPriceYBranch.txt','mdatapos.PromoBuyAnyXPriceYBranch.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoDamage.txt','mdatapos.PromoDamage.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoDamageBranch.txt','mdatapos.PromoDamageBranch.txt');
				$zip->addFile( $mpath_br . 'mdatapos.PromoDamageDetail.txt','mdatapos.PromoDamageDetail.txt');
				$zip->setEncryptionName('mdatapos.PromoDiscount.txt', ZipArchive::EM_AES_256, 'm3passw0rd');
				$zip->setEncryptionName('mdatapos.PromoDiscountBranch.txt', ZipArchive::EM_AES_256, 'm3passw0rd');
				// All files are added, so close the zip file.
				$zip->close();
				
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
			}			
		endif;
		//end me-dload-promotions
		
	} //end index
	
	
} //end main 

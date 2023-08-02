<?php 
 
use App\ThirdParty\fpdf186\fpdf;
use App\ThirdParty\fpdf186\barcode;



$request      = \Config\Services::request();
$response      = \Config\Services::response();
$mydbname     = model('App\Models\MyDBNamesModel');
$mylibzdb     = model('App\Models\MyLibzDBModel');
$mylibzsys    = model('App\Models\MyLibzSysModel');
$memelibsys   = model('App\Models\Mymelibsys_model');
$mymdcustomer = model('App\Models\MyMDCustomerModel');
$mydataz      = model('App\Models\MyDatumModel');

$this->dbx = $mylibzdb->dbx;
$this->db_erp = $mydbname->medb(1);

$tmp_time = date("F j, Y, g:i A");
$tmp_date = new DateTime($tmp_time);
$print_time = $tmp_date->format('m/d/Y g:i:s A');

$potrx_no = $request->getVar('potrx_no');

$str = "                        
SELECT
a.`potrx_no`, 
a.`muser`, 
a.`encd_date`, 
a.`hd_subtqty`, 
a.`hd_subtamt`, 
a.`muser`,
b.`qty`, 
b.`nremarks`,
b.`tamt`,
c.`ART_BARCODE1`,
c.`ART_SKU`,
c.`ART_CODE`,
c.`ART_DESC`,
c.`ART_NCONVF`,
c.`ART_UPRICE`,
dd.`VEND_ADDR1`,
dd.`VEND_NAME`,
cc.`BRNCH_NAME`,
cc.`BRNCH_ADDR1`,
cc.`BRNCH_ACCT_COMPNAME`



FROM 
trx_manrecs_po_hd a
JOIN
trx_manrecs_po_dt b
ON
a.`potrx_no` = b.`potrx_no`
JOIN
mst_article c
ON
b.`mat_code` = c.`ART_CODE`
JOIN `mst_vendor` dd
ON (a.`supplier_id` = dd.`recid`)
LEFT JOIN `mst_companyBranch` cc
ON (a.`branch_id` = cc.`recid`)
WHERE 
b.`potrx_no` = '{$potrx_no}'

";
//AND !(a.`flag` = 'C' ) AND !(a.`df_tag`='D') AND !(a.`post_tag`='N') 
// var_dump($str); 
// die();

$q3 = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
if($q3->getNumRows() == 0){ 
	$data = array('message'=>"No Data Found! <br/> Note: Maybe data already downloaded.");
	echo view('errors/html/error_404',$data);
	die();
}


$r = $q3->getResultArray();
foreach($q3->getResult() as $row){
	$potrx_no = $row->potrx_no;
	$muser = $row->muser;
	$encd_date = $row->encd_date;
	$hd_subtqty = $row->hd_subtqty;
	$hd_subtamt = $row->hd_subtamt;
	$qty = $row->qty;
	$ART_BARCODE1 = $row->ART_BARCODE1;
	$ART_SKU = $row->ART_SKU;
	$ART_CODE = $row->ART_CODE;
	$ART_DESC = $row->ART_DESC;
	$VEND_ADDR1 = $row->VEND_ADDR1;
	$VEND_NAME = $row->VEND_NAME;
	$tamt = $row->tamt;
	$art_uprice = $row->ART_UPRICE;
	$ART_NCONVF = $row->ART_NCONVF;
	$nremarks = $row->nremarks;
	$muser = $row->muser;
	$branch_name = $row->BRNCH_NAME;
	$BRNCH_ADDR1 = $row->BRNCH_ADDR1;
	$BRNCH_ACCT_COMPNAME = $row->BRNCH_ACCT_COMPNAME;



	
}
$pdfbcode = new PDF_BARCODE('P','mm','A4');

$pdfbcode->EAN13(10,10,'1234567',5,0.5,9);
$pdfbcode->Output();

$pdf = new FPDF();
$pdf->AliasNbPages();

$pdf->SetTitle('TPA #: '.$potrx_no);
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);



$pdf->AddFont('Dot','','Calibri.php');
$pdf->SetFont('Dot','',10);

// header page

//$pdf->SetFont('Dot','',15);
//$pdf->SetTextColor(0,0,0);

//$pdf->Image(site_url().'public/assets/images/SMC-LOGO.png',5,5,40,0,'png');
// $pdf->SetXY(5,10); 
// $pdf->SetFont('Dot','',15);
// $pdf->Cell(112,5,'PULL OUT DOCUMENT',1,0,'L'); 

//HEADER
$pdf->SetFont('Arial','B',12);
$pdf->Cell(200,25,'PULL OUT DOCUMENT',0,0,'C'); 

//PULLOUT DOC NO
$pdf->SetXY(5,33);  
$pdf->SetFont('Dot','',10);
$pdf->Cell(42,7,'PULL OUT DOCUMENT NO:',0,0,'L'); 

$pdf->SetXY(45.8,33);
$pdf->SetFont('Dot','',10);
$pdf->Cell(85,5,$potrx_no,'B',0,'L');  


//TO
$pdf->SetXY(5,39);  
$pdf->SetFont('Dot','',10);
$pdf->Cell(16.5,5,'TO:',0,0,'L'); 
$pdf->SetFont('Dot','',10);
$pdf->Cell(110.5,5,$branch_name,'B',0,'L');  
$pdf->SetFont('Dot','',10);

//COMPANY
$pdf->SetXY(5,45);  
$pdf->SetFont('Dot','',10);
$pdf->Cell(16.5,5,'COMPANY:',0,0,'L'); 
$pdf->SetFont('Dot','',10);
$pdf->Cell(110.5,5,$BRNCH_ACCT_COMPNAME,'B',0,'L');  
$pdf->SetFont('Dot','',10);

//ADDRESS
$pdf->SetXY(5,51);  
$pdf->SetFont('Dot','',10);
$pdf->Cell(16.5,5,'ADDRESS:',0,0,'L'); 
$pdf->SetFont('Dot','',10);
$pdf->Cell(110.5,5,$BRNCH_ADDR1,'B',0,'L');  
$pdf->SetFont('Dot','',10);



$pdf->SetXY(145,39);  
$pdf->Cell(15.5,5,'DATE/TIME CREATED:',0,0,'L'); 
$pdf->SetXY(177,38);  
$pdf->SetFont('Dot','',10);
$pdf->Cell(28,5,'','B',0,'L'); 


$pdf->SetXY(145,45);  
$pdf->SetFont('Dot','',10);
$pdf->Cell(15.5,5,'DATE NEEDED:',0,0,'L'); 
$pdf->SetFont('Dot','',10);

$pdf->SetXY(166,44);  
$pdf->Cell(40,5,'','B',0,'C');  
$pdf->SetFont('Dot','',10);

$pdf->SetXY(145,51);  
$pdf->SetFont('Dot','',10);
$pdf->Cell(15.5,5,'ENCODED BY:',0,0,'L'); 
$pdf->SetFont('Dot','',10);

$pdf->SetXY(166,50);  
$pdf->Cell(40,5,'','B',0,'C');  
$pdf->SetFont('Dot','',10);


//ITEMS TH
$pdf->SetFillColor(239,225,131,1);
$pdf->SetFont('Dot','',10);
$pdf->SetXY(5,65); 
$pdf->SetFont('Dot','',7);
$pdf->Cell(7,4,'ITEMS',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->Cell(10,4,'QTY',1,0,'C'); 
$pdf->Cell(8,4,'UNIT',1,0,'C'); 
$pdf->Cell(22,4,'BARCODE',1,0,'C'); 
$pdf->Cell(25,4,'STOCK NUMBER',1,0,'L');
$pdf->Cell(75,4,'DESCRIPTION',1,0,'C');
$pdf->SetFont('Dot','',7);
$pdf->Cell(13,4,'QTY BOX',1,0,'C');
$pdf->Cell(13,4,'QTY/BOX',1,0,'C');
$pdf->Cell(13,4,'UNIT PRICE',1,0,'C');
$pdf->Cell(13,4,'AMOUNT',1,0,'C');


//footer page number
$pdf->SetY(-15);
$pdf->SetFont('Dot','',10);
$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of TPA NO: '.$potrx_no. '  Print Time:'.$print_time,0,0,'C');

//header page number
$pdf->SetY(5);
$pdf->SetX(150);
$pdf->SetFont('Dot','',10);
$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of TPA NO: '.$potrx_no,0,0,'C');


$Y = 69;
$total_qty = 0;
$box_no = 1;
$ntqty = 0;
$ntamt = 0;
$ntcost = 0;
$ntucost = 0;
$ntuprice = 0;
foreach($q3->getResult() as $row){

	$potrx_no = $row->potrx_no;
	$muser = $row->muser;
	$encd_date = $row->encd_date;
	$hd_subtqty = $row->hd_subtqty;
	$hd_subtamt = $row->hd_subtamt;
	$qty = $row->qty;
	$ART_BARCODE1 = $row->ART_BARCODE1;
	$ART_SKU = $row->ART_SKU;
	$ART_CODE = $row->ART_CODE;
	$ART_DESC = $row->ART_DESC;
	$VEND_ADDR1 = $row->VEND_ADDR1;
	$VEND_NAME = $row->VEND_NAME;
	$tamt = $row->tamt;
	$art_uprice = $row->ART_UPRICE;
	$ART_NCONVF = $row->ART_NCONVF;
	$nremarks = $row->nremarks;
	$muser = $row->muser;
	$branch_name = $row->BRNCH_NAME;

	
		if($Y < 226){
			$border = '1';
			
			$pdf->SetFont('Dot','',8);
			$pdf->SetXY(5,$Y); 
			/*if($_recid != $xrecid){*/
				$pdf->Cell(7,5,$box_no,$border,0,'C');
				$pdf->Cell(10,5,$qty,$border,0,'C');
				$pdf->Cell(8,5,$ART_SKU,1,0,'C'); 
				$pdf->Cell(22,5,$ART_BARCODE1 ,1,0,'C'); 
				$pdf->Cell(25,5,$ART_CODE,$border,0,'L');
				$pdf->Cell(75,5,$ART_DESC,$border,0,'L');
				$pdf->Cell(13,5,'',$border,0,'C');
				$pdf->Cell(13,5,$ART_NCONVF,$border,0,'C');
				$pdf->Cell(13,5,$art_uprice,$border,0,'C');
				$pdf->Cell(13,5,$tamt,$border,0,'C');

				
		}

		else{
			//2nd pahina
			$pdf->AddPage();
			$pdf->SetAutoPageBreak(false);

			$Y = 15;

			//ITEMS TH
			$pdf->SetFillColor(239,225,131,1);
			$pdf->SetFont('Dot','',10);
			$pdf->SetXY(5,$Y); 
			$pdf->Cell(5,4,'ITEMS.',1,0,'C');
			$pdf->Cell(30,4,'QTY',1,0,'C'); 
			$pdf->Cell(30,4,'UNIT',1,0,'C'); 
			$pdf->Cell(115,4,'BARCODE',1,0,'C'); 
			$pdf->Cell(25,4,'STOCK NUMBER',1,0,'C');
			$pdf->Cell(25,4,'DESCRIPTION',1,0,'C');
			$pdf->Cell(25,4,'QTY BOX',1,0,'C');
			$pdf->Cell(25,4,'QTY/BOX',1,0,'C');
			$pdf->Cell(25,4,'UNIT PRICE',1,0,'C');
			$pdf->Cell(25,4,'AMOUNT',1,0,'C');
			
			

			//footer page numberScreenshot from 2023-04-12 14-07-03
			$pdf->SetY(-15);
			$pdf->SetFont('Dot','',10);
			$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of TPA NO: '.$potrx_no. '   Print by:'.$cuser_fullname. '   Print Time:'.$print_time,0,0,'C');
			//$pdf->SetY(-15);
			//$pdf->SetFont('Dot','',10);
			//$pdf->Cell(0,16,,0,0,'C');
			//$pdf->SetY(-15);
			//$pdf->SetFont('Dot','',10);
			//$pdf->Cell(0,22,,0,0,'C');

			//header page number
			$pdf->SetY(5);
			$pdf->SetX(150);
			$pdf->SetFont('Dot','',10);
			$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of TPA NO: '.$potrx_no,0,0,'C');

			$Y = $Y + 4;

			$pdf->SetFont('Dot','',9);
			$pdf->SetXY(5,$Y); 
			$border = '1';
			

				$pdf->Cell(7,5,$box_no,$border,0,'C');
				$pdf->Cell(10,5,$qty,$border,0,'C');
				$pdf->Cell(8,5,$ART_SKU,1,0,'C'); 
				$pdf->Cell(22,5,$ART_BARCODE1 ,1,0,'C'); 
				$pdf->Cell(25,5,$ART_CODE,$border,0,'L');
				$pdf->Cell(75,5,$ART_DESC,$border,0,'L');
				$pdf->Cell(13,5,'',$border,0,'C');
				$pdf->Cell(13,5,$ART_NCONVF,$border,0,'C');
				$pdf->Cell(13,5,$art_uprice,$border,0,'C');
				$pdf->Cell(13,5,$tamt,$border,0,'C');

		}//endfor
		$Y = $Y + 5;
		$box_no++;


	
}//endforeach

$pdf->SetXY(180,$Y);
$pdf->Cell(10,5,'TOTAL: ',0,0,'L');
$pdf->SetXY(190,$Y);
$pdf->Cell(15,5,number_format($hd_subtamt,2),'B',0,'L');

$pdf->SetXY(5,$Y);
$pdf->Cell(10,5,'TOTAL: ',0,0,'L');
$pdf->SetXY(15,$Y);
$pdf->Cell(15,5,number_format($hd_subtqty,2),'B',0,'L');


$pdf->SetXY(5,110);
$pdf->Cell(10,5,'REMARKS: ',0,0,'L');
$pdf->SetXY(18,109);
$pdf->Cell(158,5,($nremarks),'B',0,'L');

$pdf->SetXY(5,115);
$pdf->Cell(10,5,'CHECKER: ',0,0,'L');
$pdf->SetXY(18,114);
$pdf->Cell(158,5,number_format($total_qty,2),'B',0,'L');


//line only
$pdf->SetXY(47,115);
$pdf->Cell(76,7,'','B',0,'L'); 
//for beside company line
$pdf->SetXY(143,119);
$pdf->Cell(47,7,'','B',0,'L'); 
//for beside address line
$pdf->SetXY(143,124);
$pdf->Cell(47,7,'','B',0,'L'); 

$pdf->SetXY(5,122);
$pdf->Cell(20,5,'FROM: ',0,0,'C');
$pdf->SetXY(23,121);
$pdf->Cell(100,5,number_format($total_qty,2),'B',0,'L');

$pdf->SetXY(5,127);
$pdf->Cell(20,5,'COMPANY: ',0,0,'C');
$pdf->SetXY(23,126);
$pdf->Cell(100,5,number_format($total_qty,2),'B',0,'L');

$pdf->SetXY(5,132);
$pdf->Cell(20,5,'ADDRESS: ',0,0,'C');
$pdf->SetXY(23,131);
$pdf->Cell(100,5,number_format($total_qty,2),'B',0,'L');

$pdf->SetXY(5,140);
$pdf->Cell(20,5,'CHECKED BY: ',0,0,'C');
$pdf->SetXY(23,139);
$pdf->Cell(55,5,number_format($total_qty,2),'B',0,'L');

$pdf->SetXY(115,140);
$pdf->Cell(20,5,'APPROVED BY: ',0,0,'C');
$pdf->SetXY(135,139);
$pdf->Cell(55,5,number_format($total_qty,2),'B',0,'L');

$pdf->SetXY(5,148);
$pdf->Cell(20,5,'RECEIVED BY: ',0,0,'C');
$pdf->SetXY(23,147);
$pdf->Cell(55,5,number_format($total_qty,2),'B',0,'L');
$pdf->SetXY(40,152);
$pdf->Cell(20,5,'NAME/DATE/SIGNATURE ',0,0,'C');

$pdf->SetXY(5,159);
$pdf->Cell(20,5,'TOTAL PER UNIT ',0,0,'C');

$pdf->SetFillColor(239,225,131,1);
$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,164); 
$pdf->Cell(20,4,'BOX',1,0,'C'); 
$pdf->Cell(20,4,'SACK',1,0,'C'); 
$pdf->Cell(20,4,'ROLL',1,0,'C'); 
$pdf->Cell(20,4,'BUNDLE',1,0,'C');
$pdf->Cell(20,4,'PLASTIC',1,0,'C');
$pdf->Cell(20,4,'PCS',1,0,'C');
$pdf->Cell(20,4,'ST',1,0,'C');
$pdf->Cell(20,4,'TOTAL',1,0,'C');

$pdf->SetXY(10,168); 
$pdf->SetFont('Dot','',10);
$pdf->Cell(20,4,'IMPORTED',1,0,'C'); 
$pdf->SetXY(10,172); 
$pdf->Cell(20,4,'LOCAL',1,0,'C'); 
$pdf->SetXY(10,176); 
$pdf->Cell(20,4,'STORE USE',1,0,'C'); 
$pdf->SetXY(10,180); 
$pdf->Cell(20,4,'HSY',1,0,'C');
$pdf->SetXY(10,184); 
$pdf->Cell(20,4,'LSG',1,0,'C');
$pdf->SetXY(10,188); 
$pdf->Cell(20,4,'HQE',1,0,'C');
$pdf->SetXY(10,192); 
$pdf->Cell(20,4,'AYCC',1,0,'C');
$pdf->SetXY(10,196);
$pdf->Cell(20,4,'TOTAL',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,168); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,172); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,176); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,180); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,184); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,188); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,192); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');

$pdf->SetFont('Dot','',10);
$pdf->SetXY(30,196); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C'); 
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');
$pdf->Cell(20,4,'0',1,0,'C');



$pdf->SetFont('Dot','',10);
//echo $str;
$pdf->output();
exit;

?>


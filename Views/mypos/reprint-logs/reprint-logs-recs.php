<?php
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');

$mylibzdb = model('App\Models\MyLibzDBModel');
$mydatum = model('App\Models\MyDatumModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myusermod = model('App\Models\MyUserModel');

$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$mtnkid = $request->getVar('mtnkid');
$startDate = $request->getVar('startDate');
$endDate = $request->getVar('endDate');
if(empty($mtnkid)) { 
	echo "Processing MODULE FAILED!!!";
	die();
}

$str = "select CONCAT('E',BRNCH_OCODE2) MBCODE from {$mydbname->medb(0)}.mst_companyBranch aa where sha2(concat(aa.`BRNCH_CODE`,'{$mpw_tkn}'),384) = '$mtnkid'";
$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
$rw = $q->getRowArray();

	$mstart_date = $startDate;
	$mend_date = $endDate;
	$bcode = $rw['MBCODE'];
	if( $myposdbconn ) { 
		$strsql = "
		DECLARE @start_date DATETIME = '$mstart_date',
				@end_date DATETIME = '$mend_date',
				@branch_id INT = (select top(1) [id] FROM [diQTech_db].[dbo].[diQt_Branch] where [code] = '$bcode')  
				
		SELECT lr.[id]
			  ,convert(varchar(19),lr.[date],121) as Date_reprinted
			  ,FORMAT(lr.[date], 'MM/dd/yyyy') as trxdate
			  ,FORMAT(lr.[date], 'hh:mm:ss') as trxtime
			  ,s.[or_no]
			  ,b.[name] as Branch_name
			  ,t.[name] as POS_number
			  ,u.[first_name]+' '+u.[last_name] as ApprovedBy
			  ,uc.[first_name]+' '+uc.[last_name] as CashierName
		  FROM [diQTech_db].[dbo].[diQt_LogReprint] as lr
		  JOIN [diQTech_db].[dbo].[diQt_Sales] as s
		  ON lr.sales_id = s.id AND lr.terminal_id = s.terminal_id
		  JOIN [diQTech_db].[dbo].[diQt_Terminal] as t
		  ON lr.terminal_id = t.id
		  JOIN [diQTech_db].[dbo].[diQt_Branch] as b
		  ON t.branch_id = b.id
		  JOIN [diQTech_db].[dbo].[diQt_User] as u
		  ON lr.approved_by_id = u.id
		  JOIN [diQTech_db].[dbo].[diQt_User] as uc 
		  ON lr.cashier_on_duty_id = uc.id
		  WHERE CAST(s.[date] as date) BETWEEN CAST(@start_date as date) AND cast(@end_date as date)
		  AND t.[branch_id] = @branch_id
		  ORDER by lr.[date]
		";
		$stmt = sqlsrv_query( $myposdbconn, $strsql,array(), array("Scrollable"=>"buffered") );
		if( $stmt === false) {
			die( print_r( sqlsrv_errors(), true) );
		}
		$row_count = sqlsrv_num_rows( $stmt );
		//echo $row_count . '<br/>';
		if($row_count > 0) {
			echo "
			<div class=\"table-responsive\">
				<table class=\"mb-3 table-striped table-hover table-bordered table-sm\" style=\"font-size: 0.8rem !important;\">  
					<thead class=\"text-center\">
						<th></th>
						<th>Date/Time</th>
						<th>ECR #</th>
						<th>O.R. #</th>
						<th>Cashier</th>
					</thead>
			";
			$nn = 1;
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { 
					echo "<tr>
					<td>" . $nn++ . "</td>
					<td nowrap>" . $row['trxdate'] . ' / ' . $row['trxtime'] . "</td>
					<td>" . $row['POS_number'] . "</td>
					<td>" . $row['or_no'] . "</td>
					<td nowrap>" . $row['CashierName'] . "</td>
					</tr>";
			}
			echo "
			</table>
			</div>";
			
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>No Record/s found!!!</strong></div>";
		}
		sqlsrv_free_stmt( $stmt);
	} else { 
		 echo "Connection could not be established.<br />";
		 die( print_r( sqlsrv_errors(), true));
	}        


?>

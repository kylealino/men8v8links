<div class="main">
    <div id="col-md-12">
        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">PCF Replenishment Form Number:</label>
            </div>
            <div class="col-sm-9">
                <input type="number" id="number-field" class="form-control form-control-sm">
            </div>
        </div>
        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">Company Name:</label>
            </div>
            <div class="col-sm-9">
                <input type="text" id="text-field" class="form-control form-control-sm">
            </div>
        </div>

        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">Branch:</label>
            </div>
            <div class="col-sm-9">
                <input type="text" id="text-field" class="form-control form-control-sm">
            </div>
        </div>

        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">Type of expense:</label>
            </div>
            <div class="col-sm-9">
                <input type="text" id="text-field" class="form-control form-control-sm">
            </div>
        </div>

        <div class="row mb-3 mt-3 ms-1 me-1">
            <div class="col-sm-3">
                <label for="text-field">Date requested:</label>
            </div>
            <div class="col-sm-9">
                <input type="date" id="text-field" class="form-control form-control-sm">
            </div>
        </div>
    </div>
    <div id="col-md-12">
    <div class="table-responsive">
			<table class="metblentry-font" id="_tbl_pcf">
				<thead>
					<th class="text-center">
						<button type="button" id="__salesdepo_addrow" class="btn btn-sm btn-success p-1 pb-0 mebtnpt1" onclick="javascript: pcf_addRows();">
							<i class="bi bi-plus"></i>
						</button>
					</th>
					<th nowrap>Particulars</th>
					<th nowrap>Disbursement Date</th>
					<th nowrap>Amount</th>

				</thead>
				<tbody id="_tbl_pcf_contentArea">
					<tr id="tr_rec">
						<td >
							<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1 medelrecsaledepo" value="" data-rectype="dt" data-mtknid="" onClick="javascript:medelrec_saledepodt(this);">
								<i class="bi bi-trash"></i>
							</button>
						</td>
						<td >
							<input type="text" id="bankname" class="bankNameAcct form-control form-control-sm" size="30" title="" data-mtnkattr="" />
						</td>
						<td >
							<input type="date" id="bankname" class="bankNameAcct form-control form-control-sm" size="30" title="" data-mtnkattr="" />
						</td>
						<td>
							<input type="number" id="accountnumbe" size="30" title="" class="form-control form-control-sm" value="" data-mtnkattr="" onblur="metamtpcf()" />
						</td>
					</tr> 
					<tr style="display:none;">
						<td>
							<button type="button" class="btn btn-sm btn-danger p-1 pb-0 mebtnpt1" data-rectype="dt" onClick="javascript:medelrec_saledepodt(this);" >
								<i class="bi bi-trash"></i>
							</button>
						</td>
						<td>
							<input type="text" class="bankNameAcct form-control form-control-sm" size="30" data-mtnkattr="" />
						</td>
						<td>
							<input type="date" class="bankNameAcct form-control form-control-sm" size="30" data-mtnkattr="" />
						</td>
						<td>
							<input type="number" id="_acct_name" size="30" class="form-control form-control-sm" onblur="metamtpcf()"/>
						</td>
					</tr>               
				</tbody>
				<tfoot>
					<tr style="background-color:#ababab78;" class="font-weight-bold">
						<td></td>
						<td colspan="2" style="padding:8px;" class="text-left">TOTAL AMOUNT:</td>
						<td class="text-left" id="__meTotalAmtpcf">
						</td>
					</tr>	
                </tfoot>
			</table>
			<br/>
		</div>
    </div>
</div>
<script>
    __mysys_apps.meTableSetCellPadding("_tbl_pcf",3,"1px solid #7F7F7F");
</script>
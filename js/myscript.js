(function($){
	
	// Show loading gif icon when ajax has started
	$(document).on({
		ajaxStart: function(){
			$("body").addClass("loading"); 
		},
		ajaxStop: function(){ 
			$("body").removeClass("loading"); 
		}    
	});
	
	// Close modal box on wpDataTable when clicking close
	$(document).on('click','[data-dismiss="modal"]', function () {
		$('#wdt-frontend-modal').modal('hide')
	});
	
	/*
	**   Get Account name, and set it to the dropdown as Selected value
	*/
	var account = $('.searched_account').text();
	let ajaxurl = "wp-admin/admin-ajax.php";
	
	
	// Pre-fill the Filter fields if fields have values
	if (account!='') {
		$('.filterActions__account option[value=' + account + ']').attr('selected', true);
		$('#transaction_date').attr('disabled', false);
	}
	
	$('.filterActions__loanNo input[name="loan_no"]').val( $('.searched_loan_no').text() );
	$('.filterActions__custNo input[name="cust_no"]').val( $('.searched_cust_no').text() );
	
	
	/* --------------------------------------------
	** On Change of Account, trigger a click on the Filter button
	* --------------------------------------------- */ 
	$(document).on('change', '.filterActions__account', function(){
		$(".filterActions__btn input[name='filter']").trigger('click');
	});
	
	/* --------------------------------------------
	** Save Button for each row
				* --------------------------------------------- */ 
	$(document).on('click','.btn_save', function() {
		
		// Check if Transaction Date is filled in, and continue
		if ( $('input.transaction_date').val() ){
			var date = new Date( $('input.transaction_date').val() );
			day = ('0' + (date.getDate())).slice(-2);
			month = ('0' + (date.getMonth()+1)).slice(-2);
			year = date.getFullYear();
			var transDate = [year, month, day].join('-');
		} 
		else {
			alert('Please enter a Transaction Date.');
			$( "input.transaction_date" ).focus();
			return false;
		}
		
		$(this).closest('tr').each(function() {
			// Declare all variables and get all data ready for saving to DB
			var amt_received = $(this).find('td #amt_received').val();
			var desc1 = $(this).find('input[name="description1"]').val(); //Loan No.
			var paidRemark = $(this).find('input[type="radio"]:checked').attr('data-value');
			var btn = $(this).find('.btn_save');		
			var custNo = $(this).find('td:eq(3)').text(); //Customer No.
			var loanNo = $(this).find('td:eq(0)').text(); //Loan No.
			var name = $(this).find('td:eq(4)').text(); //Loan No.
			var bName = $(this).find('td:eq(5)').text().replace(/'/g,""); //Loan No.
			var loanDate = $(this).find('td:eq(6)').text(); //Loan No.
			var totalLoanAmt = $(this).find('input[name="totalloanamt"]').val(); //Loan No.
			var route_no = $(this).find('input[name="route_no"]').val(); //Loan No.
			var balance = $(this).find('input[name="balance"]').val(); //Loan No.
			
			// If Amt Received is not empty, POST data via AJAX, and save to DB
			if( amt_received && amt_received!="" ){
				$.ajax({
					url: "/lfrlending/wp-content/themes/lfr-lending/save_transaction.php",
					type: "POST",
					dataType: 'json',
					data: {
						transDate: transDate,
						desc1: desc1,
						desc2: '',
						paidRemark: paidRemark,
						amt_received: amt_received,
						custNo: custNo,
						loanNo: loanNo,
						balance: balance,
						totalLoanAmt: totalLoanAmt,	
						account: account,
						route_no: route_no,
						name: name,
						bName: bName,
						loanDate: loanDate,				
					},
					cache: false,
					success: function(data){
						if(data.statusCode==200){
							$('.' + loanNo).find('td .btn_save').attr( "disabled", "disabled" );
							$('.' + loanNo).addClass('saved');
							$('.' + loanNo).attr('data-id', data.last_id);
							
							console.log("Transaction row saved for " + loanNo);
						}
						else if(data.statusCode==201){
							alert("Error occured !");
						}
										
					}
				});
			}
			else{
				alert('Please input Amount Received.');
				$(this).closest('tr').find('td #amt_received').focus();
				return false;
			}
		});
	});
	
	
	/* --------------------------------------------
	** Edit Button for each row
	* --------------------------------------------- */ 
	$(document).on('click','.btn_edit',function(e) {
		
		var id=$(this).attr("data-id");
		
		$(this).closest('tr').each(function() {
			// Declare all variables and get all data ready for saving to DB
			var loanNo = $(this).find('td:eq(0)').text(); //Loan No.
			var name = $(this).find('td:eq(4)').text(); //Name
			var paidRemark = $(this).find('input[type="radio"]:checked').attr('data-value');
			var amt_received = $(this).find('td #amt_received').val();
			
			console.log( 'selected id to be edited is ' + id );
			
			$('#id_u').val(id);
			$('#name_u').val(name);
			$('#loanNo_u').val(loanNo);
			// $('#paidRemark_u').val(paidRemark);
			$('#amt_received_u').val(amt_received);
			$('.paidRemarks input[data-value="' + paidRemark + '"]').prop('checked',true);
			
			$('.paidRemarks input[type="radio"]').attr('name', 'Paid_' + loanNo);
			$('.paidRemarks label').attr('for', 'Paid_' + loanNo);
			$('.edit_form__name').text(loanNo + ' - ' + name);
			$('.paidRemarks input[data-value="TRUE"]').attr('id', 'PaidTrue_' + loanNo);
			$('.paidRemarks input[data-value="FALSE"]').attr('id', 'PaidFalse_' + loanNo);
			
					
		});
	});
	
	
	/* --------------------------------------------
	** Update button on the Edit Modal Box
	* --------------------------------------------- */ 
	$(document).on('click','#update',function(e) {
		// var data = $("#edit_form").serialize();
		
		// Get final values on the Edit Payment Modal box
		var id=$('#id_u').val();
		var loanNo = $('#loanNo_u').val(); //Loan No.
		var amt_received_edit = $('#amt_received_u').val();
		var paidRemark_edit = $('.paidRemarks input[name="Paid_' + loanNo + '"]:checked').attr('data-value');
		
		// console.log( 'paidRemark_edit', paidRemark_edit );
		// console.log( 'loan no', loanNo );
		// return false;
		
		// If Amt Received is not empty, POST data via AJAX, and save to DB
		if( amt_received_edit && amt_received_edit!="" ){
			
			console.log( 'Updated amt_received is ' + amt_received_edit );
			console.log( 'Updated paidRemark is ' + paidRemark_edit );
			
			// Modal Box Update Button 
			$.ajax({
				data: {
					id: id,
					amt_received: amt_received_edit,
					paidRemark: paidRemark_edit
				},
				type: "POST",
				dataType: 'json',
				url: "/lfrlending/wp-content/themes/lfr-lending/update_transaction.php",
				cache: false,
				success: function(dataResult){
					//var dataResult = JSON.parse(dataResult);
					// console.log(data);
					
					if(dataResult.statusCode==200){
						console.log( dataResult );
						alert('Data updated successfully !'); 
						console.log( 'Updated amount for Loan ' + loanNo + ' is ' + dataResult.new_amt_received);
						$('tr.' + loanNo + ' input#amt_received').val(dataResult.new_amt_received); // Update amount on row data
						$('tr.' + loanNo + ' input[name="Paid_' + loanNo + '"]').prop('checked', false).change(); 
						$('tr.' + loanNo + ' input#Paid' + dataResult.new_paidRemark + '_' + loanNo).prop('checked', true).change(); 
						
						$('.btn-close').trigger("click");					
					}
					else if(dataResult.statusCode==201){
					   alert(dataResult);
					}
				}
			});
		}
		else{
			alert('Please input Amount Received.');
			$('#amt_received_u').focus();
			return false;
		}	

	});
	
	
	/* --------------------------------------------
	** Re-check whoever pays on that specific Date and specific Account,
	** Then highlight it on the Add Multiple Transactions table list
	* --------------------------------------------- */ 
	$(document).on('change', '#transaction_date', function() {
		var transDate = $(this).val();
		var accountSelect = $('.filterActions__account').val();
		var currDate = new Date();
		
		// Check transaction date if its over the current date
		if (new Date(transDate).getTime() > currDate.getTime()) {
			  alert("Transaction Date must not be over the current date.");
			  $('#transaction_date').val('');
			  return false;
		 }
		
		// Clear all .saved class on all results
		$('.filterResults tr').removeClass('saved');
		
		if ( accountSelect ){
			$.ajax({
				data: {
					transDate: transDate,
					account: account
				},
				type: "POST",
				dataType: 'json',
				url: "/lfrlending/wp-content/themes/lfr-lending/read_transactions.php",                         
				success: function(response){                    
					// console.log( response[0] );
					if (response){
						var i = 0;
						while (i < response.length) {
							// console.log(response[i]);
							var targetTR = $('tr.' + response[i].loan_no);
							var uLoanNo = response[i].loan_no;
							var uPaidRemark = response[i].paid;
							var rowCount = $('.filterResults tr.saved').length;
							
							uPaidRemark = uPaidRemark.toLowerCase().replace(/\b[a-z]/g, function(letter) {
								return letter.toUpperCase();
							});
							
							// console.log( uLoanNo + ' - ' + uPaidRemark );
							
							$('tr.' + response[i].loan_no).find('td .btn_save').attr( "disabled", false );
							// Update the row and fields for the fetched data/values from DB
							targetTR.addClass('saved');
							targetTR.find('#amt_received').val(response[i].amt_received);
							$('tr.' + uLoanNo + ' input[name="Paid_' + uLoanNo + '"]').prop('checked', false).change(); 
							$('tr.' + uLoanNo + ' input#Paid' + uPaidRemark + '_' + uLoanNo).prop('checked', true).change(); 
							
							$('.infoMsg__transDate').text('There are ' + rowCount + ' customers paid for this date.');
							
							
							i++;
						}
					}
					
					// Reset values for Amt Received and Paid Remarks for those who are not saved/edited
					$('.filterResults tr').each(function(){
						if ( !$(this).hasClass('saved') ){
							console.log( $(this).attr('data-loan_no') );
							var tempAmtRcvd = $(this).find('#amt_received').attr('placeholder');
							var tempLoanNo = $(this).attr('data-loan_no');
							
							$(this).find('#amt_received').val(tempAmtRcvd);
							$('tr.' + tempLoanNo + ' input#PaidTrue_' + tempLoanNo ).prop('checked', true).change(); 
						}
						
						
					});
					
				}
			  
			});
		}
	});
	
	
	/* --------------------------------------------
	** Auto populate Loan # on new entry
	* --------------------------------------------- */ 
	$(document).on('click','.wpDataTableID-3 .new_table_entry',function(e) {

		var loanNoField = $('.modal-dialog #table_1_loan_no');
		
		if ( loanNoField.val() == '' ){
			$.ajax({
				url: "/lfrlending/wp-content/themes/lfr-lending/read_loans.php",     
				type: "GET",
				dataType: 'json',                    
				success: function(response){                    
					if (response){
						var newLoanNo = parseInt(response[0].id) + 1;
						loanNoField.val( 'L' + newLoanNo );
						console.log(response[0].id);
					}
				},
				error: function(e){
					console.log('Error: ' + e);
				}
			  
			});
		}
	});
	
	/* --------------------------------------------
	** Auto populate Cust # on new entry
	* --------------------------------------------- */ 
	$(document).on('click','.wpDataTableID-2 .new_table_entry',function(e) {

		var custNoField = $('.modal-dialog #table_1_custnum');
		
		if ( custNoField.val() == '' ){
			$.ajax({
				url: "/lfrlending/wp-content/themes/lfr-lending/read_customers.php",     
				type: "GET",
				dataType: 'json',                    
				success: function(response){                    
					if (response){
						var newCustNo = parseInt(response[0].id) + 1;
						custNoField.val( 'C' + newCustNo );
						console.log(response[0].id);
					}
				},
				error: function(e){
					console.log('Error: ' + e);
				}
			  
			});
		}
	});
	
	
	/*
	** Reset the Filter Form field
	*/
	$(".reset").click(function() {
		$(this).closest('form').find("input[type=text], textarea").val("");
		$(this).closest('form').find("select.filterActions__account").attr('selectedIndex', '-1').children("option:selected").removeAttr("selected");
		$(this).closest('form').submit();
	});
	
	
	/*
	** Loan Details List - Open Slide Out Panel and show all transactions
	*/
	const slideOutPanel = $('#slide-out-panel').SlideOutPanel({
		enableEscapeKey: true,
		closeBtnSize: '18px',
		width: '50vw',
		screenZindex: '9998',
	});
	
	$(document).on('click', '.btn_view_payments', function() {
	
		$('.loanDetails_main > table > tbody').html('');
		$('.message_box').html('');
		
		slideOutPanel.open();
		
		var loan_no = $(this).attr('data-loan_no');
		$('.slidePanel_loanNo').text(loan_no);
		
		if ( loan_no ){
			$.ajax({
				data: {
					loan_no: loan_no
				},
				type: "POST",
				dataType: 'json',
				url: "/lfrlending/wp-content/themes/lfr-lending/read_transactions.php",                         
				success: function(response){    
					// console.log( response[0] );
					if (response){
						var i = 0, rowData='';
						
						while (i < response.length) {
							var transDate = response[i].transaction_date;
							var desc1 = response[i].description_1;
							var desc2 = response[i].description_2;
							var paidRemark = response[i].paid;
							var amtReceived = response[i].amt_received;
							
							rowData += '<tr><td>' + transDate + '</td><td>' + desc1 + '</td><td>' + desc2 + '</td><td>' + paidRemark + '</td><td>' + amtReceived + '</tr>';
							
							i++;
						}
						
						$('.loanDetails_main > table > tbody').html(rowData);
						
					}
					else {
						$('.message_box').html("<br>No payments found or it may have been deleted.");
					}
					
					// Reset values for Amt Received and Paid Remarks for those who are not saved/edited
					$('.filterResults tr').each(function(){
						if ( !$(this).hasClass('saved') ){
							// console.log( $(this).attr('data-loan_no') );
							var tempAmtRcvd = $(this).find('#amt_received').attr('placeholder');
							var tempLoanNo = $(this).attr('data-loan_no');
							
							$(this).find('#amt_received').val(tempAmtRcvd);
							$('tr.' + tempLoanNo + ' input#PaidTrue_' + tempLoanNo ).prop('checked', true).change(); 
						}
					});
				}
			  
			});
		}
		
		
	});
	
	
	// Slide Out Panel
	
	
})(jQuery);


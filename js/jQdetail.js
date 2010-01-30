$(document).ready(function(){
	//prepareForm();
	/* ===== Comment Area Toggle ===== */
	$('#commentsHeader').click(function() {
		$('#commentsHeader').slideToggle("slow");
		$('#commentsArea').slideToggle("slow");
	});
	$('#commentsArea').click(function() {
		$('#commentsArea').slideToggle("slow");
		$('#commentsHeader').slideToggle("slow");
	});
	/* ===== Scroll to Approvals ===== */
	$('#requestStatus, .appJump').click(function() {
		$.scrollTo( $('#approvals_panel'), {speed:2500} );
	});	
//	$('#backToTop').click(function() {
//		$.scrollTo( $('#CompanyLogo'), {speed:2500} );
//	});	
	/* ===== Administration Panel Toggle ===== */
//	$('#adminPanelMenu').click(function() {
//		$('#adminPanel').slideToggle("slow");
//	});		
	/* ===== History Panel Toggle ===== */
//	$('#historyPanelMenu').click(function() {
//		$('#historyPanel').slideToggle("slow");
//	});	
	/* ========== Approvals ========== */
//	if (level==approval) {
//		var row = '#'.concat(approval).concat('Status');
//		$(row).addClass('highlight');	
//	}
//	if (status=='X') {
//		$(canceled).addClass('canceledHighlight');	
//	}
	/* ========== Requisition Status ========== */
	switch (status) {
		case 'N':
			$('#requestStatus').addClass('newStatus');
		break;		
		case 'A':
			$('#requestStatus').addClass('approvedStatus');
		break;			
		case 'O':
			$('#requestStatus').addClass('kickoffStatus');
		break;
		case 'X':
		case 'C':
			$('#requestStatus').addClass('canceledStatus');
		break;		
	}
	/* ===== jQuery UI Calendar ===== */
	$.datepicker.setDefaults({showOn: 'both', buttonImageOnly: true, dateFormat: 'YMD-', buttonImage: '/Common/images/calendar.gif'});
	$('input.popupcalendar').datepicker();
});						
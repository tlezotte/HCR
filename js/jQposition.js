$(document).ready(function(){
	/* ===== Strip Item Information ===== */
	$('#positionTable tr:odd').addClass('odd');
	$('#positionTable tr:even').addClass('even');
	/* ===== Display Position Title Center ===== */ 
	$('#openPosition').click(function() {  
		$('#addPositionContent').slideToggle("slow");
		$('#openPosition').hide();
		$('#closePosition').show();
	});
	$('#closePosition').click(function() {  
		$('#addPositionContent').slideToggle("slow");
		$('#openPosition').show();
		$('#closePosition').hide();
	});	
});						
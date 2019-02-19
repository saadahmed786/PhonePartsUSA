
function addToCartQty(product_id, e) {
var input = $(e).find("#qty"+product_id);
var qty = $( "#qty"+product_id ).val();
	//var qty = input.val();
	
	var qty_min = input.data('min');

	if (qty.length<1) return;

	if (!isNaN(qty)) {
		
		
		if(!qty) {
			if (!qty_min) {
				qty = qty_min;
			} else {
				qty = 1;
			}
		}
		input.val(qty_min);

		boss_addToCart(product_id,qty);

	}
}








function addQty(e) {
	var input = $(e).parent().find('input[type=text]');
	var qty_min = input.data('min');
	if (isNaN(input.val())) {
		input.val(qty_min);
	}
	input.val(parseInt(input.val())+1);
}

function subtractQty(e) {
	var input = $(e).parent().find('input[type=text]');
	var qty_min = input.data('min');
	if (isNaN(input.val())) {
		input.val(qty_min);
	}
	if ($(input).val()>qty_min) {
		$(input).val(parseInt($(input).val())-1);
	}
}
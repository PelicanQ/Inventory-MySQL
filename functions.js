function updateItemList(callback){
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "server.php");
	xhr.responseType = "document";
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.onload = () => {
		console.log("ITEMS RECIEVED");
		console.log(xhr.response);
		ITEM_OBJ = JSON.parse(xhr.response.getElementById("all_items").innerHTML);
		ALL_ITEMS = Object.getOwnPropertyNames(ITEM_OBJ);
		callback();
	}
	xhr.send('{"action":"all_items"}'); 
}
function generateChange(){
//Call during setup to add eventhandlers and items in <select>		
	var itemSelect = $("#1 td .itemField");
	for(var i in ALL_ITEMS){
		itemSelect.append($("<option>").append(ALL_ITEMS[i]));
	}
	itemSelect.change(
		(event) => {
			refreshRow(event.target.value);
		}
	);
	var plus = $("#1 .plus");
	var minus = $("#1 .minus");
	plus.click(function() {
		var numField = $(this).parents("tr").find(".amountField")[0];
		numField.value = parseInt(numField.value) + 1;
	});
	minus.click(function(){
		var numField = $(this).parents("tr").find(".amountField")[0];
		numField.value = numField.value >= 1 ? parseInt(numField.value) - 1 : 0;
	});
	
}
function refreshRow(item){
	//On change table :this changes markets and variants according to specified item
	
	console.log(item);
	var variantField = $("#1").find(".variantField"); 
	variantField[0].innerHTML = "";
	var itsVariants = ITEM_OBJ[item].variant;
	for(var i in itsVariants){
		variantField.append("<option>"+ itsVariants[i] +"</option>");
	}
	console.log(ITEM_OBJ[item].variant);
	var marketField = $("#1").find(".marketField");
	marketField[0].innerHTML = "";
	var itsMarkets = ITEM_OBJ[item].market;
	if(itsMarkets == ""){
		marketField.append("<option>(none)</option>");
	}
	else {
		for(var i in itsMarkets){
			marketField.append("<option>" + itsMarkets[i] + "</option>");
		}
	}
}
function sendEdit(params) {
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "server.php");
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.responseType = "document";
	
	if(!params){
		params = {
			action : $("#1").find(".actionField")[0].value.toLowerCase(),
			item : $("#1").find(".itemField")[0].value,
			variant : $("#1").find(".variantField")[0].value,
			market : $("#1").find(".marketField")[0].value,
			amount : $("#1").find(".amountField")[0].value
		};
	}
	if(ITEM_OBJ[params.item].market.length == 0) 
		params.market = "";

	xhr.onload = () => {
		var errors = xhr.response.getElementsByClassName("anError");
		if(errors.length > 0){
			var resWindow = window.open();
			resWindow.document.body = xhr.response.body.cloneNode(true);
			return;
		}
		getAllOverview(params);
		
	}

	if(parseInt(params.amount) % 1 !==0 || parseInt(params.amount) <= 0 ){
		console.log("not positive integer > 0");
		xhr.abort();
	}
	else {
		xhr.send(JSON.stringify(params));
	}
	$("#1").find(".amountField")[0].value = "0";
		
}
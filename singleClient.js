var ITEM_OBJ = {}; //array of objects (items) containig respective variants and markets
var ALL_ITEMS = [];//an array of strings of item names
var previousAmounts = {};
//SET UP
updateItemList( () => {
	$(document).ready( () => {
		console.log("Page load");
		var children = $("#change thead tr").children();
		for(var i = 0; i < children.length-1; i++){
			$("#updateContainer").parent().prepend("<td></td>");
		}
		$("#1 .itemField")[0].disabled = true;
		for(var i in ALL_ITEMS){
			//this adds the items to #single select
			$("#single").append("<option>"+ ALL_ITEMS[i] +"</option>");
		}
		$("#single").change((event) => {
			//when item dropdown changes
			var newVal = event.target.value;  
			$("#1 .itemField")[0].value = newVal;
			refreshRow(event.target.value);
			getAllOverview();
		});	
		$("#overview #upLeftSelect").change((event)=>{
			//When varaint/market changes
			getAllOverview();
			console.log("AFUAFHAW");	
			console.log(event.target.value);

		});
		generateChange();	
		refreshRow(ALL_ITEMS[0]);
		getSingle(ALL_ITEMS[0], "variant");
	});
});
//END OF SETUP
function getSingle(whatItem, attr, targetObj){
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "server.php");
	xhr.responseType = "document";
	xhr.onload = () => {
		console.log(xhr.response);
		if(xhr.response.getElementsByClassName("anError").length > 0){
			var a = window.open();
			a.document.body = xhr.response.body.cloneNode(true);
			return;
		}


		$("#overview table").replaceWith(xhr.response.getElementsByTagName("table")[0]);
		
		$("#overview #upLeftSelect").change((event) => {
			//When varaint/market changes
			getAllOverview();
		});
		//compare 
		
		if(!targetObj) return;
		var targetTd;
		var tableRows = $("#overview tbody tr");
		var headCells = $("#overview thead th");

		for(var c = 0; c < tableRows.length; c++){
			
			if(tableRows[c].firstElementChild.innerHTML !== targetObj[attr]){
				continue;
			}
			for(var f = 0; f < headCells.length; f++){
				
				var otherAttr = (attr == "variant") ? "market" : "variant";
				if(headCells[f].innerHTML == targetObj[otherAttr]){
					targetTd = tableRows[c].children[f];
				}
			}
			
		}

		setTimeout(() => {
			$(targetTd).css("transform", "scale(1.5)");
			$(targetTd).css("color", "#0F0");
			if(targetObj.action == "remove" ) $(targetTd).css("color", "#F00");
		}, 10);
		setTimeout(() => {
			$(targetTd).css("transform", "scale(1)");
			$(targetTd).css("color", "#FFF");
		}, 1000);
	}
	var searchData = {
		action : "specific",
        attr  : attr,
		item : whatItem,
		variant : "",
		market : ""
	};
	xhr.send(JSON.stringify(searchData));
} 
function getAllOverview(params){
	getSingle($("#single")[0].value, $("#overview #upLeftSelect")[0].value.toLowerCase(), params);
}
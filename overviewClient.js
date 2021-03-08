var ITEM_OBJ = {};
var ALL_ITEMS = [];
var PREV = {};
updateItemList( () => {
	$(document).ready( () => {
		var children = $("#change thead tr").children();
		for(var i = 0; i < children.length-1; i++){
			$("#updateContainer").parent().prepend("<td></td>");
		}
		generateChange();	
		refreshRow(ALL_ITEMS[0]);
		getAllOverview();
	});
});

function getData(whatItem, effect){
	console.log("getting " + whatItem)
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "server.php");
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.responseType = "document";
	xhr.onload = () => {
		console.log(xhr.response);
		if(xhr.response.getElementsByClassName("anError").length > 0){
			var a = window.open();
			a.document.body = xhr.response.body.cloneNode(true);
			return;
		}
		var items = xhr.response.getElementsByClassName("block");
		for(var i = 0; i < items.length; i++){
			console.log(items);
			//make new row for table
			var newRow = $("<tr></tr>");
			newRow[0].id = whatItem;
			var columns = items[i].getElementsByClassName("back");
			newRow.append("<td>" +  whatItem + "</td>");
			for(var j = 0; j< columns.length; j++){
				var newTd = $("<td>"+  columns[j].innerHTML + "</td>");
				
				
				newRow.append(newTd);
				if(PREV[whatItem] != columns[j].innerHTML && PREV[whatItem] != undefined){
					var diff = parseInt(columns[j].innerHTML)-parseInt(PREV[whatItem]);
					var effect = $("<span class='effect'> +" + diff.toString() + "</span>");
					if(diff < 0)
						var effect = $("<span class='effect' style='color: #F00;'>" + diff.toString() + "</span>");
					newTd.append(effect);
					setTimeout(() => effect.css("transform","scale(1.3)"),10)
					setTimeout(() => {
						effect.css("transform","scale(0)");
						setTimeout(()=> effect.remove(), 2000);
					}, 2000);

				}
				PREV[whatItem] = columns[j].innerHTML;
			}

			//now add that row to table
			var existingRow = $("#overview tbody").find("#" + whatItem);
			if(existingRow.length == 1){
				existingRow.replaceWith(newRow[0]);
			}
			else if(existingRow.length == 0){
				$("#overview tbody").append(newRow[0]);
			} else 
				console.log("WTF i found not zero or one rows id " + whatItem);

		}

		sort();
	}
	var searchData = {
		action : "search",
		item : whatItem.toLowerCase(),
		variant : "",
		market : ""
	};
	xhr.send(JSON.stringify(searchData));
}
function getAllOverview () {
	for(var i in ALL_ITEMS){
		getData(ALL_ITEMS[i]);
	}
}
function sort(){
	ALL_ITEMS.sort();
	for(var i in ALL_ITEMS){
		$("#overview tbody tr").eq(i).before($("#overview tbody").find("#"+ALL_ITEMS[i]));
	}
	console.log("sorted");
}
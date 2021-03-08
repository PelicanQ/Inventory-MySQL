<!DOCTYPE html>
<html>
<head>
	<title>lol</title>
	<style type="text/css">
		* {
			margin : 0;
			padding : 0;
		}
		body {
			width : 100vw;
			background-color: #555;
			text-align: center;
			font-family: Arial;
			--hei : calc(100vh/15);
			position: absolute;
		}
		label {
			display: block;
			color: #fff;
			letter-spacing: 0.1vw;
			font-size: var(--hei);
			vertical-align: middle;
		}
		input {
			width : 90vw;
			height : var(--hei);
			font-size: var(--hei);
			text-align: center;
			vertical-align: middle;
		}
		#submit {
			font-size : calc(var(--hei)*1.5);
		}
		#amount {
			width: 70vw;
		}
		.count {
			display: inline-block;
			width: 10vw;
			height: var(--hei);
			font-size: var(--hei);
			vertical-align: middle;
			line-height: var(--hei);

		}
		#message {
			position: fixed;
			left: 0;
			top : 0;
			display: none;
			width : 100vw;
			height : 100vh;
			background-color : rgba(0, 0, 0, 0.7);

		}
		#content {
			position: absolute;
			left: 50%;
			top : 5%;
			transform : translate(-50%, 0);
			width : 71vw;
			background-color : #FFF;
			border : 2vw solid #888;
			border-radius: 0.5vw;
		}
		#close {
			display: inline-block;
			font-size: 3vh;
		}
	</style>
	<script src="functions.js"></script>
	<script>
		function send(){
			var inputs = document.getElementsByTagName("input");
			params = {};
			for(var i = 0; i < inputs.length; i++){
				params[inputs[i].id] = inputs[i].value;
			}
			console.log(params);
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "server.php");
			xhr.responseType = "document";
			xhr.onload = () => {
				//alert(xhr.response.documentElement.innerHTML);
				document.getElementById("message").style.display = "block";
				var box = xhr.response.body.innerHTML;
				box += "<br><button onclick='closeMessage()' id='close'>Close</button>"
				document.getElementById("content").insertAdjacentHTML("afterbegin", box);
				console.log(document.getElementById("content"));
			}
			xhr.setRequestHeader("Content-Type", "application/json");

			xhr.send(JSON.stringify(params));
		}
		function change(num){
			var val = document.getElementById("amount").value;
			if (val == 0 && num == -1) return;
			document.getElementById("amount").value = parseInt(val) + num;
		}
		function closeMessage(){
			document.getElementById("message").style.display = "none";
			document.getElementById("content").innerHTML = "";
		}
	</script>
</head>
<body> 
	
	
	<?php
	echo 
	"<label>Action</label>
	<input id='action' value='" . ($_REQUEST["action"] ?? "") . "'> 
	
	<label>Item</label>
	<input id='item' value='" . ($_REQUEST["item"] ?? "") . "'>
	
	<label>Variant</label>
	<input id='variant' value='" . ($_REQUEST["variant"] ?? "") . "'> 
	
	<label>Market</label>
	<input id='market' value='" . (($_REQUEST["market"]) ?? "") . "'>
	
	<label>Amount</label>
	
	<button class='count' onclick=change(1)>+</button>
	<input type='number' id='amount' value='" . ($_REQUEST["amount"] ?? "") . "'>
	<button class='count' onclick=change(-1)>-</button>
	
	<br>
	<button id='submit' onclick='send()'>Send</button>";
	?>

	<div id="message">
		<div id="content">
			
		</div>
	</div>
</body>
</html>
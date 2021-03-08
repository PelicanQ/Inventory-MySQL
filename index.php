<!DOCTYPE html>
<html>
<head>
	<title>iCaR Inventory</title>
	<link rel="stylesheet" type="text/css" href="stylesheets/overviewStyle.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="jQuery.js"></script>
	
	<script src="functions.js" type="text/javascript"></script>
	<script src="overviewClient.js" type="text/javascript"></script>
</head>
<body >

	<?php
	$n = basename(__FILE__) or "index.php";
	include("modules/topbar.php"); 
	?>
	
	<div class="king" id="overview">
	<table>
		<thead>
			<tr>
				<th>Item</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
	<button onclick="getAllOverview()">Refresh</button>
</div>

<?php 
include("modules/changeTable.php");
?>

</body>
</html>
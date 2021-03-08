<!DOCTYPE html>
<html>
<head>
	<title>iCaR Inventory</title>
	<link rel="stylesheet" type="text/css" href="stylesheets/overviewStyle.css">
	<link rel="stylesheet" type="text/css" href="stylesheets/singleStyle.css">
	
	<script src="jQuery.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	
	<script src="functions.js" type="text/javascript"></script> 
	<script src="singleClient.js" type="text/javascript"></script>
	
</head>
<body>
	<?php 
	$n = basename(__FILE__) or "singlePage.php";
	include("modules/topbar.php");   
	?>
		
	
	<div class="king" id="overview">
	<select id="single">
	</select>
	<table>
		<thead>
			<tr>
				<th>
					<select id="upLeftSelect">
						<option>Variant</option>
						<option>Market</option>
					</select>
				</th>
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
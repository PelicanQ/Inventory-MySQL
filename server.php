<?php
	$conn = new mysqli("127.0.0.1", "root", "", "ogge");
	if($conn->connect_error){
		exit($conn->connect_error);
	}
	$postData = new stdClass();
	if(getallheaders()["Content-Type"] != "application/x-www-form-urlencoded"){
		$postData =  json_decode(file_get_contents('php://input'));
	}
	else {
		foreach($_REQUEST as $a => $b){
			$postData->$a = $b;
		}
	}
	echo "<h4>This is the POST data that server recieved</h2>";
	if(isset($postData)){
		foreach($postData as $k => $v){
			if(gettype($v)=="string"){
				echo "$k :  $v <br>";
			}
			else {
				echo "<p>value not string </p>";
			}
		}
	}
	echo "<br><br>";

	if(!isset($postData->action)){
		err("No action defined ");
	}
	$action = strtolower($postData->action); 
	if($action == "add"){	
		addItem($postData);
	}
	elseif($action =="remove"){
		removeItem($postData);
	}
	elseif($action == "search"){
		search($postData);	
	}
	elseif($action == "specific"){
		getSpecific2($postData);
	}
	elseif($action == "all_items"){
		echo "<p id='all_items'>" . json_encode(readAllItems()) . "</p>";
	}
	else {
		echo "Request not recognized";
	}
	
	

	$conn->close();



	function addItem($params){
		global $conn;
		$item = $params->item or err("ERROR No item specified");
		$variant = $params->variant or err("ERROR No HW variant specified");
		$market = $params->market;
		$amount = $params->amount or err("ERROR No amount recieved!");
		$ITEM_OBJ = readAllItems();
		if(!in_array($item, array_keys($ITEM_OBJ))){
			err("Variant not recognized");
		}
		if(!in_array($variant, $ITEM_OBJ[$item]["variant"])){
			err("Variant not recognized");
		}
		if(!in_array($market, $ITEM_OBJ[$item]["market"])){
			err("Market not recognized");
		}
		
		if($market == "")
			$addQuery = "INSERT INTO ". $item ."(variant) VALUES ('" . $variant . "')";
		else
			$addQuery = "INSERT INTO ". $item ."(variant, market) VALUES ('".$variant . "','" . $market ."')";
		for($i = 0; $i < $amount; $i++){
			$result = $conn->query($addQuery);
		}	
		if(!$result) {
			echo("From mysql : " . $conn->error);
			err("failed to add to database");
		} 
		else { 
			echo "<p id='success'>Successfully added $amount x $item $variant $market</p>";
		}
	}
	function removeItem($params){
		global $conn;
		$item = $params->item or err("No item");
		$variant = $params->variant or err("No variant");
		$market = $params->market or err("No market");
		$amount =  $params->amount or err("No amount");
		$query1 = "SELECT id FROM $item WHERE variant LIKE '$variant' ".
		" and market LIKE '$market' ORDER BY id DESC LIMIT $amount";
		$IDResult = $conn->query($query1);
		if($conn->error){ 
			err("Failed to get ids from MySQL. ".$conn->error);
		}
		if($amount > $IDResult->num_rows) err("Trying to remove more than already exists");
		for($i = 0; $i < $IDResult->num_rows; $i++){
			$row = $IDResult->fetch_assoc();
			$delID = $row["id"];
			echo "$delID"; 
			$delQuery = "DELETE FROM $item WHERE variant='$variant' and market='$market' and id=$delID";
			$conn->query($delQuery);
			if($conn->error) err($conn->error);
		}
	}

	function search($params){
		global $conn;
		$item = $params->item or err("no item");
		$variant = $variant == "" ? "%" : $params->variant;
		$market = $market == "" ? "%" : $params->market;

		if(count(readAllItems()[$item]->markets) == 0){
			$query = "SELECT COUNT(*) FROM " . $item 
			. " WHERE variant LIKE '" . $variant . "'";
		}
		else {
			$query = "SELECT COUNT(*) FROM " . $item 
			. " WHERE variant LIKE '". $variant."' and market LIKE '". $market ."'";
		}
		$all = $conn->query($query);
		
		if(!$all) 
			exit("<p class='anError'>search query fail $variant $market</p>");

		echo "<div class='block'><p class='back'>" .
		 $all->fetch_assoc()["COUNT(*)"] . "</p></div>";

		
		/*
		for($i = 0; $i < $all->num_rows; $i++){#Loop through all rows of returned data
			$row = $all->fetch_assoc();
			echo "<div class='item'>";
			foreach($row as $k => $v){
				if(gettype($k)=="string" and gettype($v)=="string")
					echo "<p class='back'>$k : $v</p>";#Field and value echoed
				else {
					echo "<p class ='back'>All values not strings</p>";
					exit("Values not strings");
				}
			}
			echo "</div>";
		}*/
		
	}

	function getSpecific($p){
		global $conn;
		$item = $p->item;
		$attr = $p->attr;
		if($attr=="market") $otherAttr = "variant";
		elseif($attr=="variant") $otherAttr = "market";
		$thisItem = readAllItems()[$item];

		
		
		
		for($i = 0; $i < count($thisItem[$attr]); $i++){
			$totalAttr = $conn->query("SELECT $attr, COUNT(*) FROM $item WHERE $attr='" . $thisItem[$attr][$i] . "'");
			if(!$totalAttr) err($conn->error);
			$row = $totalAttr->fetch_assoc();
			echo "<div class='block'><p class='back'>". $thisItem[$attr][$i] ."</p>"; 
			echo "<p class='back'>" . $row["COUNT(*)"] ."</p>";
			
			$response = $conn->query("SELECT $otherAttr, COUNT(*) FROM $item WHERE $attr ='" 
				. $thisItem[$attr][$i] . "' GROUP BY $otherAttr ORDER BY $otherAttr ASC");
			if(!$response) err($conn->error);
			for($e = 0; $e < count($thisItem[$otherAttr]); $e++){
				$specRow = $response->fetch_assoc(); 
				echo "<p class='back' style='margin:20px'><span class='key'>" . $specRow[$otherAttr] . "</span><span class='val'>". $specRow["COUNT(*)"] ."</span></p>";
			}
			
			echo "</div>";	
		}

	}
	function getSpecific2($p){
		global $conn;
		$item = $p->item;
		$attr = $p->attr;
		if($attr=="market") $otherAttr = "variant";
		elseif($attr=="variant") $otherAttr = "market";
		$thisItem = readAllItems()[$item];


		$total = Array();//this will contain total amount of variant or market (whichever is $attr)
		$obj = Array();
		$otherList = Array();		
		foreach($thisItem[$otherAttr] as $other){
			$otherList[$other] = "0";
		}
		foreach($thisItem[$attr] as $first){
			
			$obj[$first] = count($thisItem[$otherAttr]) > 0 ? $otherList : "number";

			$totalAttr = $conn->query("SELECT COUNT(*) FROM $item WHERE $attr='" . $first . "'");
			if(!$totalAttr) err($conn->error);
			$row = $totalAttr->fetch_assoc();
			$total[$first] = (string)$row["COUNT(*)"];

			if(count($thisItem[$otherAttr]) > 0){
				$response = $conn->query("SELECT COUNT(*), variant, market FROM $item GROUP BY variant, market");
				if(!$response) err($conn->error);
				for($i = 0; $i < $response->num_rows; $i++){
					$specRow = $response->fetch_assoc();
					$obj[$specRow[$attr]][$specRow[$otherAttr]] = (string)$specRow["COUNT(*)"];
				}
			}
		}

		$currentAmounts = Array(
			"firstLayer" => $attr,
			"amounts" => Array(
				"E3" => Array(
					"EU" => 1, 
					"US" => 4, 
					"CN" => 6
				),
				"E3.5" => Array()
			)
		);
		$currentAmounts["amounts"] = $obj;

		echo "<p id='currentAmounts'>". json_encode($currentAmounts) ."</p>";
		
		echo 
		"<table>
			<thead>
				<tr>
					<th>
						<select id='upLeftSelect'>
							<option "; if($attr=="variant")echo "selected"; echo">Variant</option>
							<option ";if($attr=="market")echo "selected"; echo ">Market</option>
						</select>
					</th>	
					<th>Total</th>";
					foreach($thisItem[$otherAttr] as $other){
						echo "<th>".$other."</th>";
					}
					echo
				"</tr>
			</thead>
			<tbody>";
				foreach($thisItem[$attr] as $first){
					echo 
					"<tr>
						<td>$first</td>
						<td>" . $total[$first] . "</td>";
						if(gettype($obj[$first])=="array"){
							foreach($obj[$first] as $otherAttr => $amount){
								echo "<td>". $amount."</td>";
							}
						}
					echo			
					"</tr>";
				}
			echo 
			"</tbody>
		</table>";
		
	}

	function readAllItems(){//Reads from file
		$ALL_ITEMS = array();
		$theFile =  fopen("items.txt", "r");
		$theLine = fgets($theFile, intval(filesize("items.txt")));
		while($theLine){
			if(strpos($theLine, "#") !== 0){
				//Scanning a line thats not #comment
				//below are 4 indexes in current line
				$open1 = strpos($theLine, "(" );
				$open2 = strpos($theLine, "(", $open1+1);
				$close1 = strpos($theLine, ")");
				$close2 = strpos($theLine, ")", $close1+1);
	
				$itemFromFile = trim(substr($theLine, 0, $open1));
				$itsVariants = explode(",", str_replace(" ", "" , substr($theLine, $open1+1, $close1-($open1+1) )));
				$itsMarkets  = explode(",", str_replace(" ", "" , substr($theLine, $open2+1, $close2-($open2+1) )));
				if($itsMarkets[0]==""){
					$itsMarkets = array();
				}
				$ALL_ITEMS[$itemFromFile] = array("variant" => $itsVariants, "market" => $itsMarkets);
				
			}
			$theLine = fgets($theFile, intval(filesize("items.txt")));
		}
		return $ALL_ITEMS;
	}


	function err($str){
		echo "<h1 style='color : #F00;'>Error from server:</h1>";
		echo "<h2 class='anError' style='color : #F00;'>". $str . "</h2>";
		exit("Terminating script");
	}
?>
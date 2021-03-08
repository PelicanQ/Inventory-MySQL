<?php 
if(!isset($n)) exit("You must set \$n before including topbar man");
?>
<div id="topbar">
	<h1 id="title">ICaR Inventory</h1> 	
	<ul>
		<li <?php if($n == "index.php") echo "class='currentPage'";?> ><a href="index.php">Overview</a></li>
		<li <?php if($n == "singlePage.php") echo "class='currentPage'";?> ><a href="singlePage.php">Single Item</a></li>
		<li><a href="">Bossman</a></li>
		<li><a href="">About</a></li>
	</ul>
</div>
<div class="king" id="change">
	<table>
		<thead>
			<tr>
				<th>Action</th>
				<th>Item</th>
				<th>Variant</th>
				<th>Market</th>
				<th>Amount</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr id="1">
				<td>
					<select class='actionField inputField'>
						<option>Add</option>
						<option>Remove</option>
					</select></td>
				<td>
					<select class='itemField inputField'>
						<?php ?>
					</select></td>
				<td>
					<select class='variantField inputField'>
						
					</select></td>
				<td>
					<select class='marketField inputField'>
						
					</select></td>
				<td>
					<input type='number' class='amountField inputField' value='0' size='5' min='0'></td>
				<td>
					<button class="amountChanger plus"></button>
					<button class="amountChanger minus"></button></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td id="updateContainer">
					<button id="updateButton" onclick="sendEdit()">Update</button>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
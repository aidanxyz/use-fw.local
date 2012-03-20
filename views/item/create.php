<form action="/index.php/item/create" method="POST">
	Name of the product: <input type="text" name="item[name]"> <br>
	Details <textarea name="item[description]" cols="40" rows="5"></textarea> <br>
	Price <input type="text" name="item[price]"> <br>
	<input type="submit" value="create">
</form>

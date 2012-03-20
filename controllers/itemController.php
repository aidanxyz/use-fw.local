<?php
	class itemController
	{
		public function actionCreate()
		{
			if(isset($_POST['item']))
			{
				$item = new ItemModel;
				$filter = new filter($item->validationRules, "item");
				
				if($filter->isWrong()) {
					out::writeViewTo("create", "content", $filter->errors);
				}
				else
				{
					$stmt = db::connect()->execQuery(
						"insert into Items(name, price, specs)values(:name, :price, :specs)",
						array("name" => $filter->name, "price" => $filter->price, "specs" => $filter->specs)
					);
					
					dispatcher::getInstance()->gotoUrl("item", "view", db::connect()->getLastId());
				}
			}
			else {
				out::writeViewTo("create", "content");
			}
		}
		
		public function actionView($id = null)
		{
			if($id == null) {
				dispatcher::getInstance()->gotoUrl("item", "list");
				return;
			}
			
			$stmt = db::connect()->execQuery(
				"select * from Items where id = :id",
				array("id" => $id)
			);
			
			$object = $stmt->fetch(PDO::FETCH_OBJ);
			if($object)
			{
				out::writeViewTo("view", "content", array("model" => $object));
			}
			else
			{
				if($stmt->rowCount() == 0)
					out::writeViewTo("view", "content", array("error" => "item is not found"));
			}
		}
	}
?>

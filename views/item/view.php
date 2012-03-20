<? if(out::buffer()->exists("model")): ?>
	<p>
		Name: <?=out::buffer()->model->name; ?>
	</p>

	<p>
		Specs: <?=out::buffer()->model->specs; ?>
	</p>

	<p>
		Price: <?=out::buffer()->model->price; ?>
	</p>
<? elseif(out::buffer()->exists("error")): ?>
	<p>
		Error: <?=out::buffer()->error; ?>
	</p>
<? endif; ?>

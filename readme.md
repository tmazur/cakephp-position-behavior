<h1>CakePHP Position Behavior</h1>
<p>Allows quick reordering of your CakePHP models. Supports models with a belongsTo relation, allowing to reorder data within a category.</p>
<h2>Requirements</h2>
<p>The model must have a field (default: <code>position</code>) which holds the order of the data. If the model has a belongsTo relation, this can be used to order the data within that category.</p>
<h2>Installation</h2>
<p>Put the <code>PositionBehavior.php</code> file in the <code>/App/Model/Behavior/</code> folder.</p>
<h2>Setup</h2>
<p>In your model include the behavior: <code>public $actsAs = array('Position'=>array('categoryField'=>'category_id'));</code>. If no categoryField is used, that part can be ommited <code>public $actsAs = array('Position');</code>, or supply null: <code>public $actsAs = array('Position'=>array('categoryField'=>null));</code>. The default position field can also be changed with the <code>field</code> attribute: <code>public $actsAs = array('Position'=>array('field'=>'order', 'categoryField'=>'category_id'));</code></p>
<h2>Usage</h2>
<p>Simply call the model method <code>$model->move($id, 'up');</code>. The data can be moved up/left or down/right. The first entry shall have position no. 1 (appears at the top/leftmost part of the dataset). This behavior overloads the model's onSave method to automatically add new data at the end of the data set.</p>
<h2>License</h2>
<p>Licensed under MIT license.</p>
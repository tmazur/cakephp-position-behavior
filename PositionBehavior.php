<?php
class PositionBehavior extends ModelBehavior {
	
	public function setup(Model $Model, $settings = array()) {
	    if (!isset($this->settings[$Model->alias])) {
	        $this->settings[$Model->alias] = array(
	        	'field'=>'position', //db field holding position number
	        	'categoryField'=>null //category field, optional
	        );
	    }
	    $this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array)$settings);
	}

	public function beforeSave(Model $model) {
		if(empty($model->id)) { //if creating new entry, insert item at end
			$position = $this->_getMaxPosition($model);
			$model->data[$model->alias][$this->settings[$model->alias]['field']] = $position;
		}
	}

	/**
	 * get max position id from table
	 * @param  Model  $model model to perform action on
	 * @return int        max position id
	 */
	private function _getMaxPosition(Model $model) {
		$categoryField = $this->settings[$model->alias]['categoryField'];
		if(!empty($categoryField))
			$conditions=array($categoryField=>$model->data[$model->alias][$categoryField]);
		else
			$conditions=array();
		$this->_lazyContain($model);
		$max=$model->find('first',array('fields'=>'MAX('.$this->settings[$model->alias]['field'].') as max','conditions'=>$conditions));
		return (int)$max[0]['max']+1;
	}

	/**
	 * moves position up/down
	 * @param  Model  $model model to perform action on
	 * @param  int $id    id of element to move
	 * @param  string $dir   direction to move
	 * @return bool        success?
	 */
	public function move(Model $model, $id, $dir) {
		$positionField = $this->settings[$model->alias]['field'];
		$fields = array('id', $positionField); //fields to fetch during moving
		$categoryField = $this->settings[$model->alias]['categoryField'];
		if(!empty($categoryField)) {
			$fields[] = $categoryField;
		}
		
		$this->_lazyContain($model);		
		$active = $model->find('first',array('conditions'=>array('id'=>$id),'fields'=>$fields));
		$active_position = $active[$model->alias][$positionField];
		if($active_position>0) {
			$conditions=array();
			if(!empty($categoryField)) {
				$conditionsInit=array($categoryField=>$active[$model->alias][$categoryField]);
			} else {
				$conditionsInit=array();
			}
			if($dir=='up' || $dir=='left') {
				$order = $positionField.' DESC';
				$conditions = array_merge($conditionsInit,array($positionField.' <'=>$active_position));
			} elseif($dir=='down' || $dir=='right') {
				$order = $positionField.' ASC';
				$conditions = array_merge($conditionsInit,array($positionField.' >'=>$active_position));
			} else {
				return false; // don't know what to do
			}
			$this->_lazyContain($model);
			$passive = $model->find('first',array('conditions'=>$conditions,'order'=>$order,'fields'=>$fields));
			if($passive) { // swap data found
				$passive_position = $passive[$model->alias][$positionField];
				if($passive_position>0) {
					$passive[$model->alias][$positionField]=$active_position;
					$active[$model->alias][$positionField]=$passive_position;
					$model->save($active);
					$model->save($passive);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * shortcut for move method
	**/
	public function moveUp(Model $model, $id) {
		return $this->move($model,$id,'up');
	}

	/**
	 * shortcut for move method
	**/
	public function moveDown(Model $model, $id) {
		return $this->move($model,$id,'down');
	}

	/**
	 * if the model is containable, contain it
	 * @param  Model  $model model to contain
	 * @return void        none
	 */
	private function _lazyContain(Model $model) {
		if($model->Behaviors->hasMethod('contain')) {
			$model->contain();
		}
	}
}
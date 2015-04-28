<?php

class GridFieldSelectExisting implements GridField_HTMLProvider, GridField_DataManipulator, GridField_ColumnProvider, GridField_SaveHandler {

	private static $base_path;
	private static $column_name = "Select";

	public static function set_base_path($path) {
		self::$base_path = $path;
	}

	public function getHTMLFragments($grid) {

		$directory = Config::inst()->get(__CLASS__, 'Base') ?: self::$base_path;

		Requirements::css($directory . '/GridFieldSelectExisting.css');
		Requirements::javascript($directory . '/GridFieldSelectExisting.js');

		return '';
	}

	public function getManipulatedData(GridField $grid, SS_List $data) {
		$config = $grid->getConfig();

		$config->removeComponentsByType('GridFieldDeleteAction');
		$config->removeComponentsByType('GridFieldEditButton');

		$class = $data->dataClass;
		return $class::get();
	}

	public function getColumnsHandled($grid) {
		return [self::$column_name];
	}

	public function augmentColumns($grid, &$cols) {
		if(!in_array(self::$column_name, $cols)) {
			$cols = array_merge([self::$column_name], $cols);
		}
	}

	public function getColumnMetadata($grid, $columnName) {
		if($columnName == self::$column_name) {
			return ['title' => 'Selected'];
		}
	}

	public function getColumnAttributes($grid, $row, $columnName) {
		return [];
	}

	public function getColumnContent($grid, $row, $columnName) {

		$list = $grid->getList();

		if ($list instanceof ManyManyList) {
			$class = $row->className;
			$join = "\"$class\".\"ID\" = \"{$list->joinTable}\".\"{$list->getLocalKey()}\"";
			$record = $grid->getForm()->getRecord();
			$list = $class::get()->innerJoin($list->joinTable, $join)
				->filter([ $list->getForeignKey() => $record->ID ]);
		}

		// filter list to only include row
		$checked = ($list->filter(["ID" => $row->ID])->count() > 0);

		$checkbox = CheckboxField::create(self::$column_name, self::$column_name, $checked);
		$checkbox->addExtraClass('select-existing');
		$checkbox->setName(sprintf(
			'%s[%s][%s]', $grid->getName(), __CLASS__, $row->ID
		));

		return implode([
			$checkbox->Field(),
			'<span class="ui-icon btn-icon-accept checked"></span>',
			'<span class="ui-icon btn-icon-delete unchecked"></span>'
		]);
	}

	public function handleSave(GridField $grid, DataObjectInterface $record) {

		$list = $grid->getList();
		$value = $grid->Value();

		if(!isset($value[__CLASS__]) || !is_array($value[__CLASS__])) {
			// throw error ?
			return;
		}

		$updatedList = ArrayList::create();

		foreach($value[__CLASS__] as $id => $v) {
			if(!is_numeric($id)) {
				continue;
			}

			$updatedList->push($id);
		}

		$list->exclude([ 'ID' => $updatedList->toArray() ])->removeAll();

		foreach($updatedList->toArray() as $i => $id) {

			// if list already contains item, leave it there
			if ($list->byID($id)) continue;

			$gridfieldItem = DataObject::get_by_id($list->dataClass, $id);

			if (!$gridfieldItem || !$gridfieldItem->canEdit()) {
				continue;
			}

			$list->add($gridfieldItem);
		}
	}
}

<?php
App::uses('AppModel', 'Model');
class OpauthSetting extends AppModel {

	public $actsAs = array(
		'Expandable.Expandable' => array(
            'with' => 'OpauthSettingExpanded'
		)
	);

	public $hasMany = array(
		'Opauth.OpauthSettingExpanded'
	);
}

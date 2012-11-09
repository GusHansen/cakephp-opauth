<?php
App::uses('AppModel', 'Model');
class OpauthSettingExpanded extends AppModel {
	public $belongsTo = array(
		'Opauth.OpauthSetting'
	);
}

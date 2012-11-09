<?php
class OpauthController extends OpauthAppController {

	public $uses = array('Opauth.OpauthSetting');

	public function opauth_complete() {
		if ($this->data['validated']) {
			$data = $this->data['auth'];
			$strategy = $data['provider'];
			$auth = array(
				'key' => Configure::read(sprintf(
					'Opauth.Strategy.%s.key', 
					$strategy
				)),
				'secret' => Configure::read(sprintf(
					'Opauth.Strategy.%s.secret', 
					$strategy
				)),
				'token' => $data['credentials']['token'],
				'token_secret' => $data['credentials']['secret'],
			);
			if (!empty($data['credentials']['refresh_token'])) {
				$auth['refresh_token'] = $data['credentials']['refresh_token'];
			}

			// writing into session
			if ($this->Session->check($strategy)) {
				$auth = Hash::merge($this->Session->read($strategy), $auth);
			}
			$this->Session->write($strategy, $auth);

			// writing into db for later use
			$data = $this->OpauthSetting->findByName($strategy);
			if ($data) {
				$this->OpauthSetting->id = $data['OpauthSetting']['id'];
			} else {
				$this->OpauthSetting->create();
			}
			$auth['name'] = $strategy;
			$this->OpauthSetting->save($auth);

			// redirect to strategy url
			$redirect = Configure::read(sprintf(
				'Opauth.Strategy.%s.redirect', 
				$strategy
			));
			if ($redirect) {
				$this->Session->setFlash(__(
					'Connection with %s successful',
					$strategy
				));
				return $this->redirect($redirect);
			}
			debug($this->data);
		}
	}

	public function disconnect($strategy) {
			$strategy = Inflector::camelize($strategy);

			// delete from session
			CakeSession::delete($strategy);

			// delete cache files
			Cache::clear();

			// delete from db
			$data = $this->OpauthSetting->findByName($strategy);
			if ($data) {
				$this->OpauthSetting->delete($data['OpauthSetting']['id']);
			}

			// redirect to strategy url
			$redirect = Configure::read(sprintf(
				'Opauth.Strategy.%s.redirect', 
				$strategy
			));
			if ($redirect) {
				$this->Session->setFlash(__(
					'%s disconnected',
					$strategy
				));
				return $this->redirect($redirect);
			}
			debug(__('%s disconnected', $strategy));
	}
}

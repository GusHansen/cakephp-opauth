<?php
class OpauthController extends OpauthAppController {

	public $uses = array('Opauth.OpauthSetting');

	public function opauth_complete() {
		if ($this->data['validated']) {
			// clearing cache
			Cache::clear();

			$data = $this->data['auth'];
			$strategy = $data['provider'];
			$auth = Configure::read(sprintf(
				'Opauth.Strategy.%s', 
				$strategy
			));
			foreach ($data['credentials'] as $key => $value) {
				$auth_keys = array_keys($auth);
				if (in_array($key, $auth_keys)) {
					$key .= '2';
				}
				$auth[$key] = $value;
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
		}
		
		if (isset($this->data['error'])) {
			$data = $this->data['error'];
			$strategy = $data['provider'];
			$redirect = Configure::read(sprintf(
				'Opauth.Strategy.%s.redirect', 
				$strategy
			));
			if ($redirect) {
				$this->Session->setFlash(__(
					'%s: %s',
					$strategy,
					$data['message']
				));
				return $this->redirect($redirect);
			}
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

CakePHP plugin for Opauth
=========================
Original source https://github.com/uzyn/cakephp-opauth

CakePHP 2.x plugin for [Opauth](https://github.com/uzyn/opauth).

Opauth is a multi-provider authentication framework.

Requirements
---------
CakePHP v2.x  
Opauth >= v0.2 _(submoduled with this package)_
[Expandable](https://github.com/LubosRemplik/CakePHP-Expandable-Plugin)

Installation
------------
1.	Use [composer](https://getcomposer.org/doc/00-intro.md). 
	Add following to the `composer.json` file:

		"require": {
			"lubos/cakephp-opauth": "~1.0"
		}

	And run `php composer.phar update`

1. Download Opauth library as a submodule.

   ```bash
   git submodule init
   git submodule update
   ```

1. Add this line to the bottom of your app's `Config/bootstrap.php`:

   ```php
   <?php
   CakePlugin::load('Opauth', array('routes' => true, 'bootstrap' => true));
   ```
   Overwrite any Opauth configurations you want after the above line.

1. Load [strategies](https://github.com/uzyn/opauth/wiki/list-of-strategies) onto `Strategy/` directory.

   Append configuration for strategies at your app's `Config/bootstrap.php` as follows:
   ```php
   <?php
   CakePlugin::load('Opauth', array('routes' => true, 'bootstrap' => true));
   
   // Using Facebook strategy as an example
   Configure::write('Opauth.Strategy.Facebook', array(
       'app_id' => 'YOUR FACEBOOK APP ID',
       'app_secret' => 'YOUR FACEBOOK APP SECRET',
	   'redirect' => '/'
   ));
   ```

1. Go to `http://path_to_your_cake_app/auth/facebook` to authenticate with Facebook, and similarly for other strategies that you have loaded.

1. After validateion you will be redirected to Opauth.Strategy.Facebook.redirect url

### Note:
If your CakePHP app **does not** reside at DocumentRoot (eg. `http://localhost`), but at a directory below DocumentRoot (eg. `http://localhost/your-cake-app`),  
add this line to your app's `APP/Config/bootstrap.php`, replacing `your-cake-app` with your actual path :

```php
<?php // APP/Config/bootstrap.php
Configure::write('Opauth.path', '/your-cake-app/auth/');
Configure::write('Opauth.callback_url', '/your-cake-app/auth/callback');
```

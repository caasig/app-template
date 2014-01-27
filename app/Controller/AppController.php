<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
abstract class AppController extends Controller {

/**
 * List of components which can handle action invocation
 * @var array
 */
	public $dispatchComponents = array();

/**
 * Dispatches the controller action. Checks that the action exists and isn't private.
 *
 * If CakePHP raises MissingActionException we attempt to execute Crud
 *
 * @param CakeRequest $request
 * @return mixed The resulting response.
 * @throws PrivateActionException When actions are not public or prefixed by _
 * @throws MissingActionException When actions are not defined and scaffolding and CRUD is not enabled.
 */
	public function invokeAction(CakeRequest $request) {
		try {
			return parent::invokeAction($request);
		} catch (MissingActionException $e) {
			// Check for any dispatch components
			if (!empty($this->dispatchComponents)) {
				// Iterate dispatchComponents
				foreach ($this->dispatchComponents as $component => $enabled) {
					// Skip them if they aren't enabled
					if (empty($enabled)) {
						continue;
					}

					// Skip if isActionMapped isn't defined in the Component
					if (!method_exists($this->{$component}, 'isActionMapped')) {
						continue;
					}

					// Skip if the action isn't mapped
					if (!$this->{$component}->isActionMapped($request->params['action'])) {
						continue;
					}

					// Skip if execute isn't defined in the Component
					if (!method_exists($this->{$component}, 'execute')) {
						continue;
					}

					// Execute the callback, can return CakeResponse object
					return $this->{$component}->execute();
				}
			}

			// No additional callbacks, re-throw the normal Cake exception
			throw $e;
		}
	}

/**
 * List of global controller components
 *
 * @var array
 */
	public $components = array(
		'RequestHandler',
		'Session',
		'Crud.Crud' => array(
			'listeners' => array(
				'Crud.Api',
				'Crud.ApiPagination',
				'Crud.ApiQueryLog'
			)
		),
		'Paginator' => array('settings' => array('paramType' => 'querystring', 'limit' => 30))
	);

}

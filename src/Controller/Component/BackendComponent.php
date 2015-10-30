<?php

namespace Backend\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Backend\Controller\BackendControllerInterface;
use User\Controller\Component\AuthComponent;

/**
 * Class BackendComponent
 * @package Backend\Controller\Component
 *
 * @property \User\Controller\Component\AuthComponent $Auth
 * @property \Backend\Controller\Component\FlashComponent $Flash
 */
class BackendComponent extends Component
{
    public static $flashComponentClass = '\Backend\Controller\Component\FlashComponent';

    public static $authComponentClass = '\User\Controller\Component\AuthComponent';

    //public $components = ['Backend.Flash', 'User.Auth'];

    protected $_defaultConfig = [
        'authLoginAction' => ['plugin' => 'Backend', 'controller' => 'Auth', 'action' => 'login'],
        'authLoginRedirect' => ['plugin' => 'Backend', 'controller' => 'Auth', 'action' => 'login_success'],
        'authLogoutAction' => ['plugin' => 'Backend', 'controller' => 'Auth', 'action' => 'logout'],
        'authUnauthorizedRedirect' => ['plugin' => 'Backend', 'controller' => 'Auth', 'action' => 'unauthorized'],
        'authAuthorize' => ['Controller', 'Backend.Backend', 'User.Roles'],
        'userModel' => 'User.Users'
    ];

    /**
     * @var Controller
     */
    protected $_controller;

    public function initialize(array $config)
    {
        $controller = $this->_registry->getController();

        // Configure Backend FlashComponent
        if ($this->_registry->has('Flash') || !is_a($this->_registry->get('Flash'), static::$flashComponentClass)) {
            $this->_registry->unload('Flash');
            $controller->Flash = $this->_registry->load('Flash', [
                'className' => static::$flashComponentClass,
                'key' => 'backend',
                'plugin' => 'Backend'
            ]);
        }

        // Configure Backend Authentication
        if (!$this->_registry->has('Auth') || !is_a($this->_registry->get('Auth'), static::$authComponentClass)) {
            $this->_registry->unload('Auth');
            $controller->Auth = $this->_registry->load('Auth', [
                'className' => static::$authComponentClass,
            ]);
        }
        $controller->Auth->config('loginAction', $this->config('authLoginAction'));
        $controller->Auth->config('loginRedirect', $this->config('authLoginRedirect'));
        $controller->Auth->config('authenticate', [
            AuthComponent::ALL => ['userModel' => $this->config('userModel')],
            'Form',
            //'Basic'
        ]);

        // Configure Backend Authorization
        $controller->Auth->config('unauthorizedRedirect', $this->config('authUnauthorizedRedirect'));
        $controller->Auth->config('authorize', $this->config('authAuthorize'));

        // Configure controller
        $controller->viewBuilder()->className('Backend.Backend');
        $controller->viewBuilder()->layout('Backend.admin');

        $this->_controller =& $controller;
    }

    public function beforeFilter(Event $event)
    {
        // only act on instances of BackendControllerInterface
        //if ($event->subject() instanceof BackendControllerInterface) {
        //}

    }

    public function startup(Event $event)
    {
    }


    public function beforeRender(\Cake\Event\Event $event)
    {
        // only act on instances of BackendControllerInterface
        if ($event->subject() instanceof BackendControllerInterface) {
            $controller = $event->subject();
            $controller->set('be_title', Configure::read('Backend.title'));
            $controller->set('be_dashboard_url', Configure::read('Backend.dashboardUrl'));
            $controller->set('be_auth_login_url', $this->config('authLoginAction'));
            $controller->set('be_auth_logout_url', $this->config('authLogoutAction'));
        }
    }

    public function implementedEvents()
    {
        $events = parent::implementedEvents();

        //@TODO Implement implementedEvents (disabled)
        //$events['User.login'] = 'onUserLogin';

        return $events;
    }

    public function onUserLogin(Event $event)
    {
        //@TODO Implement event callback for 'User.login' (disabled)
        Log::debug('Backend:Event: User.login');
    }

    public function authConfig($key, $val = null, $merge = true)
    {
        $this->_controller->Auth->config($key, $val, $merge);
    }

    public function flashConfig($key, $val = null, $merge = true)
    {
        $this->_controller->Flash->config($key, $val, $merge);
    }
}

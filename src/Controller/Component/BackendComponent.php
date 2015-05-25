<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/19/15
 * Time: 5:12 AM
 */

namespace Backend\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Backend\Controller\BackendControllerInterface;

class BackendComponent extends Component
{
    public function initialize(array $config)
    {
    }

    public function beforeFilter(Event $event)
    {
        // only act on instances of BackendControllerInterface
        if ($event->subject() instanceof BackendControllerInterface) {



        }

    }

    public function startup(Event $event)
    {

    }


    public function beforeRender(\Cake\Event\Event $event)
    {
        if ($event->subject() instanceof BackendControllerInterface) {
            $controller = $event->subject();
            $controller->set('be_title', Configure::read('Backend.title'));
            $controller->set('be_dashboard_url', Configure::read('Backend.dashboardUrl'));
            $controller->set('be_auth_login_url', '/login');
            $controller->set('be_auth_logout_url', '/logout');
        }
    }

    public function implementedEvents()
    {
        $events = parent::implementedEvents();

        $events['User.Auth.afterLogin'] = 'afterLogin';
        $events['User.Auth.loginFailure'] = 'loginFailure';

        return $events;
    }

    public function afterLogin(Event $event)
    {
        //@TODO Implement me
        Log::debug('Backend:Event:afterLogin');
    }

    public function loginFailure(Event $event)
    {
        //@TODO Implement me
        Log::debug('Backend:Event:loginFailure');
    }
}

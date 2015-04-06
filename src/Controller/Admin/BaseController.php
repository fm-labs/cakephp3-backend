<?php
namespace Backend\Controller\Admin;

use Cake\Controller\Controller;
use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Component\PaginatorComponent;
use Cake\Controller\Component\FlashComponent;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Core\Exception\Exception;

/**
 * Class BackendAppController
 *
 * Use this class as a base controller for app controllers
 * which should run in backend context
 *
 * @package Backend\Controller
 *
 * @property AuthComponent $Auth
 * @property FlashComponent $Flash
 * @property PaginatorComponent $Paginator
 */
abstract class BaseController extends Controller
{
    public $layout = "Backend.admin";

    public $helpers = [
        'Html',
        'SemanticUi.Ui',
        'Form' => [
            'templates' => 'Backend.semantic-form-templates',
            'widgets' => ['button' => ['SemanticUi\View\Widget\ButtonWidget']]
        ],
        'Paginator' => [
            'templates' => 'Backend.semantic-paginator-templates'
        ]
    ];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        $this->loadComponent('Flash', [
            'className' => '\Backend\Controller\Component\FlashComponent',
            'key' => 'backend',
            'plugin' => 'Backend'
        ]);
    }

    public function startup()
    {
        /**
         * @TODO Replace with more sophisticated mechanism
         */
        if (!$this->Auth->user('is_backend_user')) {
            throw new Exception('Backend: Not Authorized');
        }
    }
}
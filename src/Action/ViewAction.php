<?php
declare(strict_types=1);

namespace Admin\Action;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Association;

class ViewAction extends BaseEntityAction implements EventListenerInterface
{
    protected $scope = ['table', 'form'];

    //protected $template = "view";

    protected $_defaultConfig = [
        'entity' => null,
        'entityOptions' => [],
        'modelClass' => null,
        'modelId' => null,
        'fields' => [],
        'fields.whitelist' => [],
        'fields.blacklist' => [],
        'related' => [],
        'viewOptions' => [],
        'actions' => [],
        'tabs' => [],
    ];

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __d('admin', 'View');
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return ['data-icon' => 'file-o'];
    }

    /**
     * @inheritDoc
     */
    public function _execute(Controller $controller)
    {
        $viewVars = $controller->viewBuilder()->getVars();
        if (!isset($viewVars['related'])) {
            $related = [];
            foreach ($this->model()->associations() as $assoc) {
                /** @var \Cake\ORM\Association $assoc */
                //debug($assoc->getAlias() . " : " . $assoc->type());
                switch ($assoc->type()) {
                    case Association::ONE_TO_MANY:
                    default:
                        $related[] = $assoc->getAlias();
                }
            }
            $controller->set('related', $related);
        }

        if ($this->_config['entity'] !== null) {
            $entity = $this->_entity = $this->_config['entity'];
            $this->_config['modelId'] = $entity->id;
        } else {
            // attempt to get model ID from request if not set
            if (!$this->_config['modelId']) {
                $this->_config['modelId'] = $controller->getRequest()->getParam('id') ?: null;
            }
            if (!$this->_config['modelId']) {
                $this->_config['modelId'] = $controller->getRequest()->getParam('pass')[0] ?? null;
            }
            if (!$this->_config['modelId']) {
                throw new BadRequestException('ViewAction: Model ID missing');
            }
            $entity = $this->entity();
        }

        $this->_config['viewOptions']['model'] = $this->_config['modelClass'];
        $this->_config['viewOptions']['title'] = $entity->get($this->model()->getDisplayField());
        $this->_config['viewOptions']['debug'] = Configure::read('debug');
        $this->_config['viewOptions']['fields'] = $this->_config['fields'];
        $this->_config['viewOptions']['whitelist'] = $this->_config['fields.whitelist'];
        $this->_config['viewOptions']['blacklist'] = $this->_config['fields.blacklist'];
        //$this->_config['viewOptions']['related'] = $this->_config['related'];
        $controller->set('viewOptions', $this->_config['viewOptions']);
        $controller->set('title', $entity->get($this->model()->getDisplayField()));
        $controller->set('modelClass', $this->_config['modelClass']);
        $controller->set('entity', $entity);
        $controller->set('actions', $this->_config['actions']);
        $controller->set('tabs', $this->_config['tabs']);
        //$controller->set('associations', $this->model()->associations());
        $controller->set('_serialize', ['entity']);
        //$controller->render();
    }

    public function beforeRender(Event $event)
    {
        $entity = $event->getSubject()->viewVars['entity'];
        $modelClass = $event->getSubject()->viewVars['modelClass'];

        $event->getSubject()->viewVars['tabs']['data'] = [
            'title' => __d('admin', 'Data'),
            'url' => ['plugin' => 'Admin', 'controller' => 'Entity', 'action' => 'view', $modelClass, $entity->id],
        ];
    }

    public function implementedEvents(): array
    {
        return [
            //'Controller.beforeRender' => 'beforeRender'
        ];
    }
}

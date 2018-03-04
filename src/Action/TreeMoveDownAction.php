<?php

namespace Backend\Action;

use Cake\Controller\Controller;

/**
 * Class TreeMoveDownAction
 *
 * @package Backend\Action
 */
class TreeMoveDownAction extends BaseEntityAction
{
    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return __d('backend','Move Down');
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return ['data-icon' => 'chevron-down'];
    }

    /**
     * {@inheritDoc}
     */
    public function _execute(Controller $controller)
    {
        if ($this->model()->hasBehavior('Tree')) {
            $entity = $this->entity();

            if ($this->model()->moveDown($entity)) {
                $controller->Flash->success(__d('backend', 'The {0} has been moved down.', $this->model()->alias()));
            } else {
                $controller->Flash->error(__d('backend', 'The {0} could not be moved. Please, try again.', $this->model()->alias()));
            }
        } else {
            $controller->Flash->error('Tree behavior not loaded for model ' . $this->model()->alias());
        }

        return $controller->redirect($controller->referer(['action' => 'index']));
    }
}

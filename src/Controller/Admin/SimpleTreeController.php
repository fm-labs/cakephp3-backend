<?php

namespace Backend\Controller\Admin;


use Cake\Log\Log;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

class SimpleTreeController extends AppController
{

    public function index()
    {
        $query = $this->request->query;
        if (!isset($query['model'])) {
            $this->Flash->error(__('No model selected'));
            return;
        }
        $modelName = $query['model'];
        unset($query['model']);

        try {
            $Model = $this->loadModel($modelName);
            if (!$Model->behaviors()->has('SimpleTree')) {
                $this->Flash->warning(__('Model {0} has no SimpleTree behavior attached', $modelName));
            }
        } catch (\Exception $ex) {
            $this->Flash->error(__('Failed to load model {0}', $modelName));
            return;
        }

        $this->set('modelName', $modelName);
        $this->set('data', $Model->find('sorted')->where($query)->all());
        $this->set('sortUrl', ['controller' => 'SimpleTree', 'action' => 'treeSort', 'model' => $modelName]);
    }


    public function treeSort()
    {
        $this->viewBuilder()->className('Json');

        $responseData = [];
        try {
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->data;

                $modelName = (isset($data['model'])) ? (string) $data['model'] : null;
                $id = (isset($data['id'])) ? (int) $data['id'] : null;
                $after = (isset($data['after'])) ? (int) $data['after'] : false;

                $responseData = [
                    'model' => $modelName,
                    'id' => $id,
                    'after' => $after,
                ];

                if (!$id) {
                    throw new BadRequestException('ID missing');
                }

                if (!$modelName || !$this->loadModel($modelName)) {
                    throw new NotFoundException("Table not found");
                }

                $model = $this->loadModel($modelName);
                if (!$model->behaviors()->has('SimpleTree')) {
                    throw new \Exception('Table has no Sortable behavior attached');
                }

                $node = $model->get($id);
                $responseData['oldPos'] = $node->pos;

                $node = $model->moveAfter($node, $after);
                $responseData['newPos'] = $node->pos;

                $responseData['success'] = (bool) $node;
            }
        } catch (\Exception $ex) {
            $responseData['success'] = false;
            $responseData['error'] = $ex->getMessage();
        }

        //$this->autoRender = false;
        //$this->response->body(json_encode($responseData));

        $this->set('result', $responseData);
        $this->set('_serialize', 'result');
    }

}
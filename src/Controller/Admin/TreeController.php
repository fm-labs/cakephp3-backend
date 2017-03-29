<?php

namespace Backend\Controller\Admin;


use Cake\Log\Log;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Routing\Router;

class TreeController extends AppController
{

    public function index()
    {

        $modelName = $this->request->query('model');
        if (!$modelName) {
            $this->Flash->error(__('No model selected'));
            return;
        }
        try {
            $Model = $this->loadModel($modelName);
            if (!$Model->behaviors()->has('Tree')) {
                $this->Flash->warning(__('Model {0} has no Tree behavior attached', $modelName));
            }
        } catch (\Exception $ex) {
            $this->Flash->error(__('Failed to load model {0}', $modelName));
            return;
        }

        $this->set('dataUrl', ['action' => 'jstreeData', 'model' => $modelName]);
        $this->set('sortUrl', ['action' => 'jstreeSort', 'model' => $modelName]);
    }

    public function jstreeData()
    {
        $this->viewBuilder()->className('Json');

        $modelName = $this->request->query('model');
        if (!$modelName) {
            throw new BadRequestException('Model name missing');
        }

        $tree = [];
        try {
            $Model = $this->loadModel($modelName);
            $Model->addBehavior('Backend.JsTree');
            $tree = $Model->find('jstree')->toArray();

        } catch (\Exception $ex) {
            Log::error('TreeController::treeData: ' . $ex->getMessage());
            //throw new InternalErrorException($ex->getMessage());
        }

        $this->set('tree', $tree);
        $this->set('_serialize', 'tree');
    }


    public function jstreeSort()
    {
        $this->viewBuilder()->className('Json');

        $modelName = $this->request->query('model');
        if (!$modelName) {
            throw new BadRequestException('Model name missing');
        }

        $request = $this->request->data + ['nodeId' => null, 'oldParentId' => null, 'oldPos' => null, 'newParentId' => null, 'newPos' => null];

        try {
            $Model = $this->loadModel($modelName);
            $Model->addBehavior('Backend.JsTree');

            $node = $Model->get($request['nodeId']);
            //$Model->behaviors()->Tree->config('scope', ['site_id' => $node->site_id]);
            $node = $Model->moveTo($node, $request['newParentId'], $request['newPos'], $request['oldPos']);

            $result = ['success' => __("Node has been moved")];
        } catch (\Exception $ex) {
            Log::error('TreeController::treeData: ' . $ex->getMessage());
            //throw new InternalErrorException($ex->getMessage());
            $result =  ['error' => $ex->getMessage()];
        }

        $this->set('request', $request);
        $this->set('node',$node);
        $this->set('result', $result);
        $this->set('_serialize', ['request', 'message', 'node']);
    }
}
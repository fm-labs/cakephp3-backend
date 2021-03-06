<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\Event\Event;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * @property \Cake\View\Helper\BreadcrumbsHelper $Breadcrumbs
 */
class BreadcrumbHelper extends Helper
{
    public $helpers = ['Breadcrumbs'];

    protected $_defaultConfig = [
        'class' => 'breadcrumb',
    ];

    /**
     * {@inheritDoc}
     */
    public function implementedEvents(): array
    {
        return [
            'View.beforeLayout' => ['callable' => 'beforeLayout'],
        ];
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function beforeLayout(Event $event)
    {
        $request = $this->getView()->getRequest();
        //@TODO _no_breadcrumbs and layout_no_breadcrumbs are deprecated. Set `breadcrumbs` to FALSE instead
        if (
            $event->getSubject()->get('breadcrumbs') === false
            || $event->getSubject()->get('_no_breadcrumbs') === true
            || $event->getSubject()->get('layout_no_breadcrumbs') === true
        ) {
            return;
        }

        if (empty($this->Breadcrumbs->getCrumbs()) && $event->getSubject()->get('breadcrumbs')) {
            foreach ((array)$event->getSubject()->get('breadcrumbs') as $breadcrumb) {
                $breadcrumb += ['title' => null, 'url' => null, 'options' => []];
                $this->Breadcrumbs->add($breadcrumb['title'], $breadcrumb['url'], $breadcrumb['options']);
            }
        } elseif (empty($this->Breadcrumbs->getCrumbs())) {
            if ($request->getParam('plugin') && $request->getParam('plugin') != $request->getParam('controller')) {
                $this->Breadcrumbs->add(Inflector::humanize($request->getParam('plugin')), [
                    'plugin' => $request->getParam('plugin'),
                    'controller' => $request->getParam('plugin'),
                    'action' => 'index',
                ]);
            }

            $this->Breadcrumbs->add(Inflector::humanize($request->getParam('controller')), [
                'plugin' => $request->getParam('plugin'),
                'controller' => $request->getParam('controller'),
                'action' => 'index',
            ]);

            if (isset($request->getParam('pass')[0])) {
                $this->Breadcrumbs->add(Inflector::humanize(Inflector::singularize($request->getParam('controller'))), [
                    'plugin' => $request->getParam('plugin'),
                    'controller' => $request->getParam('controller'),
                    'action' => 'view',
                    $request->getParam('pass')[0],
                ]);
            }

            //if ($request->getParam('action') != 'index') {
                $this->Breadcrumbs->add(Inflector::humanize($request->getParam('action')));
            //}
        }

        // inject admin dashboard url on first position
        $this->Breadcrumbs->prepend($this->_View->get('be_title'), $this->_View->get('be_dashboard_url'));

        $breadcrumbsHtml = $this->Breadcrumbs->render($this->getConfig());
        $event->getSubject()->assign('breadcrumbs', $breadcrumbsHtml);
    }
}

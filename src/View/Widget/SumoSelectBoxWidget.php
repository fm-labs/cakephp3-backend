<?php
declare(strict_types=1);

namespace Admin\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\View;
use Cake\View\Widget\SelectBoxWidget;

class SumoSelectBoxWidget extends SelectBoxWidget
{
    /**
     * Constructor.
     *
     * @param \Cake\View\StringTemplate $templates Templates list.
     * @param \Cake\View\View $view The view instance
     */
    public function __construct($templates, View $view)
    {
        parent::__construct($templates);

        $view->loadHelper('Admin.SumoSelect');
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $data, ContextInterface $context): string
    {
        // generate dom id
        if (!isset($data['id'])) {
            $data['id'] = uniqid('sumoselect');
        }

        $placeholderTxt = __d('admin', 'Select Here');
        if (isset($data['empty']) && is_string($data['empty'])) {
            $placeholderTxt = $data['empty'];
            //$data['empty'] = true;
        }

        $html = parent::render($data, $context);

        //@see https://github.com/HemantNegi/jquery.sumoselect
        $sumo = [
            'showTitle' => true, //(boolean) set to false to prevent title (tooltip) from appearing (deafult true)
            'up' => false, //(boolean) the direction in which to open the dropdown (default: false)
            'search' => true, // (boolean) To enable searching in sumoselect (default is false).
            'searchText' => __d('admin', 'Search ...'), // (string) placeholder for search input.
            'noMatch' => __d('admin', 'No matches for "{0}"'), // (string) placeholder to display if no itmes matches the search term
            'locale' => [__d('admin', 'OK'), __d('admin', 'Cancel'), __d('admin', 'Select All')], // (array) the text used in plugin
            'placeholder' => $placeholderTxt, //  The palceholder text to be displayed in the rendered select widget
            'captionFormat' => __d('admin', '{0} Selected'), // (string) Its the format in which you want to see the caption when more than csvDispCount items are selected.
            'captionFormatAllSelected' => __d('admin', '{0} all selected!'), // (string) Format of caption text when all elements are selected.
            //'csvDispCount' => 3,
            //'floatWidth' => 400,
        ];
        if (isset($data['sumo'])) {
            $sumo = array_merge($sumo, (array)$data['sumo']);
            unset($data['sumo']);
        }

        $js = sprintf("<script>$(document).ready(function() { $('#%s').SumoSelect(%s); });</script>", $data['id'], json_encode($sumo));

        return $html . $js;
    }

//    /**
//     * Ignore the 'empty' value
//     */
//    protected function _emptyValue($empty)
//    {
//        return [];
//    }
}

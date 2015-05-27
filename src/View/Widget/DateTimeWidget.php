<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 2/6/15
 * Time: 7:25 PM
 */

namespace Backend\View\Widget;

use Cake\View\Helper\FormHelper;
use Cake\View\Widget\DateTimeWidget as CakeDateTimeWidget;
use Cake\View\Form\ContextInterface;
use Cake\View\StringTemplate;
use DateTime;

class DateTimeWidget extends CakeDateTimeWidget
{
    public function __construct(StringTemplate $templates)
    {
        $this->_templates = $templates;
    }

    public function render(array $data, ContextInterface $context)
    {
        $_data = [
            'type' => 'text',
            'escape' => true,
            'class' => 'datepicker',
            'options' => $data['options'],
            'id' => $data['id'],
            'name' => $data['name'],
            'val' => $data['val']
        ];

        if ($_data['val']) {
            if (!is_object($_data['val'])) {
                $_data['val'] = new DateTime($_data['val']);
            }
            $_data['data-value'] = $_data['value'] = date("Y-m-d", $_data['val']->getTimestamp());
        }
        unset($_data['val']);
        unset($_data['options']);

        $this->_templates->add([
            'pickadate' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
        ]);


        return $this->_templates->format('input', [
            'name' => $_data['name'],
            'type' => $_data['type'],
            'attrs' => $this->_templates->formatAttributes(
                    $_data,
                    ['name', 'type']
                ),
        ]);
    }

}
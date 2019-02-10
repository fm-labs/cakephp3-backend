<?php

namespace Backend\View\Helper;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\View;

/**
 * Class DateRangePickerHelper
 * @package Backend\View\Helper
 *
 * @property HtmlHelper $Html
 * @property FormHelper $Form
 */
class DateRangePickerHelper extends Helper
{
    public $helpers = ['Html', 'Form'];

    public function initialize(array $config)
    {
        $this->Html->css('/backend/libs/daterangepicker/daterangepicker.css', ['block' => true]);
        $this->Html->script('/backend/libs/daterangepicker/daterangepicker.js', ['block' => true]);

        $this->Form->addWidget('daterange', ['Backend\View\Widget\DateRangePickerWidget', '_view']);
    }
}

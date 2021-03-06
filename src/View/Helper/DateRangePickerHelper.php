<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;

/**
 * Class DateRangePickerHelper
 * @package Admin\View\Helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 */
class DateRangePickerHelper extends Helper
{
    public $helpers = ['Html', 'Form'];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        $this->Html->css('/admin/libs/daterangepicker/daterangepicker.css', ['block' => true]);
        $this->Html->script('/admin/libs/daterangepicker/daterangepicker.js', ['block' => true]);

        $this->Form->addWidget('daterange', ['Admin\View\Widget\DateRangePickerWidget', '_view']);
    }
}

<?php

namespace Backend\View\Helper;

use Bootstrap\View\Helper\UiHelper;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\NumberHelper;
use Cake\View\Helper\TimeHelper;
use Cake\View\View;

/**
 * Class FormatHelper
 * @package Backend\View\Helper
 *
 * @property HtmlHelper $Html
 * @property NumberHelper $Number
 * @property TimeHelper $Time
 * @property UiHelper $Ui
 *
 * @TODO Remove hard dependency on Bootstrap plugin. Use mixin solution
 */
class FormatterHelper extends Helper
{
    /**
     * @var array
     */
    static protected $_formatters = [];

    /**
     * @param string $formatterName Formatter alias
     * @param callable $formatter Formatter callback
     * @return void
     */
    public static function register($formatterName, callable $formatter)
    {
        self::$_formatters[$formatterName] = $formatter;
    }

    /**
     * @var array
     */
    public $helpers = ['Html', 'Number', 'Time', 'Bootstrap.Ui'];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        // escape
        self::register('escape', function ($val, $extra, $params) {
            return h($val);
        });

        // boolean
        self::register('boolean', function ($val, $extra, $params) {
            return $this->Ui->statusLabel($val);
        });

        // date
        self::register('date', function ($val, $extra, $params) {
            //$format = null;
            //$format = 'MM.dd.yyyy';
            $format = 'yyyy-MM-dd';
            if (isset($params['format'])) {
                $format = $params['format'];
            }
            if ($val instanceof \DateTimeInterface) {
                return $this->Time->format($val, $format);
            }

            return (string)$val;
        });
        self::register('datetime', function ($val, $extra, $params) {
            $format = null;
            //$format = 'yyyy-MM-dd HH:mm:ss';
            //$format = 'MM.dd.yyyy HH:mm:ss';
            if (isset($params['format'])) {
                $format = $params['format'];
            }
            if ($val instanceof \DateTimeInterface) {
                return $this->Time->format($val, $format);
            }

            return (string)$val;
        });

        // link
        self::register('link', function ($val, $extra, $params) {
            $title = $url = $val;
            if (isset($params['url'])) {
                $url = $params['url'];
                unset($params['url']);
            }

            $url = $this->Html->Url->build($url, true);
            if (isset($params['title'])) {
                $title = $params['title'];
            }

            return $this->Html->link($title, $url, $params);
        });

        // number
        self::register('number', function ($val, $extra, $params) {
            return $this->Number->format($val, $params);
        });

        // truncate
        // @todo make span html wrapper optional
        self::register('truncate', function ($val, $extra, $params) {
            $length = (is_int($params)) ? $params : 300;
            $str = Text::truncate((string)$val, $length);

            return '<span title="' . (string)$val . '">' . $str . '</span>';
        });

        // currency
        self::register('currency', function ($val, $extra, $params) {

            $currency = (isset($params['currency']))
                ? $params['currency'] : Configure::read('Shop.defaultCurrency'); // @TODO Use App-level default currency
            $currency = (!$currency && $extra && isset($extra, $params['currency_field']))
                ? Hash::get($extra, $params['currency_field']) : $currency;

            //$params['useIntlCode'] = true;
            $params['fractionPosition'] = 'before';
            $params['fractionSymbol'] = $currency;

            return $this->Number->currency($val, $currency, $params);
        });

        // email
        self::register('email', function ($val, $extra, $params) {
            return ($val) ? $this->Html->link($val, 'mailto:' . $val) : null;
        });

        // array
        self::register('array', function ($val, $extra, $params, $view) {
            return $view->element('Backend.array_to_list', ['array' => $val]);
        });
        self::register('json', function ($val, $extra, $params, $view) {
            if (is_string($val)) {
                try {
                    $val = json_decode($val, true);

                    if (json_last_error()) {
                        throw new \RuntimeException(json_last_error_msg());
                    }
                } catch (\Exception $ex) {
                    return "JSON ERROR: " . $ex->getMessage();
                }
            }

            return $view->element('Backend.array_to_list', ['array' => $val]);
        });

        // NULL
        self::register('null', function ($val, $extra, $params) {
            return '-';
        });

        // object
        self::register('object', function ($val, $extra, $params) {

            if ($val instanceof EntityInterface) {
                return '[entity:' . get_class($val) . ']';
            }

            if ($val instanceof ResultSet) {
                return '[resultset]';
            }

            if (method_exists($val, '__toString')) {
                return h((string)$val);
            }

            return '[object:' . get_class($val) . ']';

            //return '<pre>' . print_r($val, true) . '</pre>';
        });

        // html
        self::register('html', function ($val, $extra, $params) {
            //@todo sanitation
            return sprintf('<div class="html">' . $val . '</div>', $val);
        });

        self::register('related', function ($val, $extra, $params) {
            if (!$val) {
                return $val;
            }

            $params = (is_string($params)) ? ['field' => $params] : $params;
            $params = array_merge(['field' => 'name'], $params);

            if ($val instanceof EntityInterface) {
                return $val->get($params['field']);
            } elseif (is_array($val)) {
                return (isset($val[$params['field']])) ? $val[$params['field']] : null;
            } elseif (is_object($val)) {
                return (isset($val->{$params['field']})) ? $val->{$params['field']} : null;
            }

            return (string)$val;
        });
    }

    /**
     * @return array
     */
    public function getFormatters()
    {
        return array_keys(self::$_formatters);
    }

    /**
     * @param mixed $value Value to format
     * @param null|string|callable $formatter Formatter to use
     * @param array $formatterArgs Formatter callback arguments
     * @param array $extra Extra data passed to the formatter callback
     * @return mixed
     */
    public function format($value, $formatter = null, $formatterArgs = [], $extra = [])
    {
        if ($formatter === false) {
            return $value;
        }

        if ($formatter === null || $formatter === 'default') {
            $formatter = $this->_detectFormatter($value);
        }

        if (is_array($formatter)) {
            // ['formatter-name' => 'formatter-data']
            if (count($formatter) === 1) {
                $formatterArgs = current($formatter);
                $formatter = key($formatter);
            } elseif (count($formatter) === 2) {
                list($formatter, $formatterArgs) = $formatter;
            } else {
                debug("Unsupported formatter array format");
                $formatter = null;
            }
        }

        switch ($formatter) {
            case "integer":
            case "float":
            case "double":
            case "decimal":
                $formatter = 'number';
                break;

            case "unknown type":
            case "resource":
                return "[" . h($formatter) . "]";

            /*
            //case "datetime":
            //case "date":
            case "uuid":
            case "text":
            case "string":
            case "null":
            case "NULL":
            case null:
                $formatter = 'escape';
                break;
            */
            default:
                break;
        }

        if (is_string($formatter)) {
            if (!isset(self::$_formatters[$formatter])) {
                //debug("Formatter $formatter not found for dataType $dataType");
                $formatter = null;
            } else {
                $formatter = self::$_formatters[$formatter];
            }
        }

        if (!is_callable($formatter)) {
            //@TODO remove debug code
            if ($formatter) {
                debug("Uncallable formatter");
                var_dump($formatter);
            }

            return h($value);
        }

        return call_user_func_array($formatter, [$value, $extra, $formatterArgs, $this->_View]);
    }

    /**
     * Autodetect formatter for value
     *
     * @param mixed $value The value to format
     * @return string Formatter alias
     */
    protected function _detectFormatter($value)
    {
        // detect date types
        if (is_object($value) && $value instanceof \DateTimeInterface) {
            $formatter = 'datetime';
        } else {
            // Fallback to default formatter based on values datatype
            $formatter = gettype($value);
        }

        return $formatter;
    }
}

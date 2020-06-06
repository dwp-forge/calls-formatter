<?php

/**
 * DokuWiki calls formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('basic.php');
require_once('plugin_qna.php');
require_once('plugin_refnotes.php');
require_once('style.php');

class DokuwikiCallsFormatter {

    private static $formatterClasses = array();
    private static $processorClasses = array();

    private $calls;
    private $style;
    private $callFormatters;
    private $callProcessors;

    /**
     * Registers a call formatter for a given mode.
     *
     * @param $mode Mode name.
     * @param $formatterClass Formatter class name.
     */
    public static function registerFormatter($mode, $formatterClass) {
        self::$formatterClasses[$mode] = $formatterClass;
    }

    /**
     * Registers a call processor for a given mode.
     *
     * @param $mode Mode name.
     * @param $processorClass Processor class name.
     */
    public static function registerProcessor($mode, $processorClass) {
        self::$processorClasses[$mode] = $processorClass;
    }

    /**
     * Constructor
     *
     * @param $calls Calls array.
     */
    public function __construct($calls) {
        $this->calls = $calls;
        $this->style = new DokuwikiCallsStyle($calls);
        $this->callFormatters = new DokuwikiModeHandlerFactory(self::$formatterClasses, 'DokuwikiGenericCallFormatter', $this->style);
        $this->callProcessors = new DokuwikiModeHandlerFactory(self::$processorClasses, 'DokuwikiGenericCallProcessor', $this->style);
    }

    /**
     * Sets formatting style.
     *
     * @param $style Style specification. Can be either an array or list of style constants.
     */
    public function setStyle($style) {
        if (!is_array($style)) {
            $style = func_get_args();
        }

        $this->style->set($style);
    }

    /**
     * Returns formatted calls list.
     *
     * @param $style Optional style specification. Can be either an array or list of style constants.
     */
    public function format($style = "none") {
        if ($style != "none") {
            if (!is_array($style)) {
                $style = func_get_args();
            }

            $this->style->set($style);
        }

        $output = $this->style->has(DokuwikiCallsStyle::START_WITH_NEW_LINE) ? "\n" : '';
        $count = count($this->calls);

        for ($index = 0; $index < $count; $index += $progress) {
            list($call, $progress) = $this->getCall($index);

            if (!$this->style->getHideIndex()) {
                $output .= $this->formatIndex($index, $call);
            }

            $output .= $this->formatCall($call);
        }

        return $output;
    }

    /**
     *
     */
    private function getCall($index) {
        return $this->callProcessors->get($this->calls[$index])->getCall($this->calls, $index);
    }

    /**
     *
     */
    private function formatIndex($index, $call) {
        if ($this->style->getOffsetInIndex()) {
            return sprintf($this->style->getIndexFormat(), $index, $call[2]);
        }

        return sprintf($this->style->getIndexFormat(), $index);
    }

    /**
     *
     */
    private function formatCall($call) {
        return $this->callFormatters->get($call)->format($call);
    }
}

function format_calls($calls) {
    $formatter = new DokuwikiCallsFormatter($calls);

    return $formatter->format(
            DokuwikiCallsStyle::START_WITH_NEW_LINE,
            DokuwikiCallsStyle::COLLAPSE_DATA_FORMATTING,
            DokuwikiCallsStyle::COLLAPSE_DATA_PARAGRAPHS,
            DokuwikiCallsStyle::COMPACT_DATA);
}

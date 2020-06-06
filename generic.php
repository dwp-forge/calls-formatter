<?php

/**
 * DokuWiki generic call data formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('style.php');

class DokuwikiGenericCallProcessor {

    protected $style;

    /**
     * Constructor.
     *
     * @param $style Style object.
     */
    public function __construct($style) {
        $this->style = $style;
    }

    /**
     * Returns call at given index.
     *
     * Allows to customize processing (merging) of multiple calls. The number of
     * handled calls should be returned as the second return value.
     *
     * @param $calls Calls array.
     * @param $index Index of the call.
     * @return Call data and number of processed calls.
     */
    public function getCall($calls, $index) {
        return array($calls[$index], 1);
    }
}

class DokuwikiGenericCallFormatter {

    protected $style;

    /**
     * Constructor.
     *
     * @param $style Style object.
     */
    public function __construct($style) {
        $this->style = $style;
    }

    /**
     * Returns formatted call.
     *
     * @param $call Call data array.
     */
    public function format($call) {
        $output = $this->formatCall($call);

        if (!$this->style->getInlineData()) {
            $output .= $this->formatCallEol($call);
        }

        if (!$this->style->getHideData() && !empty($call[1])) {
            $output .= $this->formatCallData($call);
        }

        if ($this->style->getInlineData()) {
            $output .= $this->formatCallEol($call);
        }

        return $output;
    }

    /**
     *
     */
    protected function formatCall($call) {
        return $call[0] == 'plugin' ? $call[1][0] : $call[0];
    }

    /**
     *
     */
    protected function formatCallEol($call) {
        return $this->style->getOffsetAtEol() ? ' @ ' . $call[2] . "\n" : "\n";
    }

    /**
     *
     */
    protected function formatCallData($call) {
        if ($this->style->getHideUnknownCallData()) {
            return '';
        }

        $data = '';

        if ($this->style->getInlineData()) {
            $data .= ' {';
            $data .= $this->formatArrayCompact($call[1]);
            $data .= '}';
        }
        elseif ($this->style->getCompactData()) {
            $data .= $this->style->getDataIndent();
            $data .= $this->formatArrayCompact($call[1]);
            $data .= "\n";
        }
        else {
            $data .= $this->formatArray($call[1], $this->style->getDataIndent());
        }

        return $data;
    }

    /**
     *
     */
    protected function formatValueCompact($value) {
        if (is_string($value)) {
            return $this->formatString($value, DokuwikiCallsStyle::MAX_COMPACT_STRING_LENGTH);
        }
        else if (is_array($value)) {
            return '{' . $this->formatArrayCompact($value) . '}';
        }

        return strval($value);
    }

    /**
     *
     */
    protected function formatArrayCompact($array) {
        $output = '';

        foreach ($array as $key => $value) {
            if (!empty($output)) {
                $output .= ', ';
            }

            $output .= "[$key] => ";
            $output .= $this->formatValueCompact($value);
        }

        return $output;
    }

    /**
     *
     */
    protected function formatValue($value, $indent) {
        if (is_string($value)) {
            return $this->formatString($value, DokuwikiCallsStyle::MAX_STRING_LENGTH);
        }
        else if (is_array($value)) {
            return "Array:\n" . rtrim($this->formatArray($value, $indent . '    '));
        }

        return strval($value);
    }

    /**
     *
     */
    protected function formatString($string, $maxLength) {
        $output = trim(str_replace("\n", '\n', $string));

        if (strlen($output) > $maxLength) {
            $output = substr($output, 0, $maxLength - 3) . '...';
        }

        return '"' . $output . '"';
    }

    /**
     *
     */
    protected function formatArray($array, $indent) {
        $output = '';

        foreach ($array as $key => $value) {
            $output .= $indent . "[$key] => ";
            $output .= $this->formatValue($value, $indent) . "\n";
        }

        return $output;
    }
}

class DokuwikiModeHandlerFactory {

    private $handlers;
    private $genericHandler;

    /**
     * Constructor.
     *
     * @param $classes Map from mode name to handler class name.
     * @param $genericHandlerClass Class name of generic handler.
     * @param $style Style object.
     */
    public function __construct($classes, $genericClass, $style) {
        $handlers = array();
        $this->handlers = array();

        foreach ($classes as $mode => $class) {
            if (!array_key_exists($class, $handlers)) {
                $handlers[$class] = new $class($style);
            }

            $this->handlers[$mode] = $handlers[$class];
        }

        $this->genericHandler = new $genericClass($style);
    }

    /**
     * Returns a handler for a mode.
     *
     * @param $call Call data.
     * @return Mode-specific handler or a generic one if there none registered for the mode.
     */
    public function get($call) {
        $mode = $call[0];

        if ($mode == 'plugin') {
            $mode .= ':' . $call[1][0];
        }

        if (array_key_exists($mode, $this->handlers)) {
            return $this->handlers[$mode];
        }

        return $this->genericHandler;
    }
}

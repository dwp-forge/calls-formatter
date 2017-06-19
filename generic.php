<?php

/**
 * DokuWiki generic call data formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('style.php');

class DokuwikiGenericCallFormatter {

    protected $style;

    /**
     * Constructor
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
            foreach ($call[1] as $index => $value) {
                $data .= sprintf($this->style->getDataIndexFormat(), $index);
                $data .= str_replace("\n", "\n" . $this->style->getDataIndent(), rtrim(print_r($value, true)));
                $data .= "\n";
            }
        }

        return $data;
    }

    /**
     *
     */
    protected function formatValueCompact($value) {
        if (is_string($value)) {
            return $this->formatStringCompact($value);
        }
        else if (is_array($value)) {
            return '{' . $this->formatArrayCompact($value) . '}';
        }

        return strval($value);
    }

    /**
     *
     */
    protected function formatStringCompact($string, $maxLength = DokuwikiCallsStyle::MAX_COMPACT_STRING_LENGTH) {
        $output = trim(str_replace("\n", '\n', $string));

        if (strlen($output) > $maxLength) {
            $output = substr($output, 0, $maxLength - 3) . '...';
        }

        return '"' . $output . '"';
    }

    /**
     *
     */
    protected function formatArrayCompact($array) {
        $output = '';
        $first = true;

        foreach ($array as $key => $value) {
            if ($first) {
                $first = false;
            }
            else {
                $output .= ', ';
            }

            $output .= "[$key] => ";
            $output .= $this->formatValueCompact($value);
        }

        return $output;
    }
}

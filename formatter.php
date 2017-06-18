<?php

/**
 * DokuWiki calls formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('style.php');

 class DokuwikiCallsFormatter {

    private $calls;
    private $count;
    private $style;

    /**
     * Constructor
     */
    public function __construct($calls) {
        $this->calls = $calls;
        $this->count = count($this->calls);
        $this->style = new DokuwikiCallsStyle($this->count);
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

        for ($index = 0; $index < $this->count; $index += isset($call[3]) ? $call[3] : 1) {
            $call = $this->getCall($index);

            $output .= $this->formatIndex($index, $call);
            $output .= $this->formatCall($call);
            $output .= $this->formatCallEol($call);
            $output .= $this->formatCallData($call);
        }

        return $output;
    }

    /**
     *
     */
    private function getCall($index) {
        $call = $this->calls[$index];

        if ($call[0] == 'p_open' && $this->style->getCollapseDataParagraphs() && $index + 2 < $this->count &&
                $this->calls[$index + 1][0] == 'cdata' && $this->calls[$index + 2][0] == 'p_close') {
            $call[0] = 'p_cdata';
            $call[1] = $this->calls[$index + 1][1];
            $call[3] = 3;
        }

        return $call;
    }

    /**
     *
     */
    private function formatIndex($index, $call) {
        if ($this->style->getHideIndex()) {
            return '';
        }

        if ($this->style->getOffsetInIndex()) {
            return sprintf($this->style->getIndexFormat(), $index, $call[2]);
        }

        return sprintf($this->style->getIndexFormat(), $index);
    }

     /**
     *
     */
    private function formatCall($call) {
        return $call[0] == 'plugin' ? $call[1][0] : $call[0];
    }

    /**
     *
     */
    private function formatCallEol($call) {
        return $this->style->getOffsetAtEol() ? ' @ ' . $call[2] . "\n" : "\n";
    }

    /**
     *
     */
    private function formatCallData($call) {
        if ($this->style->getHideData() || empty($call[1])) {
            return '';
        }

        $data = '';

        if ($this->style->getCompactData()) {
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
    private function formatValueCompact($value) {
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
    private function formatStringCompact($string) {
        $output = trim(str_replace("\n", '\n', $string));

        if (strlen($output) > DokuwikiCallsStyle::MAX_COMPACT_STRING_LENGTH) {
            $output = substr($output, 0, DokuwikiCallsStyle::MAX_COMPACT_STRING_LENGTH - 3) . '...';
        }

        return '"' . $output . '"';
    }

    /**
     *
     */
    private function formatArrayCompact($array) {
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

function format_calls($calls) {
    $formatter = new DokuwikiCallsFormatter($calls);

    return $formatter->format(
        DokuwikiCallsStyle::START_WITH_NEW_LINE,
        DokuwikiCallsStyle::COLLAPSE_DATA_PARAGRAPHS,
        DokuwikiCallsStyle::COMPACT_DATA
    );
}

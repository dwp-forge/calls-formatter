<?php

/**
 * DokuWiki calls formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('generic.php');
require_once('style.php');

class DokuwikiCallsFormatter {

    private $calls;
    private $count;
    private $style;
    private $genericCallFormatter;

    /**
     * Constructor
     */
    public function __construct($calls) {
        $this->calls = $calls;
        $this->count = count($this->calls);
        $this->style = new DokuwikiCallsStyle($this->count);
        $this->genericCallFormatter = new DokuwikiGenericCallFormatter($this->style);
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
        if ($this->style->getOffsetInIndex()) {
            return sprintf($this->style->getIndexFormat(), $index, $call[2]);
        }

        return sprintf($this->style->getIndexFormat(), $index);
    }

    /**
     *
     */
    private function formatCall($call) {
        return $this->genericCallFormatter->format($call);
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

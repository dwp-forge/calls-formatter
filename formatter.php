<?php

/**
 * DokuWiki calls formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

 class DokuwikiCallsFormatter {

    const COLLAPSE_DATA_PARAGRAPHS = 0x98f1f81d;
    const OFFSET_AT_EOL = 0x52de62ad;
    const OFFSET_IN_INDEX = 0x7266e39e;
    const START_WITH_NEW_LINE = 0x2e0a6907;

    private $calls;
    private $count;
    private $style;
    private $indexWidth;
    private $indexFormat;
    private $collapseDataParagraphs;
    private $offsetAtEol;
    private $offsetInIndex;

    /**
     * Constructor
     */
    public function __construct($calls) {
        $this->calls = $calls;
        $this->count = count($this->calls);
        $this->indexWidth = strlen(strval($this->count));

        $this->setStyle(array());
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

        $this->style = $style;
        $this->collapseDataParagraphs = $this->hasStyle(self::COLLAPSE_DATA_PARAGRAPHS);
        $this->offsetAtEol = $this->hasStyle(self::OFFSET_AT_EOL);
        $this->offsetInIndex = $this->hasStyle(self::OFFSET_IN_INDEX);

        $offsetWidth = strlen(strval($this->calls[count($this->calls) - 1][2]));

        if ($this->offsetInIndex) {
            $this->indexFormat = '[%' . $this->indexWidth . 'd | %' . $offsetWidth . 'd] ';
        }
        else {
            $this->indexFormat = '[%' . $this->indexWidth . 'd] ';
        }
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

            $this->setStyle($style);
        }

        $output = $this->hasStyle(self::START_WITH_NEW_LINE) ? "\n" : '';

        for ($index = 0; $index < $this->count; $index += isset($call[3]) ? $call[3] : 1) {
            $call = $this->getCall($index);

            $output .= $this->formatIndex($index, $call);
            $output .= $call[0];
            $output .= $this->formatCallEol($call);
        }

        return $output;
    }

    /**
     *
     */
    private function hasStyle($style) {
        return in_array($style, $this->style);
    }

    /**
     *
     */
    private function getCall($index) {
        $call = $this->calls[$index];

        if ($call[0] == 'p_open' && $this->collapseDataParagraphs && $index + 2 < $this->count &&
                $this->calls[$index + 1][0] == 'cdata' && $this->calls[$index + 2][0] == 'p_close') {
            $call[0] = 'p_cdata';
            $call[3] = 3;
        }

        return $call;
    }

    /**
     *
     */
    private function formatIndex($index, $call) {
        if ($this->offsetInIndex) {
            return sprintf($this->indexFormat, $index, $call[2]);
        }
        else {
            return sprintf($this->indexFormat, $index);
        }
    }

    /**
     *
     */
    private function formatCallEol($call) {
        return $this->offsetAtEol ? ' @ ' . $call[2] . "\n" : "\n";
    }
}

function format_calls($calls) {
    $formatter = new DokuwikiCallsFormatter($calls);

    return $formatter->format(
        DokuwikiCallsFormatter::START_WITH_NEW_LINE,
        DokuwikiCallsFormatter::COLLAPSE_DATA_PARAGRAPHS,
        DokuwikiCallsFormatter::OFFSET_AT_EOL
    );
}

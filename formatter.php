<?php

/**
 * DokuWiki calls formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

 class DokuwikiCallsFormatter {

    const COLLAPSE_DATA_PARAGRAPHS = 0x98f1f81d;
    const HIDE_DATA = 0x5a213f13;
    const HIDE_INDEX = 0xebefe439;
    const OFFSET_AT_EOL = 0x52de62ad;
    const OFFSET_IN_INDEX = 0x7266e39e;
    const START_WITH_NEW_LINE = 0x2e0a6907;

    private $calls;
    private $count;
    private $style;
    private $indexWidth;
    private $indexFormat;
    private $dataIndent;
    private $dataIndexFormat;
    private $collapseDataParagraphs;
    private $hideData;
    private $hideIndex;
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
        $this->hideData = $this->hasStyle(self::HIDE_DATA);
        $this->hideIndex = $this->hasStyle(self::HIDE_INDEX);
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

        if ($this->hideIndex) {
            $dataIndent = 4;
        }
        else {
            $dataIndent = $this->indexWidth + ($this->offsetInIndex ? $offsetWidth + 6 : 3);
        }

        $this->dataIndent = str_pad('', $dataIndent);
        $this->dataIndexFormat = $this->dataIndent . '[%d] => ';
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
            $output .= $this->formatCall($call);
            $output .= $this->formatCallEol($call);
            $output .= $this->formatCallData($call);
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
            $call[1] = $this->calls[$index + 1][1];
            $call[3] = 3;
        }

        return $call;
    }

    /**
     *
     */
    private function formatIndex($index, $call) {
        if ($this->hideIndex) {
            return '';
        }

        if ($this->offsetInIndex) {
            return sprintf($this->indexFormat, $index, $call[2]);
        }

        return sprintf($this->indexFormat, $index);
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
        return $this->offsetAtEol ? ' @ ' . $call[2] . "\n" : "\n";
    }

    /**
     *
     */
    private function formatCallData($call) {
        if ($this->hideData || empty($call[1])) {
            return '';
        }

        $data = '';

        foreach ($call[1] as $index => $value) {
            $data .= sprintf($this->dataIndexFormat, $index);
            $data .= str_replace("\n", "\n" . $this->dataIndent, rtrim(print_r($value, true)));
            $data .= "\n";
        }

        return $data;
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

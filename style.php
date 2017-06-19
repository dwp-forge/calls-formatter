<?php

/**
 * DokuWiki calls formatter styling options
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

class DokuwikiCallsStyle {

    const COLLAPSE_DATA_PARAGRAPHS = 0x98f1f81d;
    const COMPACT_DATA = 0x6724fd87;
    const INLINE_DATA = 0x0345846a;
    const HIDE_DATA = 0x5a213f13;
    const HIDE_INDEX = 0xebefe439;
    const HIDE_UNKNOWN_CALL_DATA = 0x3604171e;
    const OFFSET_AT_EOL = 0x52de62ad;
    const OFFSET_IN_INDEX = 0x7266e39e;
    const START_WITH_NEW_LINE = 0x2e0a6907;

    const MAX_COMPACT_STRING_LENGTH = 30;

    private $style;

    private $indexWidth;
    private $indexFormat;
    private $dataIndent;
    private $dataIndexFormat;
    private $dataSeparator;

    private $collapseDataParagraphs;
    private $compactData;
    private $inlineData;
    private $hideData;
    private $hideIndex;
    private $hideUnknownCallData;
    private $offsetAtEol;
    private $offsetInIndex;

    /**
     * Constructor
     */
    public function __construct($callsCount) {
        $this->indexWidth = strlen(strval($callsCount));

        $this->set(array());
    }

    /**
     * Sets formatting style.
     *
     * @param $style Style specification. Can be either an array or list of style constants.
     */
    public function set($style) {
        if (!is_array($style)) {
            $style = func_get_args();
        }

        $this->style = $style;
        $this->collapseDataParagraphs = $this->has(self::COLLAPSE_DATA_PARAGRAPHS);
        $this->compactData = $this->has(self::COMPACT_DATA) || $this->has(self::INLINE_DATA);
        $this->inlineData = $this->has(self::INLINE_DATA);
        $this->hideData = $this->has(self::HIDE_DATA);
        $this->hideIndex = $this->has(self::HIDE_INDEX);
        $this->hideUnknownCallData = $this->has(self::HIDE_UNKNOWN_CALL_DATA);
        $this->offsetAtEol = $this->has(self::OFFSET_AT_EOL);
        $this->offsetInIndex = $this->has(self::OFFSET_IN_INDEX);

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
        $this->dataSeparator = $this->compactData ? ', ' : "\n" . $this->dataIndent;
    }

    /**
     *
     */
    public function has($style) {
        return in_array($style, $this->style);
    }

    /**
     *
     */
    public function getIndexWidth() {
        return $this->indexWidth;
    }

    /**
     *
     */
    public function getIndexFormat() {
        return $this->indexFormat;
    }

    /**
     *
     */
    public function getDataIndent() {
        return $this->dataIndent;
    }

    /**
     *
     */
    public function getDataIndexFormat() {
        return $this->dataIndexFormat;
    }

    /**
     *
     */
    public function getDataSeparator() {
        return $this->dataSeparator;
    }

    /**
     *
     */
    public function getCollapseDataParagraphs() {
        return $this->collapseDataParagraphs;
    }

    /**
     *
     */
    public function getCompactData() {
        return $this->compactData;
    }

    /**
     *
     */
    public function getInlineData() {
        return $this->inlineData;
    }

    /**
     *
     */
    public function getHideData() {
        return $this->hideData;
    }

    /**
     *
     */
    public function getHideIndex() {
        return $this->hideIndex;
    }

    /**
     *
     */
    public function getHideUnknownCallData() {
        return $this->hideUnknownCallData;
    }

    /**
     *
     */
    public function getOffsetAtEol() {
        return $this->offsetAtEol;
    }

    /**
     *
     */
    public function getOffsetInIndex() {
        return $this->offsetInIndex;
    }
}

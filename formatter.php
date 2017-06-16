<?php

/**
 * DokuWiki calls formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

 class DokuwikiCallsFormatter {

    const OFFSET_AT_EOL = 1;
    const OFFSET_IN_INDEX = 2;
    const START_WITH_NEW_LINE = 3;

    private $calls;
    private $style;
    private $indexWidth;
    private $indexFormat;
    private $offsetAtEol;
    private $offsetInIndex;

    /**
     * Constructor
     */
    public function __construct($calls) {
        $this->calls = $calls;
        $this->indexWidth = strlen(strval(count($this->calls)));

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

        foreach ($this->calls as $index => $call) {
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
        DokuwikiCallsFormatter::OFFSET_AT_EOL
    );
}

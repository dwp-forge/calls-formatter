<?php

/**
 * DokuWiki calls formatter
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

 class DokuwikiCallsFormatter {

    private $calls;
    private $indexWidth;
    private $indexFormat;

    /**
     * Constructor
     */
    public function __construct($calls) {
        $this->calls = $calls;
        $this->indexWidth = strlen(strval(count($this->calls)));
        $this->indexFormat = '[%' . $this->indexWidth . 'd] ';
    }

    /**
     * Returns formatted calls list.
     */
    public function format() {
        $output = "";

        foreach ($this->calls as $index => $call) {
            $output .= sprintf($this->indexFormat, $index);
            $output .= $call[0];
            $output .= ' @ ' . $call[2];
            $output .= "\n";
        }

        return $output;
    }
}

function format_calls($calls) {
    $formatter = new DokuwikiCallsFormatter($calls);

    return $formatter->format();
}

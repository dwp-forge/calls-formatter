<?php

/**
 * DokuWiki basic call formatters
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('generic.php');

class DokuwikiCdataCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    protected function formatCall($call) {
        $output = parent::formatCall($call);

        if ($this->style->getCompactData()) {
            $output .= ' ' . $this->formatStringCompact($call[1][0], 50);
        }

        return $output;
    }

    /**
     *
     */
    protected function formatCallData($call) {
        if ($this->style->getCompactData()) {
            return '';
        }

        return $this->style->getDataIndent() . $this->formatStringCompact($call[1][0], 80) . "\n";
    }
}

class DokuwikiHeaderCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    protected function formatCall($call) {
        $output = parent::formatCall($call);

        if ($this->style->getCompactData()) {
            $output .= ' ' . $this->formatData($call);
        }

        return $output;
    }

    /**
     *
     */
    protected function formatCallData($call) {
        if ($this->style->getCompactData()) {
            return '';
        }

        return $this->style->getDataIndent() . $this->formatData($call) . "\n";
    }

    /**
     *
     */
    private function formatData($call) {
        return $this->formatStringCompact($call[1][0]) . ', level = ' . $call[1][1];
    }
}

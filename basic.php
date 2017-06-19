<?php

/**
 * DokuWiki basic call formatters
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('generic.php');
require_once('formatter.php');

class DokuwikiCdataCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    protected function formatCallData($call) {
        if ($this->style->getInlineData()) {
            return ' ' . $this->formatStringCompact($call[1][0], 50);
        }

        return $this->style->getDataIndent() . $this->formatStringCompact($call[1][0], 80) . "\n";
    }
}

class DokuwikiHeaderCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    protected function formatCallData($call) {
        if ($this->style->getInlineData()) {
            return ' ' . $this->formatData($call);
        }

        return $this->style->getDataIndent() . $this->formatData($call) . "\n";
    }

    /**
     *
     */
    private function formatData($call) {
        $maxTitleLength = $this->style->getInlineData() ? 40 : 80;

        return $this->formatStringCompact($call[1][0], $maxTitleLength) . $this->style->getDataSeparator() .
            'level = ' . $call[1][1];
    }
}

DokuwikiCallsFormatter::registerFormatter('cdata', 'DokuwikiCdataCallFormatter');
DokuwikiCallsFormatter::registerFormatter('header', 'DokuwikiHeaderCallFormatter');
DokuwikiCallsFormatter::registerFormatter('p_cdata', 'DokuwikiCdataCallFormatter');

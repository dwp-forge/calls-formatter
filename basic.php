<?php

/**
 * DokuWiki basic call formatters
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('generic.php');
require_once('formatter.php');

class DokuwikiPopenCallProcessor extends DokuwikiGenericCallProcessor {
    /**
     *
     */
    public function getCall($calls, $index) {
        $call = $calls[$index];

        if ($this->style->getCollapseDataParagraphs() && $index + 2 < count($calls) &&
                $calls[$index + 1][0] == 'cdata' && $calls[$index + 2][0] == 'p_close') {
            $call[0] = 'p_cdata';
            $call[1] = $calls[$index + 1][1];

            return array($call, 3);
        }

        return array($call, 1);
    }
}

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

class DokuwikiPCdataCallFormatter extends DokuwikiCdataCallFormatter {
    /**
     *
     */
    public function format($call) {
        $output = parent::format($call);

        if ($this->style->getEmptyLineAfterParagraph()) {
            $output .= "\n";
        }

        return $output;
    }
}

class DokuwikiPCloseCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    public function format($call) {
        $output = parent::format($call);

        if ($this->style->getEmptyLineAfterParagraph()) {
            $output .= "\n";
        }

        return $output;
    }
}

DokuwikiCallsFormatter::registerProcessor('p_open', 'DokuwikiPopenCallProcessor');

DokuwikiCallsFormatter::registerFormatter('cdata', 'DokuwikiCdataCallFormatter');
DokuwikiCallsFormatter::registerFormatter('header', 'DokuwikiHeaderCallFormatter');
DokuwikiCallsFormatter::registerFormatter('p_cdata', 'DokuwikiPCdataCallFormatter');
DokuwikiCallsFormatter::registerFormatter('p_close', 'DokuwikiPCloseCallFormatter');

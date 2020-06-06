<?php

/**
 * DokuWiki QnA plugin call formatters
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('generic.php');
require_once('formatter.php');

class DokuwikiRefnotesReferenceCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    protected function formatCall($call) {
        return 'refnotes_reference_' . $call[1][1][0];
    }

    /**
     *
     */
    protected function formatCallData($call) {
        switch ($call[1][1][0]) {
            case 'start':
                return $this->formatStart($call[1][3]);

            case 'render':
                return $this->formatRender($call[1][1]);
        }

        return '';
    }

    /**
     *
     */
    private function formatStart($call) {
        if ($this->style->getInlineData()) {
            return ' {' . $this->formatString($call, DokuwikiCallsStyle::MAX_COMPACT_STRING_LENGTH) . '}';
        }

        return $this->style->getDataIndent() . 'text => ' . $this->formatString($call, DokuwikiCallsStyle::MAX_STRING_LENGTH) . "\n";
    }

    /**
     *
     */
    private function formatRender($call) {
        $output = $this->formatProperties('attributes', $call[1]);

        if (count($call) > 2) {
            $output .= $this->style->getDataSeparator();
            $output .= $this->formatProperties('data', $call[2]);
        }

        if ($this->style->getInlineData()) {
            return ' {' . $output . '}';
        }

        return $output;
    }

    /**
     *
     */
    private function formatProperties($tag, $call) {
        $output =  $tag . ' => ';

        if ($this->style->getCompactData() || $this->style->getInlineData()) {
            $output .= '{' . $this->formatArrayCompact($call) . '}';
        }
        else {
            $output .= $this->formatValue($call, $this->style->getDataIndent());
        }

        if ($this->style->getInlineData()) {
            return $output;
        }

        return $this->style->getDataIndent() . $output . "\n";
    }
}

DokuwikiCallsFormatter::registerFormatter('plugin:refnotes_references', 'DokuwikiRefnotesReferenceCallFormatter');

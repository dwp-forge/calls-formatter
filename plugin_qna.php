<?php

/**
 * DokuWiki QnA plugin call formatters
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('generic.php');
require_once('formatter.php');

class DokuwikiQnaBlockCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    protected function formatCall($call) {
        return 'qna_' . $call[1][1][0];
    }

    /**
     *
     */
    protected function formatCallData($call) {
        switch ($call[1][1][0]) {
        	case 'open_question':
            	return $this->formatOpenQuestion($call[1][1]);
        }

        return '';
    }

    /**
     *
     */
    private function formatOpenQuestion($call) {
        if ($this->style->getInlineData()) {
            return ' ' . $this->formatOpenQuestionData($call);
        }

        return $this->style->getDataIndent() . $this->formatOpenQuestionData($call) . "\n";
    }

    /**
     *
     */
    private function formatOpenQuestionData($call) {
        $maxTitleLength = $this->style->getInlineData() ? 40 : 80;

        return $this->formatStringCompact($call[1], $maxTitleLength) . $this->style->getDataSeparator() .
            'id = ' . $call[2];
    }
}

DokuwikiCallsFormatter::registerFormatter('plugin:qna_block', 'DokuwikiQnaBlockCallFormatter');

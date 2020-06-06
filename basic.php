<?php

/**
 * DokuWiki basic call formatters
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <dwpforge@gmail.com>
 */

require_once('generic.php');
require_once('formatter.php');

abstract class DokuwikiCdataCollapseProcessor extends DokuwikiGenericCallProcessor {

    private $closeCall;
    private $collapsedCall;

    /**
     *
     */
    public function __construct($style, $callBase) {
        parent::__construct($style);

        $this->closeCall = $callBase . '_close';
        $this->collapsedCall = $callBase . '_cdata';
    }

    /**
     *
     */
    abstract protected function collapse();

    /**
     *
     */
    public function getCall($calls, $index) {
        $call = $calls[$index];

        if ($this->collapse() && $index + 2 < count($calls) && $calls[$index + 1][0] == 'cdata' &&
                $calls[$index + 2][0] == $this->closeCall) {
            $call[0] = $this->collapsedCall;
            $call[1] = $calls[$index + 1][1];

            return array($call, 3);
        }

        return array($call, 1);
    }
}

class DokuwikiPopenCallProcessor extends DokuwikiGenericCallProcessor {
    /**
     *
     */
    public function __construct($style) {
        parent::__construct($style, 'p');
    }

    /**
     *
     */
    protected function collapse() {
        return $this->style->getCollapseDataParagraphs();
    }
}

abstract class DokuwikiFormattingCollapseProcessor extends DokuwikiCdataCollapseProcessor {
    /**
     *
     */
    protected function collapse() {
        return $this->style->getCollapseDataFormatting();
    }
}

class DokuwikiEmphasisOpenCallProcessor extends DokuwikiFormattingCollapseProcessor {
    /**
     *
     */
    public function __construct($style) {
        parent::__construct($style, 'emphasis');
    }
}

class DokuwikiStrongOpenCallProcessor extends DokuwikiFormattingCollapseProcessor {
    /**
     *
     */
    public function __construct($style) {
        parent::__construct($style, 'strong');
    }
}

class DokuwikiUnderlineOpenCallProcessor extends DokuwikiFormattingCollapseProcessor {
    /**
     *
     */
    public function __construct($style) {
        parent::__construct($style, 'underline');
    }
}

class DokuwikiCdataCallFormatter extends DokuwikiGenericCallFormatter {
    /**
     *
     */
    protected function formatCallData($call) {
        if ($this->style->getInlineData()) {
            return ' ' . $this->formatString($call[1][0], 50);
        }

        return $this->style->getDataIndent() . $this->formatString($call[1][0], 80) . "\n";
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

        return $this->formatString($call[1][0], $maxTitleLength) . $this->style->getDataSeparator() .
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

DokuwikiCallsFormatter::registerProcessor('emphasis_open', 'DokuwikiEmphasisOpenCallProcessor');
DokuwikiCallsFormatter::registerProcessor('p_open', 'DokuwikiPopenCallProcessor');
DokuwikiCallsFormatter::registerProcessor('strong_open', 'DokuwikiStrongOpenCallProcessor');
DokuwikiCallsFormatter::registerProcessor('underline_open', 'DokuwikiUnderlineOpenCallProcessor');

DokuwikiCallsFormatter::registerFormatter('cdata', 'DokuwikiCdataCallFormatter');
DokuwikiCallsFormatter::registerFormatter('header', 'DokuwikiHeaderCallFormatter');
DokuwikiCallsFormatter::registerFormatter('p_cdata', 'DokuwikiPCdataCallFormatter');
DokuwikiCallsFormatter::registerFormatter('p_close', 'DokuwikiPCloseCallFormatter');

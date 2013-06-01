<?php

namespace Hal\MutaTesting\Diff;

class DiffHtml 
{


    /**
     * Returns the diff between two arrays or strings as string.
     *
     * @param  array|string $from
     * @param  array|string $to
     * @return string
     */
    public function diff($from, $to)
    {
        $tool = new \SebastianBergmann\Diff('');
        $buffer = '';

        $diff = $tool->diffToArray($from, $to);

        $inOld = FALSE;
        $i = 0;
        $old = array();

        foreach ($diff as $line) {
            if ($line[1] === 0 /* OLD */) {
                if ($inOld === FALSE) {
                    $inOld = $i;
                }
            } else if ($inOld !== FALSE) {
                if (($i - $inOld) > 5) {
                    $old[$inOld] = $i - 1;
                }

                $inOld = FALSE;
            }
            ++$i;
        }

        $start = isset($old[0]) ? $old[0] : 0;
        $end = count($diff);

        if ($tmp = array_search($end, $old)) {
            $end = $tmp;
        }

        $newChunk = TRUE;

        for ($i = $start; $i < $end; $i++) {
            if (isset($old[$i])) {
                $buffer .= "<br />";
                $newChunk = TRUE;
                $i = $old[$i];
            }

            if ($newChunk) {
//                $buffer .= "@@ @@\n";
                $newChunk = FALSE;
            }

            if ($diff[$i][1] === 1 /* ADDED */) {
                $buffer .= '<span style="background-color:#DFF0D8;">' . $this->highlight($diff[$i][0]) . "</span><br />";
            } else if ($diff[$i][1] === 2 /* REMOVED */) {
                $buffer .= '<span style="background-color:#F2DEDE;">' . $this->highlight($diff[$i][0]) . "</span><br />";
            } else {
                $buffer .= ' ' . $this->highlight($diff[$i][0]) . "<br />";
            }
        }

        return $buffer;
    }
    
    public function highlight($string) {
        $output = highlight_string('<?php '.$string, true);
        $output = preg_replace('!(&lt;\?php(&nbsp;).*?)!', '', $output);
        return $output;
    }

}

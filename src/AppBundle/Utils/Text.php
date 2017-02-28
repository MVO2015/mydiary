<?php

namespace AppBundle\Utils;

class Text
{
    /**
     * Shorten text to specified maximum and append "...".
     *
     * @param string $text Input text
     * @param int $length Maximum lenght without "..."
     * @return string Short text
     */
    public function shorten($text, $length=200)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        $pos=strpos($text, ' ', $length);
        $shortText = substr($text,0,$pos) . "...";
        return strlen($shortText) < strlen($text) ? $shortText : $text;
    }

    /**
     * Get the first sentence from string
     * @param string $content Input string
     * @return string The first sentence
     */
    public function getFirstSentence($content) {

        $string = preg_replace("/[\s]/", " ", $content); // replace whitespaces with spaces

        $dot = strpos($string, '. ');
        $exc = strpos($string, '! ');
        $que = strpos($string, '? ');

        $string = trim($string);
        $len = strlen($string);

        $dot = $dot ? $dot : $len;
        $exc = $exc ? $exc : $len;
        $que = $que ? $que : $len;

        $pos = min($dot, $exc, $que);
        $result = substr($string, 0, $pos + 1);
        // do not show last dot
        if (substr($result, - 1, 1) == '.')
        {
            $result = substr($result, 0, -1);
        }
        return $result;
    }

    /**
     * Get headline of an article as first sentence or shorten first sentence
     * @param $content
     * @return string
     */
    public function getHeadline($content)
    {
        return $this->shorten($this->getFirstSentence($content));
    }
}

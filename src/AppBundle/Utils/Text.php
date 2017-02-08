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
}

<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="DiaryEntryRepository")
 * @ORM\Table(name="diary_entry")
 */
class DiaryEntry
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $dateTime;
    /**
     * @ORM\Column(type="text")
     */
    private $note;
    /**
     * @ORM\Column(type="string")
     */
    private $category;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="diaryEntries")
     * @ORM\JoinTable(name="diaryEntries_tags")
     */
    private $tags;

    /**
     * DiaryEntry constructor.
     * @param string $dateTime
     * @param string $note
     * @param string $category
     */
    public function __construct($dateTime, $note, $category)
    {
        $this->dateTime = $dateTime;
        $this->note = $note;
        $this->category = $category;
        $this->tags = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param DateTime $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get tags, delimited with comma
     *
     * @return string of tags, delimited with comma
     */
    public function getTextTags()
    {
        $tagsArray = $this->getTags();
        $textTags = [];
        /** @var Tag $oneTag */
        foreach ($tagsArray as $oneTag) {
            $textTags[] = $oneTag->getText();
        }
        return implode(", ", $textTags);
    }

    public function getTempTags()
    {
        $tagsArray = $this->getTags();
        $tempTags = [];
        /** @var Tag $oneTag */
        foreach ($tagsArray as $key => $oneTag) {
            $tempTags[$oneTag->getText()] = $oneTag->getId();
        }
        return $tempTags;
    }

    /**
     * Shorten text to specified maximum and append "...".
     *
     * @param int $length Maximum length without "..."
     * @return string Short text
     */
    private function shorten($text, $length=200)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        $pos=strpos($text, ' ', $length);
        $shortText = substr($text,0,$pos) . "...";
        return strlen($shortText) < strlen($text) ? $shortText : $text;
    }

    /**
     * Get the first sentence
     * @return string The first sentence
     */
    public function getFirstSentence() {

        $string = preg_replace("/[\s]/", " ", $this->getNote()); // replace whitespaces with spaces

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
    public function getTitle()
    {
        return $this->shorten($this->getFirstSentence());
    }

    /**
     * Shorten note to specified maximum and append "...".
     *
     * @return string Short text
     */
    public function getShort()
    {
        return $this->shorten($this->getNote());
    }
}

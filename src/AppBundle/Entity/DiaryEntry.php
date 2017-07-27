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
     * @var string
     * @ORM\Column(type="string")
     */
    private $category;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="diaryEntries", cascade={"persist"})
     * @ORM\JoinTable(name="diaryEntries_tags")
     */
    public $tags;

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
     * Get tags as string, delimited with comma
     * If there is no tag, return "".
     *
     * @return string of tags, delimited with comma
     */
    public function getTagsAsString()
    {
        $tags = $this->getTags();
        if ($tags) {
            $tagsArray = [];
            /** @var Tag $oneTag */
            foreach ($tags as $oneTag) {
                $tagsArray[] = $oneTag->getText();
            }
            return implode(", ", $tagsArray);
        }
        return "";
    }

    /**
     * Get tags as array ['text'] => id
     *
     * @return array
     */
    public function getTagsAsArrayByText()
    {
        $tags = $this->getTags();
        $tagsArray = [];
        /** @var Tag $oneTag */
        foreach ($tags as $key => $oneTag) {
            $tagsArray[$oneTag->getText()] = $oneTag->getId();
        }
        return $tagsArray;
    }

    /**
     * Get tags as array [id1, id2, id...]
     *
     * @return array
     */
    public function getTagsAsArrayOfIds()
    {
        $tags = $this->getTags();
        $idsArray = [];
        /** @var Tag $oneTag */
        foreach ($tags as $oneTag) {
            $idsArray[] = $oneTag->getId();
        }
        return $idsArray;
    }

    /**
     * Get tags as array [id] => 'text'
     *
     * @return array
     */
    public function getTagsAsArrayById()
    {
        $tags = $this->getTags();
        $tagsArray = [];
        /** @var Tag $oneTag */
        foreach ($tags as $oneTag) {
            $tagsArray[$oneTag->getId()] = $oneTag->getText();
        }
        return $tagsArray;
    }

    /**
     * Get tag by Id
     * @param $id Tag id
     * @return int|string|bool Tag key
     */
    public function getTagKeyById($id)
    {
        $tags = $this->tags;
        foreach ($tags as $key => $tag) {
            if ($tag->getId() == $id) {
                return $key;
            }
        }
        return false;
    }

    /**
     * Remove tag from the diary entry
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        /** @var Tag $oneTag */
        foreach ($this->tags as $key => $oneTag) {
            if ($oneTag->getId() == $tag->getId()) {
                unset($this->tags[$key]);
                break;
            }
        }
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

    /**
     * Add tag
     * @param Tag $tag
     */
    public function addTag($tag)
    {
        $this->tags->add($tag);
    }

    public function getTagsCollection()
    {
        return $this->getTags();
    }
}

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
}

<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class AddNewChoiceTransformer implements DataTransformerInterface
{
    private $em;
    private $user;

    public function __construct(EntityManagerInterface $em, User $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * Do nothing
     * Called when form is invoked.
     *
     * @param string[] $text
     * @return string[]
     */
    public function transform($text)
    {
        return $text;
    }

    /**
     * Add new choice in repository and return complete list in correct form.
     * Called when form is submitted.
     *
     * @param string[] $sentData
     * @return string[]
     */
    public function reverseTransform($sentData)
    {
        foreach ($sentData as $key => $item) {
            if (!is_numeric($item)) {
                $newTag = new Tag();
                $newTag->setText($item);
                $newTag->setUserId($this->user->getId());
                $this->em->persist($newTag);
                $this->em->flush();
                $sentData[$key] = $newTag->getId();
            }
        }
        return $sentData;
    }
}
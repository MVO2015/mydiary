<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagToTextTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Tag|null $tag
     * @return string
     */
    public function transform($tag)
    {
        if (null === $tag) {
            return '';
        }

        return $tag->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $tagText
     * @return Tag|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($tagText)
    {
        // no issue number? It's optional, so that's ok
        if (!$tagText) {
            return null;
        }

        /** @var Tag $tag */
        $tag = $this->em
            ->getRepository(Tag::class)
            // query for the tag with this text
            ->findBy(['text' => $tagText]);

        if (null === $tag) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A tag with text "%s" does not exist!',
                $tagText
            ));
        }

        return $tag;
    }
}
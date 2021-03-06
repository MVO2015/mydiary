<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function findAllByUserOrderBy($userId, $orderBy='text', $sort='asc')
    {
        return $this->findBy(['userId' => $userId], array($orderBy => $sort));
    }

    public function findByUserAndId($userId, $id)
    {
        return $this->findBy(['userId' => $userId, 'id' => $id])[0] ?? false;
    }

    public function findAllAsChoiceArray()
    {
        $tags = $this->findBy(array(), array("id" => 'asc'));
        $tagChoices = [];
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $tagChoices[$tag->getText()]= $tag->getId();
        }
        return $tagChoices;
    }

    /**
     * @param int $userId user id
     */
    public function findAllByUser($userId)
    {
        $qb = $this->createQueryBuilder('tag');
        $q = $qb->where('tag.userId = :uid')
            ->setParameter(':uid', $userId)
            ->getQuery();
        return $q->getResult();
    }

    public function findOneByUserAndText($userId, $text)
    {
        $qb = $this->createQueryBuilder('tag');
        $q = $qb->where('tag.userId = :uid AND tag.text = :text')
            ->setParameters([':uid' => $userId, ':text' => $text])
            ->getQuery();
        return $q->getResult();
    }
}

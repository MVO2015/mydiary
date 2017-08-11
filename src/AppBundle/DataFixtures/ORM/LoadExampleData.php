<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Tag;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\DiaryEntry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadExampleData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        // Create our user1 and set details
        $userData = [
            ['martin', 'martin@email.com', '123456', true, 'ROLE_USER'],
            ['jana', 'jana@email.com', '123456', true, 'ROLE_USER'],
        ];
        foreach ($userData as $oneUser) {
            $user= $userManager->createUser();
            $user->setUsername($oneUser[0]);
            $user->setEmail($oneUser[1]);
            $user->setPlainPassword($oneUser[2]);
            //$user->setPassword('3NCRYPT3D-V3R51ON');
            $user->setEnabled($oneUser[3]);
            $user->setRoles(array($oneUser[4]));
            // Update the user
            $userManager->updateUser($user, true);
            $users[] = $user;
        }

        $string = file_get_contents(__DIR__ . "/data.json");
        $jsonArray = json_decode($string, true);
        foreach ($jsonArray as $item) {
            $userId = $users[$item['user_id']]->getId();
            $date = date_create_from_format('Ymd', $item['date']);
            $diaryEntry = new DiaryEntry(
                $userId,
                $date,
                $item['text'],
                ""
            );
            $manager->persist($diaryEntry);
            $manager->flush();
        }

        $tags = "php symfony api css git javascript python shell mysql html yaml xml docker composer";
        $tagsArray = explode(" ", $tags);
        foreach ($tagsArray as $tag) {
            $tagRecord = new Tag();
            $tagRecord->setText($tag);
            $tagRecord->setUserId($users[0]->getId());
            $manager->persist($tagRecord);
            $manager->flush();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 1;
    }
}
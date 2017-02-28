<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Tag;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\DiaryEntry;

class LoadExampleData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $string = file_get_contents(__DIR__ . "/data.json");
        $jsonArray = json_decode($string, true);
        foreach ($jsonArray as $item) {
            $date = date_create_from_format('Ymd', $item['date']);
            $diaryEntry = new DiaryEntry(
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
            $manager->persist($tagRecord);
            $manager->flush();
        }
    }
}
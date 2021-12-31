<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new \App\Entity\Profile();
        $profile->setRs('Facebook');
        $profile->setUrl('https://www.facebook.com/aymen.sellaouti');

        $profile = new \App\Entity\Profile();
        $profile->setRs('twitter');
        $profile->setUrl('https://twitter.com/aymensellaouti');

        $profile1 = new \App\Entity\Profile();
        $profile1->setRs('Facebook');
        $profile1->setUrl('https://www.facebook.com/aymen.sellaouti');

        $profile2 = new \App\Entity\Profile();
        $profile2->setRs('LinkedIn');
        $profile2->setUrl('https://www.linkedin.com/in/aymen-sellaouti-b0427731/');

        $profile3 = new \App\Entity\Profile();
        $profile3->setRs('Github');
        $profile3->setUrl('https://github.com/aymensellaouti');

        $manager->persist($profile);
        $manager->persist($profile2);
        $manager->persist($profile1);
        $manager->persist($profile3);
        $manager->flush();
    }
}

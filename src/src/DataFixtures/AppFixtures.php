<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $author1 = new Author();
        $author1->setName('Steve Jobs');

        $quote1 = new Quote();
        $quote1->setQuote("I am the best");
        $quote2 = new Quote();
        $quote2->setQuote("Iphone is my precious");

        $author1->setQuotes([$quote1, $quote2]);

        $author2 = new Author();
        $author2->setName('John Doe');

        $quote3 = new Quote();
        $quote3->setQuote("I am the worst");
        $quote4 = new Quote();
        $quote4->setQuote("Iphone is ridiculous");

        $author2->setQuotes([$quote3, $quote4]);


        $manager->persist($author1);
        $manager->persist($author2);
        $manager->flush();
    }
}

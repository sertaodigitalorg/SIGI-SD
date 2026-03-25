<?php

namespace App\DataFixtures;

use App\Entity\ThematicArea;
use App\Entity\CoverageType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CatalogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Load thematic areas
        $thematicArea = new ThematicArea();
        $thematicArea->setName('Environment');
        $manager->persist($thematicArea);

        // Load coverage types
        $coverageType = new CoverageType();
        $coverageType->setName('National');
        $manager->persist($coverageType);

        // Add more as needed

        $manager->flush();
    }
}
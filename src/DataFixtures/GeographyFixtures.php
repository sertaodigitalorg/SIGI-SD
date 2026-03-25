<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\Region;
use App\Entity\State;
use App\Entity\Mesoregion;
use App\Entity\Microregion;
use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GeographyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // =========================
        // COUNTRY - BRAZIL
        // =========================
        $brazil = new Country();
        $brazil->setName('Brazil');
        $brazil->setIso2('BR');
        $brazil->setIso3('BRA');
        $brazil->setNumericCode('076');
        $brazil->setPhoneCode('55');
        $brazil->setCurrency('BRL');
        $brazil->setCreatedAt(new \DateTimeImmutable('2026-03-19 01:33:06'));

        $manager->persist($brazil);

        // =========================
        // REGION - NORTHEAST
        // =========================
        $northeast = new Region();
        $northeast->setName('Nordeste');
        $northeast->setCountry($brazil);

        $manager->persist($northeast);

        // =========================
        // STATES (ALL BRAZIL)
        // =========================
        $statesData = [
            ['AC', 'Acre'],
            ['AL', 'Alagoas'],
            ['AP', 'Amapá'],
            ['AM', 'Amazonas'],
            ['BA', 'Bahia'],
            ['CE', 'Ceará'],
            ['DF', 'Distrito Federal'],
            ['ES', 'Espírito Santo'],
            ['GO', 'Goiás'],
            ['MA', 'Maranhão'],
            ['MT', 'Mato Grosso'],
            ['MS', 'Mato Grosso do Sul'],
            ['MG', 'Minas Gerais'],
            ['PA', 'Pará'],
            ['PB', 'Paraíba'],
            ['PR', 'Paraná'],
            ['PE', 'Pernambuco'],
            ['PI', 'Piauí'],
            ['RJ', 'Rio de Janeiro'],
            ['RN', 'Rio Grande do Norte'],
            ['RS', 'Rio Grande do Sul'],
            ['RO', 'Rondônia'],
            ['RR', 'Roraima'],
            ['SC', 'Santa Catarina'],
            ['SP', 'São Paulo'],
            ['SE', 'Sergipe'],
            ['TO', 'Tocantins'],
        ];

        $states = [];

        foreach ($statesData as [$uf, $name]) {
            $state = new State();
            $state->setUf($uf);
            $state->setName($name);
            $state->setCountry($brazil);

            // Apenas Nordeste vinculado à região
            if (in_array($uf, ['AL','BA','CE','MA','PB','PE','PI','RN','SE'])) {
                $state->setRegion($northeast);
            }

            $manager->persist($state);
            $states[$uf] = $state;
        }

        // =========================
        // MESOREGION - SERTÃO PB
        // =========================
        $mesoregion = new Mesoregion();
        $mesoregion->setName('Sertão Paraibano');
        $mesoregion->setIbgeCode('2504');
        $mesoregion->setMunicipalitiesCount(83);
        $mesoregion->setState($states['PB']);

        $manager->persist($mesoregion);

        // =========================
        // MICROREGION - SOUSA
        // =========================
        $microregion = new Microregion();
        $microregion->setName('Sousa');
        $microregion->setIbgeCode('25005');
        $microregion->setMesoregion($mesoregion);

        $manager->persist($microregion);

        // =========================
        // CITIES
        // =========================

        // Sousa
        $sousa = new City();
        $sousa->setState($states['PB']);
        $sousa->setMicroregion($microregion);
        $sousa->setIbgeName('Sousa');
        $sousa->setIbgeCode('2516201');
        $sousa->setIbgeCode7('2516201');
        $sousa->setZipCode('58800000');
        $sousa->setIsCapital(false);

        $manager->persist($sousa);

        // Marizópolis
        $marizopolis = new City();
        $marizopolis->setState($states['PB']);
        $marizopolis->setMicroregion($microregion);
        $marizopolis->setIbgeName('Marizópolis');
        $marizopolis->setIbgeCode('2509152');
        $marizopolis->setIbgeCode7('2509152');
        $marizopolis->setZipCode('58819000');
        $marizopolis->setIsCapital(false);

        $manager->persist($marizopolis);

        // =========================
        // FLUSH
        // =========================
        $manager->flush();
    }
}
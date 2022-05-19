<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $slugger;

    public function __construct(UserPasswordHasherInterface $encoder, SluggerInterface $slugger)
    {
        $this->encoder = $encoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker=Faker\Factory::create('fr_FR');

        $admin = new User();

        $admin
            ->setEmail('a@a.a')
            ->setRegistrationDate($faker->dateTimeBetween('-1 year', 'now'))
            ->setPseudonym('Batman')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(
                $this->encoder->hashPassword($admin, 'Lo!!71210')
            )


        ;

        $manager->persist($admin);

        for ($i = 0; $i<10; $i++){
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setRegistrationDate($faker->dateTimeBetween('-1 year', 'now'))
                ->setPseudonym($faker->userName)
                ->setPassword(
                    $this->encoder->hashPassword($user, 'Lo!!71210')
                )
            ;

            $manager->persist($user);
        }

        for($i=0; $i<200; $i++){
            $article = new Article();

            $article
                ->setTitle( $faker->sentence(10) )
                ->setContent($faker->paragraph(15))
                ->setPublicationDate($faker->dateTimeBetween('-1 year','now'))
                ->setAuthor( $admin )
                ->setSlug( $this->slugger->slug($article->getTitle())->lower() )
            ;

            $manager->persist($article);

        }

        $manager->flush();
    }
}

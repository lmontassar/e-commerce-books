<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Livres;
use DateTimeImmutable;
use App\Entity\Commande;
use App\Entity\Categories;
use App\Entity\CommandeRelation;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $faker;

    public function __construct( UserPasswordHasherInterface $userPasswordHasher )
    {
        $this->passwordEncoder = $userPasswordHasher ;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager ,)
    {
        $users = [];
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setNom($this->faker->lastName);
            $user->setPrenom($this->faker->firstName);
            $user->setPassword($this->passwordEncoder->hashPassword(
                $user,
                'password'.$i
            ));
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(false);
            $user->setAdresse($this->faker->address);
            $user->setNumtel($this->faker->randomNumber(8, true));
            $manager->persist($user);
            $users[] = $user;
        }
        foreach ($users as $index => $user) {
            $this->addReference('user_' . $index, $user);
        }
        $books = [];

        for ($i = 0; $i < 5; $i++) {
            $category = new Categories();
            $category->setLibelle($this->faker->word);
            $category->setSlug($this->faker->slug);
            $category->setDescription($this->faker->paragraph);
            $manager->persist($category);

            for ($j = 0; $j < 10; $j++) {
                $editedAt = $this->faker->dateTimeBetween('-12 months', 'now');
                $editedAtImmutable = DateTimeImmutable::createFromMutable($editedAt);
                $book = new Livres();
                $book->setTitre($this->faker->name());
                $book->setSlug($this->faker->slug);
                $book->setImage("https://picsum.photos/480/720");
                $book->setISBN($this->faker->isbn13);
                $book->setEditeur($this->faker->company);
                $book->setEditedAt($editedAtImmutable);
                $book->setResume($this->faker->paragraph);
                $book->setPrix($this->faker->randomFloat(2, 10, 100));
                $book->setQte($this->faker->numberBetween(1, 100));
                $book->setAuteur($this->faker->name);
                $book->setCategorie($category);
                $manager->persist($book);
                $books[] = $book;
            }
        }
        foreach ($books as $index => $book) {
            $this->addReference('book_' . $index, $book);
        }
        
        for ($i = 0; $i < 100; $i++) {
            $editedAt = $this->faker->dateTimeBetween('-12 months', 'now');
            $editedAtImmutable = DateTimeImmutable::createFromMutable($editedAt);
            $order = new Commande();
            $order->setDateCommandeAt($editedAtImmutable);
            $order->setIsPayed($this->faker->boolean());
            $order->setEtat(1); 
            $order->setClient($this->getReference('user_'. $this->faker->numberBetween(0, 19))); 
            $manager->persist($order);

            for ($j = 0; $j < $this->faker->numberBetween(1, 10) ; $j++) {
                $orderRelation = new CommandeRelation();
                $orderRelation->setQte($this->faker->numberBetween(1, 5));
                $orderRelation->setCommande($order);
                $orderRelation->setLivre($this->getReference('book_'. $this->faker->numberBetween(0, 49))); 
                $manager->persist($orderRelation);
            }
        }
        $manager->flush();
    }
}

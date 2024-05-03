<?php

namespace App\Repository;

use App\Entity\Atelier;
use App\Entity\Vacation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vacation>
 *
 * @method Vacation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vacation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vacation[]    findAll()
 * @method Vacation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacationRepository extends ServiceEntityRepository
{

    private $manager;
    
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct($registry, Vacation::class);
    }

    /**
     * Permet de sauvegarder une vacation
     *
     * @param string $date_debut
     * @param string $date_fin
     * @param Atelier $atelier
     * @return void
     */
    public function save(string $date_debut, string $date_fin, Atelier $atelier){
        $vacation = new Vacation();
        $vacation->setDateHeureDebut($date_debut);
        $vacation->setDateHeureFin($date_fin);
        $vacation->setAtelier($atelier);
        $this->manager->persist($vacation);
        $this->manager->flush();
    }


//    /**
//     * @return Vacation[] Returns an array of Vacation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vacation
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

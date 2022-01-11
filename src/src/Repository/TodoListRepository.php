<?php

namespace App\Repository;

use App\Entity\TodoList;
use App\Repository\Traits\FiltersAndFields;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends ServiceEntityRepository
{
    use FiltersAndFields;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    public function findAllForUser(string $userKey, array $filters, string $orderField, string $orderType): array
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->addSelect($this->getRelatedEntityAlias())
            ->leftJoin("{$this->getAlias()}.subTask", $this->getRelatedEntityAlias())
            ->where("{$this->getAlias()}.userKey = :userKey")
//            ->andWhere("{$this->getAlias()}.parentTask IS NULL")
            ->setParameter('userKey', $userKey);

        $qb = $this->setFilters($filters, $qb);
        $qb = $this->setOrdering($orderField, $orderType, $qb);

        if ($filters === []) {
            $qb->andWhere("{$this->getAlias()}.parentTask IS NULL");
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findOneTaskByIdForUser(int $id, string $userKey): ?TodoList
    {
        try {
            return $this->createQueryBuilder($this->getAlias())
                ->andWhere("{$this->getAlias()}.id = :id")
                ->andWhere("{$this->getAlias()}.userKey = :userKey")
                ->setParameter('id', $id)
                ->setParameter('userKey', $userKey)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findOneTaskWithChild(int $id, string $userKey): ?TodoList
    {
        try {
           return $this->createQueryBuilder($this->getAlias())
                ->addSelect($this->getRelatedEntityAlias())
                ->leftJoin("{$this->getAlias()}.subTask", $this->getRelatedEntityAlias())
                ->andWhere("{$this->getAlias()}.id = :id")
                ->andWhere("{$this->getAlias()}.userKey = :userKey")
                ->setParameter('id', $id)
                ->setParameter('userKey', $userKey)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    protected function getAlias(): string
    {
        return 't';
    }

    protected function getRelatedEntityAlias(): string
    {
        return 'st';
    }

    protected function availableFilerFields(): array
    {
        return ['status', 'priority', 'title', 'description'];
    }

    protected function availableOrderFields(): array
    {
        return ['createdAt', 'doneAt', 'priority'];
    }

    protected function fullTextSearchFieldNames(): array
    {
        return ['title', 'description'];
    }

    protected function getAvailableOrdering(): array
    {
        return ['ASC', 'DESC'];
    }
}

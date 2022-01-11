<?php

namespace App\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Symfony\Config\Framework\Assets\PackageConfig;

trait FiltersAndFields
{
    abstract protected function getAlias(): string;

    abstract protected function getRelatedEntityAlias(): string;

    abstract protected function availableFilerFields(): array;

    abstract protected function availableOrderFields(): array;

    abstract protected function fullTextSearchFieldNames(): array;

    protected function setFilters(array $filters, QueryBuilder $qb): QueryBuilder
    {
        foreach ($filters as $key => $value) {
            if (in_array($key, $this->availableFilerFields(), true)) {
                if (in_array($key, $this->fullTextSearchFieldNames(), true)) {
                    $fields = $this->buildColumnsForMatch();
                    $qb->andWhere("MATCH ($fields) AGAINST (:words) > 0")
                        ->setParameter('words', $value);
                    continue;
                }

                $qb->andWhere("{$this->getAlias()}.{$key} = :value")
                    ->setParameter('value', $value);
            }
        }

        return $qb;
    }

    protected function setOrdering($orderField, $orderType, QueryBuilder $qb): QueryBuilder
    {
        if (in_array($orderField, $this->availableOrderFields(), true) && in_array($orderType, $this->getAvailableOrdering(), true)) {
            $qb->addOrderBy("{$this->getAlias()}.$orderField", $orderType)
                ->addOrderBy("{$this->getRelatedEntityAlias()}.$orderField", $orderType);
        }

        return $qb;
    }

    private function buildColumnsForMatch(): string {
        $fields = '';
        foreach ($this->fullTextSearchFieldNames() as $i => $fieldName) {
            $fields .= $i !== (count($this->fullTextSearchFieldNames()) - 1)
                ? "{$this->getAlias()}.$fieldName,"
                : "{$this->getAlias()}.$fieldName";
        }
        return $fields;
    }
}

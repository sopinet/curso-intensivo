<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CardRepository
 */
class CardRepository extends EntityRepository
{
    public function customFindByString($stringSearch) {
        // Esto buscaría estrictamente los resultados donde el texto fuese exactamente igual

        /**
        $resultTitle = $this->findByTitle($stringSearch);
        $resultSubtitle = $this->findBySubtitle($stringSearch);
        $resultDescription = $this->findByDescription($stringSearch);

        $results = array_merge($resultTitle, $resultSubtitle, $resultDescription);

        return $results;
        **/

        // Esto busca los registros que contienen cualquier parte de $stringSearch

        $queryBuilder = $this->createQueryBuilder('c');
        $query = $queryBuilder->where('c.title LIKE :stringSearch')
            ->orWhere('c.subtitle LIKE :stringSearch')
            ->orWhere('c.description LIKE :stringSearch')
            ->setParameter('stringSearch', '%'.$stringSearch.'%')
            ->getQuery();

        return $query->getResult();
    }
}

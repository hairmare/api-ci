<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ProjectRepository extends DocumentRepository
{
    public function getPendingProjects($count = 5)
    {
        return $this->createQueryBuilder()
            ->field('needsUpdate')->equals(true)
            ->sort('updatedAt', 'desc')
            ->limit($count)
            ->getQuery()->execute();
    }

    public function getRecentProjects($count = 5)
    {
        return $this->createQueryBuilder()
            ->sort('updatedAt', 'desc')
            ->limit($count)
            ->getQuery()->execute();
    }
}


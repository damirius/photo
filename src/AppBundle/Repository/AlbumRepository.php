<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AlbumRepository extends EntityRepository
{
    public function findAllWithPicture()
    {
        $queryBuilder = $this->createQueryBuilder('album');

        $query = $queryBuilder
            ->select('a')
            ->from('AppBundle:Album','a')
            ->leftJoin('a.photos','p')
            ->having('count(p.id)>1')
            ->groupBy('a.id')
            ->orderBy('a.created','DESC')
            ->getQuery();
        dump($query->getSQL());
        return $query->getResult();
    }

    public function findOneWithPicture($id)
    {
        $queryBuilder = $this->createQueryBuilder('album');

        $query = $queryBuilder
            ->select('a')
            ->from('AppBundle:Album','a')
            ->leftJoin('a.photos','p')
            ->where('a.id = :id')
            ->having('count(p.id)>1')
            ->setParameter('id',$id)
            ->getQuery();
        if(count($query->getResult())==0) {
            return null;
        } else {
            return $query->getResult()[0];
        }
    }

    public function findAllByTag($tag)
    {
        $queryBuilder = $this->createQueryBuilder('album');

        $query = $queryBuilder
            ->select('a')
            ->from('AppBundle:Album','a')
            ->leftJoin('a.photos','p')
            ->leftJoin('a.tags', 't')
            ->where('t.name = :tag')
            ->having('count(p.id)>1')
            ->groupBy('a.id')
            ->orderBy('a.created','DESC')
            ->setParameter('tag',$tag)
            ->getQuery();
        return $query->getResult();
    }

    public function findAllByCategory($category)
    {
        $queryBuilder = $this->createQueryBuilder('album');

        $query = $queryBuilder
            ->select('a')
            ->from('AppBundle:Album','a')
            ->leftJoin('a.photos','p')
            ->leftJoin('a.category', 'c')
            ->where('c.name = :category')
            ->having('count(p.id)>1')
            ->groupBy('a.id')
            ->orderBy('a.created','DESC')
            ->setParameter('category',$category)
            ->getQuery();
        return $query->getResult();
    }

    public function findAllByQuery($search)
    {
        $queryBuilder = $this->createQueryBuilder('album');

        $query = $queryBuilder
            ->select('a')
            ->from('AppBundle:Album','a')
            ->leftJoin('a.photos','p')
            ->where('a.title LIKE :search')
            ->having('count(p.id)>1')
            ->groupBy('a.id')
            ->orderBy('a.created','DESC')
            ->setParameter('search','%'.$search.'%')
            ->getQuery();
        return $query->getResult();
    }
}
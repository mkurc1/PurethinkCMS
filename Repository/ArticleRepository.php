<?php

namespace Purethink\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Purethink\CMSBundle\Entity\Article;

class ArticleRepository extends EntityRepository
{
    public function searchResults($locale, $search)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('t')
            ->join('a.translations', 't')
            ->where('a.published = true')
            ->andWhere('t.locale = :locale')
            ->andWhere('UPPER(t.name) LIKE UPPER(:search)')
            ->setParameter('locale', $locale)
            ->setParameter('search', '%' . $search . '%');

        return $qb->getQuery()->getResult();
    }

    public function articleBySlug($slug)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('t')
            ->join('a.translations', 't')
            ->where('a.published = true')
            ->andWhere('t.slug = :slug')
            ->setParameter('slug', $slug)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function prevArticle(Article $article)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.createdAt < :createdAt')
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter('createdAt', $article->getCreatedAt())
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function nextArticle(Article $article)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.createdAt > :createdAt')
            ->orderBy('a.createdAt')
            ->setParameter('createdAt', $article->getCreatedAt())
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}

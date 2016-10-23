<?php

namespace Purethink\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Purethink\CMSBundle\Entity\Article;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleRepository extends EntityRepository
{
    public function getMonthsWithArticles()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.createdAt AS date')
            ->addSelect('YEAR(a.createdAt) AS HIDDEN year')
            ->addSelect('MONTH(a.createdAt) AS HIDDEN month')
            ->where('a.published = true')
            ->groupBy('year', 'month')
            ->orderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getUserArticlesQuery(UserInterface $user) : Query
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('t')
            ->join('a.translations', 't')
            ->where('a.published = true')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user);

        $this->addOrderByCreatedAt($qb);

        return $qb->getQuery();
    }

    public function getArticlesQuery($year = null, $month = null) : Query
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('t')
            ->join('a.translations', 't')
            ->where('a.published = true');

        if (null != $year && null != $month) {
            $qb->andWhere('YEAR(a.createdAt) = :year')
                ->setParameter('year', $year)
                ->andWhere('MONTH(a.createdAt) = :month')
                ->setParameter('month', $month);
        }

        $this->addOrderByCreatedAt($qb);

        return $qb->getQuery();
    }

    public function searchResultsQuery($locale, $search) : Query
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('t')
            ->join('a.translations', 't')
            ->where('a.published = true')
            ->andWhere('t.locale = :locale')
            ->andWhere('UPPER(t.name) LIKE UPPER(:search)')
            ->setParameter('locale', $locale)
            ->setParameter('search', '%' . $search . '%');

        return $qb->getQuery();
    }

    public function articleBySlug($slug)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('t, m')
            ->join('a.translations', 't')
            ->leftJoin('a.media', 'm')
            ->where('a.published = true')
            ->andWhere('t.slug = :slug')
            ->setParameter('slug', $slug)
            ->setMaxResults(1);

        $this->getEntityManager()->getFilters()->disable('oneLocale');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function prevArticle(Article $article)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('at')
            ->join('a.translations', 'at')
            ->where('a.createdAt < :createdAt')
            ->setParameter('createdAt', $article->getCreatedAt())
            ->andWhere('at.slug IS NOT NULL');

        $this->addOrderByCreatedAt($qb);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function nextArticle(Article $article)
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('at')
            ->join('a.translations', 'at')
            ->where('a.createdAt > :createdAt')
            ->setParameter('createdAt', $article->getCreatedAt())
            ->andWhere('at.slug IS NOT NULL');

        $this->addOrderByCreatedAt($qb, 'ASC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    private function addOrderByCreatedAt(QueryBuilder $qb, string $order = 'DESC')
    {
        $qb->orderBy($qb->getRootAliases()[0] . '.createdAt', $order);
    }
}

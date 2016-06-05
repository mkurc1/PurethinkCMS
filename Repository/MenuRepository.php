<?php

namespace Purethink\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MenuRepository extends EntityRepository
{
    public function getActiveMenu($typeSlug, $locale)
    {
        $articles = $this->getEntityManager()->getRepository('PurethinkCMSBundle:MenuArticle')
            ->getActiveMenus($typeSlug, $locale);

        $urls = $this->getEntityManager()->getRepository('PurethinkCMSBundle:MenuUrl')
            ->getActiveMenus($typeSlug, $locale);

        $actions = $this->getEntityManager()->getRepository('PurethinkCMSBundle:MenuAction')
            ->getActiveMenus($typeSlug, $locale);

        $menus = array_merge($articles, $urls, $actions);

        $qb = $this->createQueryBuilder('a')
            ->addSelect('at')
            ->join('a.translations', 'at', 'WITH', 'at.locale = :locale')
            ->where('a.published = true')
            ->andWhere('a IN (:menus)')
            ->andWhere('a.menu IS NULL')
            ->orderBy('a.position')
            ->groupBy('a.id')
            ->setParameter('menus', $menus)
            ->setParameter('locale', $locale);

        return $qb->getQuery()->getResult();
    }
}

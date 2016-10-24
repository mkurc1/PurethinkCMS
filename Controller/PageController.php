<?php

namespace Purethink\CMSBundle\Controller;

use Knp\Component\Pager\Pagination\AbstractPagination;
use Purethink\CMSBundle\Entity\Article;
use Purethink\CMSBundle\Entity\Contact;
use Purethink\CMSBundle\Form\Type\ContactFormType;
use Purethink\CMSBundle\Repository\ArticleRepository;
use Purethink\CoreBundle\Entity\Repository\CategoryRepository;
use Purethink\CoreBundle\Entity\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Purethink\CMSBundle\Entity\Site;

class PageController extends Controller
{
    public function indexAction(Request $request)
    {
        /** @var Site $meta */
        $meta = $this->getMetadata();

        $page = $request->query->getInt('page', 1);

        $query = $this->getArticleRepository()
            ->getArticlesQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page);

        return $this->render('PurethinkCMSBundle:Page:index.html.twig', compact('meta', 'pagination'));
    }

    public function searchListAction(Request $request)
    {
        /** @var Site $meta */
        $meta = $this->getMetadata();

        $search = $request->query->get('query', '');
        $page = $request->query->getInt('page', 1);

        $query = $this->getArticleRepository()
            ->searchResultsQuery($request->getLocale(), $search);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page);

        return $this->render('PurethinkCMSBundle:Page:search_list.html.twig', compact('meta', 'pagination', 'search'));
    }

    public function archiveAction(Request $request, int $year, int $month)
    {
        /** @var Site $meta */
        $meta = $this->getMetadata();

        $page = $request->query->getInt('page', 1);

        $query = $this->getArticleRepository()
            ->getArticlesQuery($year, $month);

        $paginator = $this->get('knp_paginator');
        /** @var AbstractPagination $pagination */
        $pagination = $paginator->paginate($query, $page);

        if ($pagination->getTotalItemCount() === 0) {
            throw $this->createNotFoundException();
        }

        return $this->render('PurethinkCMSBundle:Page:archive.html.twig', compact('meta', 'pagination'));
    }

    public function tagAction(Request $request, string $slug)
    {
        $tag = $this->getTagRepository()->findActiveTagBySlug($slug);
        if (!$tag) {
            throw $this->createNotFoundException();
        }

        /** @var Site $meta */
        $meta = $this->getMetadata();

        $page = $request->query->getInt('page', 1);

        $query = $this->getArticleRepository()
            ->getArticlesWithTagQuery($tag);

        $paginator = $this->get('knp_paginator');
        /** @var AbstractPagination $pagination */
        $pagination = $paginator->paginate($query, $page);

        if ($pagination->getTotalItemCount() === 0) {
            throw $this->createNotFoundException();
        }

        return $this->render('PurethinkCMSBundle:Page:tag.html.twig', compact('meta', 'pagination', 'tag'));
    }

    public function categoryAction(Request $request, string $slug)
    {
        $category = $this->getCategoryRepository()->findActiveCategoryBySlug($slug);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        /** @var Site $meta */
        $meta = $this->getMetadata();

        $page = $request->query->getInt('page', 1);

        $query = $this->getArticleRepository()
            ->getArticlesWithCategoryQuery($category);

        $paginator = $this->get('knp_paginator');
        /** @var AbstractPagination $pagination */
        $pagination = $paginator->paginate($query, $page);

        if ($pagination->getTotalItemCount() === 0) {
            throw $this->createNotFoundException();
        }

        return $this->render('PurethinkCMSBundle:Page:category.html.twig', compact('meta', 'pagination', 'category'));
    }

    public function authorAction(Request $request, string $username)
    {
        $user = $this->get('fos_user.user_manager')->findUserByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        /** @var Site $meta */
        $meta = $this->getMetadata();

        $page = $request->query->getInt('page', 1);

        $query = $this->getArticleRepository()
            ->getUserArticlesQuery($user);

        $paginator = $this->get('knp_paginator');
        /** @var AbstractPagination $pagination */
        $pagination = $paginator->paginate($query, $page);

        if ($pagination->getTotalItemCount() === 0) {
            throw $this->createNotFoundException();
        }

        return $this->render('PurethinkCMSBundle:Page:author.html.twig', compact('meta', 'pagination', 'user'));
    }

    public function contactAction(Request $request)
    {
        /** @var Site $meta */
        $meta = $this->getMetadata();

        $contact = new Contact();
        $form = $this->createForm(new ContactFormType(), $contact);

        if ($request->isMethod('POST') && $form->submit($request) && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'info' => 'success',
                    'msg'  => 'Your message has been sent. Thank you!'
                ]);
            } else {
                $this->addFlash('success', 'flash.contact.success');

                return $this->redirectToRoute('purethink_cms_contact');
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'info' => 'error',
                'msg'  => 'Please fill out all fields'
            ]);
        } else {
            return $this->render('PurethinkCMSBundle:Page:contact.html.twig', [
                'meta' => $meta,
                'form' => $form->createView()
            ]);
        }
    }

    public function changeLocaleAction()
    {
        return $this->redirectToRoute('purethink_cms_homepage');
    }

    public function articleAction($slug = null)
    {
        $article = $this->getArticleRepository()->articleBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException();
        }

        $this->getDoctrine()->getRepository('PurethinkCMSBundle:ArticleView')
            ->incrementViews($article->getViews());

        /** @var Site $meta */
        $meta = $this->getMetadata();
        /** @var Article $prevArticle */
        $prevArticle = $this->getArticleRepository()->prevArticle($article);
        /** @var Article $nextArticle */
        $nextArticle = $this->getArticleRepository()->nextArticle($article);

        return $this->render('PurethinkCMSBundle:Page:article.html.twig',
            compact('meta', 'article', 'prevArticle', 'nextArticle'));
    }

    /**
     * @return Site
     */
    protected function getMetadata()
    {
        return $this->getDoctrine()
            ->getRepository('PurethinkCMSBundle:Site')
            ->getSite();
    }

    protected function getArticleRepository() : ArticleRepository
    {
        return $this->getDoctrine()->getRepository('PurethinkCMSBundle:Article');
    }

    protected function getCategoryRepository() : CategoryRepository
    {
        return $this->getDoctrine()->getRepository('PurethinkCoreBundle:Category');
    }

    protected function getTagRepository() : TagRepository
    {
        return $this->getDoctrine()->getRepository('PurethinkCoreBundle:Tag');
    }
}

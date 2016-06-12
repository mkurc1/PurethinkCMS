<?php

namespace Purethink\CMSBundle\Controller;

use A2lix\I18nDoctrineBundle\Annotation\I18nDoctrine;
use Purethink\CMSBundle\Entity\Article;
use Purethink\CMSBundle\Entity\Contact;
use Purethink\CMSBundle\Form\Type\ContactFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Purethink\CMSBundle\Entity\Site;

class PageController extends Controller
{
    /**
     * @Route("", name="page")
     * @Method("GET")
     * @I18nDoctrine
     */
    public function indexAction(Request $request)
    {
        /** @var Site $meta */
        $meta = $this->getMetadataByLocale($request->getLocale());

        return $this->render('PurethinkCMSBundle:Page:index.html.twig', compact('meta'));
    }

    /**
     * @Route("/search")
     * @Method("GET")
     * @I18nDoctrine
     */
    public function searchListAction(Request $request)
    {
        $locale = $request->getLocale();

        /** @var Site $meta */
        $meta = $this->getMetadataByLocale($locale);

        if ($search = $request->query->get('query')) {
            $entities = $this->getArticleRepository()
                ->searchResults($locale, $search);
        } else {
            $entities = null;
        }

        return $this->render('PurethinkCMSBundle:Page:searchList.html.twig', compact('meta', 'entities'));
    }

    /**
     * @Route("/contact", options={"expose"=true})
     * @Method("GET|POST")
     * @I18nDoctrine
     */
    public function contactAction(Request $request)
    {
        $locale = $request->getLocale();

        /** @var Site $meta */
        $meta = $this->getMetadataByLocale($locale);

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

                return $this->redirectToRoute('purethink_cms_page_contact');
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

    /**
     * @Route("/change-locale/{_locale}", name="change_locale")
     * @Method("GET")
     */
    public function changeLocaleAction()
    {
        return $this->redirectToRoute('page');
    }

    /**
     * @Route("/{slug}", name="article")
     * @ParamConverter("article", class="PurethinkCMSBundle:Article", options={
     *     "repository_method" = "articleBySlug",
     *     "map_method_signature" = true
     * })
     * @Method("GET")
     * @I18nDoctrine
     */
    public function articleAction(Article $article)
    {
        $this->getDoctrine()->getRepository('PurethinkCMSBundle:ArticleView')
            ->incrementViews($article->getViews());

        /** @var Article $prevArticle */
        $prevArticle = $this->getArticleRepository()->prevArticle($article);
        /** @var Article $nextArticle */
        $nextArticle = $this->getArticleRepository()->nextArticle($article);

        return $this->render('PurethinkCMSBundle:Page:article.html.twig',
            compact('article', 'prevArticle', 'nextArticle'));
    }

    /**
     * @param string $locale
     * @return Site
     */
    protected function getMetadataByLocale($locale)
    {
        return $this->getDoctrine()
            ->getRepository('PurethinkCMSBundle:Site')
            ->getSiteByLocale($locale);
    }

    protected function getArticleRepository()
    {
        return $this->getDoctrine()->getRepository('PurethinkCMSBundle:Article');
    }
}

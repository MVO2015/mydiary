<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DiaryEntry;
use AppBundle\Form\BaseDiaryEntryType;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\DiaryEntryRepository;

/**
 * Class DiaryController
 * @package AppBundle\Controller
 */
class DiaryController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function homeAction($request)
    {
        $this->get("app.logic")->init($request);
        return $this->render(
            'diary/home.html.twig'
        );
    }

    /**
     * @Route("/add", name="add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $entry = new DiaryEntry(new DateTime("now", new DateTimeZone("Europe/Prague")), "my diary entry", "diary");
        $form = $this->createForm(BaseDiaryEntryType::class, $entry);
        // buttons
        $form->add(
            'save',
            SubmitType::class,
            [
                'attr' => [
                    'class' => "btn btn-lg btn-success"
                ]
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $diaryEntry = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($entry);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Your entry "' . $diaryEntry->getNote() . '" has been saved!'
                );
            }
            return $this->redirectToRoute("index");
        }

        return $this->render('diary/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param int $id Diary entry id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var DiaryEntry $diaryEntry */
        $diaryEntry = $em->getRepository("AppBundle:DiaryEntry")->find($id);
        $tagChoices = $diaryEntry->getTempTags();
        $debug = print_r($tagChoices, true);
        $form = $this->createForm(BaseDiaryEntryType::class, $diaryEntry, ['tag_choices' => $tagChoices]);

        // buttons
        $form->add(
            'update',
            SubmitType::class,
            [
                'attr' => [
                    'class' => "btn btn-lg btn-success"
                ]
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('update')->isClicked()) {
                $diaryEntry = $form->getData();
                $em->flush();
                $this->addFlash(
                    'success',
                    'Your entry "' . $diaryEntry->getNote() . '" has been updated!'
                );
            }
            return $this->redirectToRoute("index");
        }
        if ($diaryEntry) {
            return $this->render('diary/form.html.twig', array(
                'form' => $form->createView(),
                'id' => $diaryEntry->getId(), // for Delete button
                // for Modal - delete confirmation
                'shortNote' => $diaryEntry->getShort(),
                'debug' => $debug
            ));
        }
        return $this->redirectToRoute("index");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param int $id Diary entry id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $diaryEntry = $em->getRepository("AppBundle:DiaryEntry")->find($id);
        if ($diaryEntry) {
            $em->remove($diaryEntry);
            $em->flush();
            $this->addFlash(
                'success',
                'Your entry "' . $diaryEntry->getNote() . '" has been deleted!'
            );
        }
        return $this->redirectToRoute("index");
    }

    /**
     * @Route("/index/{page}", name="index")
     * @return Response
     */
    public function indexAction($page = 1)
    {
        $limit = 10;
        $em = $this->getDoctrine()->getManager();
        /** @var DiaryEntryRepository $repository */
        $repository = $em->getRepository("AppBundle:DiaryEntry");
        $diaryEntries = $repository->getAllEntries($page, $limit);

        // You can also call the count methods (check PHPDoc for `paginate()`)
        // $totalEntriesReturned = $diaryEntries->getIterator()->count();

        # Count of ALL posts (ie: `20` posts)
        $totalEntries = $diaryEntries->count();

        # ArrayIterator
        $iterator = $diaryEntries->getIterator();

        $maxPages = ceil($totalEntries / $limit);
        $thisPage = $page;
        // Pass through the 3 above variables to calculate pages in twig
        return $this->render(
            'diary/index.html.twig',
            ['diaryEntries' => $iterator, 'maxPages' => $maxPages, 'thisPage' => $thisPage]
        );
    }

    /**
     * @Route("/show/{id}", name="show")
     * @param Request $request
     * @param int $id Diary entry id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var DiaryEntry $diaryEntry */
        $diaryEntry = $em->getRepository("AppBundle:DiaryEntry")->find($id);
//        $this->get('app.logic')->setActualPageNumber($request, $pageNumber);
        return $this->render('diary/ajax.html.twig', ['id' => $id]);
    }

    /**
     * Controller Index action with paginator
     * @Route("/paginate/{page}", name="paginate")
     * @param integer $page The current page passed via URL
     * @return Response
     */
    public function paginateAction($page = 1)
    {
        $limit = 1;
        $em = $this->getDoctrine()->getManager();
        /** @var DiaryEntryRepository $repository */
        $repository = $em->getRepository("AppBundle:DiaryEntry");
        $diaryEntries = $repository->getAllEntries($page, $limit);

        // You can also call the count methods (check PHPDoc for `paginate()`)
//        $totalEntriesReturned = $diaryEntries->getIterator()->count();

        # Count of ALL posts (ie: `20` posts)
        $totalEntries = $diaryEntries->count();

        # ArrayIterator
        $iterator = $diaryEntries->getIterator();

        $maxPages = ceil($totalEntries / $limit);
        $thisPage = $page;
        // Pass through the 3 above variables to calculate pages in twig
        return $this->render('diary/pagination.html.twig',
            ['diaryEntry' => $iterator[0], 'maxPages' => $maxPages, 'thisPage' => $thisPage]);
    }
}

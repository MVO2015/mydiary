<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DiaryEntry;
use AppBundle\Entity\Tag;
use AppBundle\Form\BaseDiaryEntryType;
use AppBundle\Form\DataTransformer\TagToIdTransformer;
use AppBundle\Form\DataTransformer\AddNewChoiceTransformer;
use AppBundle\Form\TagType;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\ArrayType;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function homeAction(Request $request)
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
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $tag_choices = $em->getRepository('AppBundle:Tag')->findAllAsChoiceArray();
        $entry = new DiaryEntry(new DateTime("now", new DateTimeZone("Europe/Prague")), "", "");
        $form = $this->createForm(BaseDiaryEntryType::class, $entry, ['tag_choices' => $tag_choices]);
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
                $entry=$form->getData();
                $em->persist($entry);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Your entry "' . $entry->getNote() . '" has been saved!'
                );
            }
            return $this->redirectToRoute("index");
        }

        return $this->render('diary/add.html.twig', array(
            'form' => $form->createView()
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
        $options = ['data_transformer' => new AddNewChoiceTransformer($em)];
        $form = $this->createForm(BaseDiaryEntryType::class, $diaryEntry, $options);

        $form->add(
            'update',
            SubmitType::class,
            [
                'attr' => [
                    'class' => "diarybtn"
                ]
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('update')->isClicked()) {
                $em->flush();
            }
            return $this->redirectToRoute("index");
        }
        if ($diaryEntry) {
            return $this->render('diary/edit.html.twig', array(
                'form' => $form->createView(),
                'shortNote' => $diaryEntry->getShort(),
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
        $diaryEntries = $repository->getAllEntries($page, $limit, "DESC");

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
            [
                'diaryEntries' => $iterator,
                'maxPages' => $maxPages,
                'thisPage' => $thisPage,
                'limit' => $limit,
            ]
        );
    }

    /**
     * @Route("/show/offset/{offset}", name="show_by_offset")
     * @param Request $request
     * @param int $offset Diary entry offset
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function showByOffsetAction(Request $request, $offset)
    {
        $maxOffset =  $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->getMaxOffset();
        return $this->render('diary/ajax.html.twig', ['offset' => $offset, 'maxOffset' => $maxOffset]);
    }

    /**
     * Controller Index action with paginator
     * @Route("/paginate/{page}", name="paginate")
     * @param Request $request
     * @param integer $page The current page passed via URL
     * @return Response
     */
    public function paginateAction(Request $request, $page = 1)
    {
        $limit = 1;
        $em = $this->getDoctrine()->getManager();
        /** @var DiaryEntryRepository $diaryEntryRepository */
        $diaryEntryRepository = $em->getRepository("AppBundle:DiaryEntry");
        $diaryEntries = $diaryEntryRepository->getAllEntries($page, $limit);

        // You can also call the count methods (check PHPDoc for `paginate()`)
//        $totalEntriesReturned = $diaryEntries->getIterator()->count();

        # Count of ALL posts (ie: `20` posts)
        $totalEntries = $diaryEntries->count();

        # ArrayIterator
        $iterator = $diaryEntries->getIterator();
        /** @var DiaryEntry $diaryEntry */
        $id = $iterator[0]->getId();
        $diaryEntry = $diaryEntryRepository->find($id);

        $maxPages = ceil($totalEntries / $limit);
        $thisPage = $page;

        // Pass through the 3 above variables to calculate pages in twig
        return $this->render('diary/pagination.html.twig',
            ['diaryEntry' => $diaryEntry,
                'maxPages' => $maxPages,
                'thisPage' => $thisPage,
                'tags' => $diaryEntry->getTags(),
            ]);
    }

    /**
     * @Route("/delete_tag/{diaryEntryId}/{tagId}", name="delete_tag")
     * @param int $diaryEntryId Diary entry id
     * @param int $tagId Tag Id
     * @return string
     */
    public function deleteTagAction($diaryEntryId, $tagId)
    {
        $em = $this->getDoctrine()->getManager();
        $diaryEntry = $em->getRepository("AppBundle:DiaryEntry")->find($diaryEntryId);
        if ($diaryEntry) {
            $diaryEntry->removeTag($tagId);
            $em->persist($diaryEntry);
            $em->flush();
        } else {
            return new View("Diary entry not found", Response::HTTP_NOT_FOUND);
        }
        return $this->redirectToRoute('paginate');
    }
}

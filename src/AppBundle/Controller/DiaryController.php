<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DiaryEntry;
use AppBundle\Form\DiaryEntryType;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function diaryAction(Request $request)
    {
        $session = $request->getSession();
        $diaryEntry = new DiaryEntry(time(), "testovací text", "testovací_kategorie");
        return $this->render(
            'diary/diary.html.twig',
            ['note' => $diaryEntry->getNote(), 'dateTime' => $diaryEntry->getDateTime()]
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
        $form = $this->createForm(DiaryEntryType::class, $entry);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $diaryEntry = $form->getData();
            $this->addFlash(
                'success',
                'Your entry "' . $diaryEntry->getNote() . '" was saved!'
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($entry);
            $em->flush();

            return $this->redirectToRoute("index");
        }

        return $this->render('diary/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/success", name="diary_success")
     * @param Request $request
     * @return Response
     */
    public function successAction(Request $request)
    {
        return $this->render('diary/success.html.twig');
    }

    /**
     * @Route("/update", name="update")
     * @param Request $request
     * @param int $id Diary entry id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $diaryEntry = $em->getRepository("AppBundle:DiaryEntry")->find($id);
        $form = $this->createForm(DiaryEntryType::class, $diaryEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diaryEntry = $form->getData();
            $this->addFlash(
                'success',
                'Your entry "' . $diaryEntry->getNote() . '" was updated!'
            );
            $em->flush();

            return $this->redirectToRoute("index");
        }
        return $this->render('diary/add.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    public function deleteAction(Request $request, $id)
    {

    }

    public function showAction(Request $request, $id)
    {

    }

    /**
     * @Route("/index", name="index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $diaryEntries = $em->getRepository('AppBundle:DiaryEntry')->findAll();
        return $this->render(
            'diary/index.html.twig',
            ['diaryEntries' => $diaryEntries]
        );
    }
}
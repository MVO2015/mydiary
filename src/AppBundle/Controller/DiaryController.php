<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DiaryEntry;
use AppBundle\Form\AddDiaryEntryType;
use AppBundle\Form\EditDiaryEntryType;
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
        $form = $this->createForm(AddDiaryEntryType::class, $entry);

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
        $diaryEntry = $em->getRepository("AppBundle:DiaryEntry")->find($id);
        $form = $this->createForm(EditDiaryEntryType::class, $diaryEntry);
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
            if ($form->get('delete')->isClicked()) {
                $diaryEntry = $form->getData();
                $em->remove($diaryEntry);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Your entry "' . $diaryEntry->getNote() . '" has been deleted!'
                );
            }
            return $this->redirectToRoute("index");
        }
        return $this->render('diary/edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $diaryEntry->getId(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Request $request
     * @param int $id Diary entry id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $diaryEntry = $em->getRepository("AppBundle:DiaryEntry")->find($id);
        $form = $this->createForm(EditDiaryEntryType::class, $diaryEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diaryEntry = $form->getData();
            $em->remove($diaryEntry);
            $em->flush();
            $this->addFlash(
                'success',
                'Your entry "' . $diaryEntry->getNote() . '" has been deleted!'
            );
            return $this->redirectToRoute("index");
        }
        return $this->render('diary/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param $id
     */
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
        $diaryEntries = $em->getRepository('AppBundle:DiaryEntry')->findAllDesc();
        return $this->render(
            'diary/index.html.twig',
            ['diaryEntries' => $diaryEntries]
        );
    }
}

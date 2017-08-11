<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Repository\TagRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TagController
 * @package AppBundle\Controller
 */
class TagController extends Controller
{
    /**
     * @Route(
     *     "/tag/index/{orderBy}/{sort}",
     *     name="tag_index",
     *     defaults={"orderBy": "text", "sort": "asc"},
     *     requirements={"orderBy": "id|text", "sort": "asc|desc"},
     *     )
     * @param Request $request
     * @param string $orderBy Database column for order by clause
     * @param string $sort sorting parameter of database order by clause
     * @return Response
     */
    public function indexAction(Request $request, $orderBy = "text", $sort = "asc")
    {
        $user = $this->getUser();
        if ($user) {
            $userId = $user->getId();
            $em = $this->getDoctrine()->getManager();
            $tags = $em->getRepository("AppBundle:Tag")->findAllByUserOrderBy($userId, $orderBy, $sort);
            return $this->render("tag/index.html.twig", ['tags' => $tags]);
        }
        return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route("/tag/edit/{id}", name="tag_edit")
     * @param Request $request
     * @param int $id Id of the tag
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user) {
            $userId = $user->getId();
            $em = $this->getDoctrine()->getManager();
            /** @var Tag $tag */
            $tag = $em->getRepository("AppBundle:Tag")->findByUserAndId($userId, $id);
            if (!$tag) {
                return $this->redirectToRoute('homepage');
            }
            $form = $this->createForm(TagType::class, $tag);

            // buttons
            $form->add(
                'update',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => "diarybtn"
                    ]
                ]);

            $form->handleRequest($request);

            // submit form
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('update')->isClicked()) {
                    $tag = $form->getData();
                    $tag->setUserId($userId);
                    $em->flush();
                    $this->addFlash(
                        'success',
                        'Your tag "' . $tag->getText() . '" has been updated!'
                    );
                }
                return $this->redirectToRoute("tag_index");
            }
            if ($tag) {
                return $this->render('tag/edit.html.twig', array(
                    'form' => $form->createView(),
                    'id' => $tag->getId(), // for Delete button
                ));
            } else {
                return $this->redirectToRoute('homepage');
            }
        }
        return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route("/tag/delete/{id}", name="tag_delete")
     * @param Request $request
     * @param int $id Id of the tag
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user) {
            $userId = $user->getId();
            $this->deleteTag($userId, $id);
            return $this->redirectToRoute("tag_index");
        }
        return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route("/tag/collection/delete/element/{id}", name="tag_delete_element")
     * @param Request $request
     * @param int $id Id of the tag
     * @return Response
     */
    public function deleteElementAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user) {
            $userId = $user->getId();
            if ($this->deleteTag($userId, $id)) {
                return $this->redirectToRoute("tag_new");
            } else {
                return $this->redirectToRoute('homepage');
            }
        }
        return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route(
     *     "/tag/collection/{orderBy}/{sort}",
     *     name="tag_collection",
     *     defaults={"orderBy": "id", "sort": "asc"},
     *     requirements={"orderBy": "id|text", "sort": "asc|desc"},
     *     )
     * @param Request $request
     * @param string $orderBy Database column for order by clause
     * @param string $sort sorting parameter of database order by clause
     * @return Response
     */
    public function collectionAction(Request $request, $orderBy = "text", $sort = "asc")
    {
        $user = $this->getUser();
        if ($user) {
            $userId = $user->getId();
            $em = $this->getDoctrine()->getManager();
            $tags = $em->getRepository("AppBundle:Tag")->findAllByUserOrderBy($userId, $orderBy, $sort);
            return $this->render("tag/collection.page.html.twig", ['tags' => $tags]);
        } else {
            return $this->$this->redirectToRoute();
        }
    }

    /**
     * Delete tag from repository
     * @param int $userId user id
     * @param int $id tag id
     * @return bool True if success, otherwise false.
     */
    private function deleteTag($userId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Tag $tag */
        $tag = $em->getRepository("AppBundle:Tag")->findByUserAndId($userId, $id);
        if ($tag) {
            $em->remove($tag);
            $em->flush();
            return true;
        }
        return false;
    }

    /**
     * @Route("/tag/new", name="tag_new")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $orderBy = "text";
        $user = $this->getUser();
        if ($user) {
            $userId = $user->getId();
            $em = $this->getDoctrine()->getManager();
            $tags = $em->getRepository("AppBundle:Tag")->findAllByUserOrderBy($userId, $orderBy);
            $form = $this->createForm(TagType::class);
            // buttons
            $form->add(
                'add',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => "diarybtn"
                    ]
                ]);

            $form->handleRequest($request);
            // submit form
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('add')->isClicked()) {

                    /** @var Tag $tagFromForm */
                    $tagFromForm =$form->getData();
                    if (!$em->getRepository("AppBundle:Tag")->findOneByUserAndText($userId, $tagFromForm->getText())) {
                        $tagFromForm->setUserId($userId);
                        $em->persist($tagFromForm);
                        $em->flush();
                        $tags = $em->getRepository("AppBundle:Tag")->findAllByUserOrderBy($userId, $orderBy);
                    }
                }
            }

            return $this->render(
                ":tag:add.form.html.twig",
                [
                    "form" => $form->createView(),
                    "tags" => $tags
                ]);
        }
        return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route("/collapse", name="collapse")
     * @param Request $request
     * @return Response
     */
    public function collapseAction(Request $request)
    {
        $user = $this->getUser();
        if ($user) {
            $userId = $user->getId();

            $em = $this->getDoctrine()->getManager();
            $tags = $em->getRepository("AppBundle:Tag")->findAllByUserOrderBy($userId, "text", "ASC");
            return $this->render(
                ":tag:collapse_diary.html.twig",
                [
                    'tags' => $tags
                ]
            );
        }
        return $this->redirectToRoute('fos_user_security_login');
    }
}

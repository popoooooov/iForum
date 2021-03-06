<?php

/*
 * This file is part of the "php-paradise/array-keys-converter" package.
 * (c) Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\Topics;
use App\Form\EditTopicForm;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AddTopic;

class TopicController extends Controller
{

    /**
     * This method allow u to delete Topic and it's messages
     *
     * First we delete messages and then topic
     *
     * @var topic has topic that will be deleted
     * @var message has messages from $topic, they will be deleted too
     *
     * @Route("/delete/topic/{id}", name="delete_topic")
     *
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTopic($id)
    {
        $em = $this->getDoctrine()->getManager();

        $topic = $em->getRepository(Topics::class)->findOneBy(['id'=>$id]);
        $messages = $em->getRepository(Messages::class)->findBy(['topics'=>$id]);

        if (empty($topic) or !$topic->IsAuthorOf($topic, $this->getUser())) {
            return $this->render('error.html.twig');
        }

        $topic->setLastMessage(null);

        foreach ($messages as $message) {
            $em->remove($message);
        }
        $em->flush();

        $em->remove($topic);
        $em->flush();

        return $this->redirectToRoute('form', ['id'=>$topic->getSection()->getId()]);
    }

    /**
     * This method allow u to edit topic
     *
     * @var topic has topic that will be edited
     * @var form has form that u'll fill
     *
     * @Route("/edit/topic/{id}", name="edit_topic")
     *
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function EditTopic(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $topic = $em->getRepository(Topics::class)->find($id);
        $form = $this->createForm(EditTopicForm::class, $topic);

        if (empty($topic) or !$topic->IsAuthorOf($topic, $this->getUser())) {
            return $this->render('error.html.twig');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('form', ['id'=>$topic->getSection()->getId()]);
        }
        $form->handleRequest($request);

        return $this->render('topics/editTopic.html.twig', ['form'=>$form->createView()]);
    }

    /**
     *  This method allow u to add topic if u're authorized
     *
     * @var form has form u'll need to fill in
     *
     * @Route("/add_topic/{id}", name="add_topic")
     */
    public function addTopic(Request $request, $id=null)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = new Topics();
        $user->setAuthor($this->getUser());
        $form = $this->createForm(AddTopic::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity_manager = $this->getDoctrine()->getManager();
            $entity_manager->persist($user);
            $entity_manager->flush();
            $last_id = $user->getId();

            return $this->redirectToRoute('list', ['id'=>$last_id]);
        }
        //$this->addSql("INSERT INTO sections (name) VALUES ('Авто'),('Кино'),('Музыка'),('ИТ'),('Бизнес')");
        return $this->render('topics/addTopic.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * This method shows all topics in chosen section, also there is included pagination
     *
     * @var topics has topics in chosen category
     * @var $paginator \Knp\Component\Pager\Paginator call pagination
     * @var result create pagination with chosen options
     *
     * @Route("/section/{id}", name="form")
     *
     * @return Response
     */
    public function showTopics($id, UserService $service, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $topics = $em->getRepository(Topics::class)->findBy(['section'=>$id]);

        
        $paginator  = $this->get('knp_paginator');

        $result = $paginator->paginate(
            $topics,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 7)
        );

        $userId = $service->getUserId();

        return $this->render('topics/topics.html.twig', ['topics'=>$result, 'id'=>$id,'userId'=>$userId, 'result'=>$result]);
    }

    /**
     * This method allow u to close topic if u have enough rights
     *
     * @var topic has topic that will be closed
     *
     * @Route("/close_topic/{id}", name="close")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function CloseTopic($id)
    {
        $em = $this->getDoctrine()->getManager();

        $topic = $em->getRepository(Topics::class)->findOneBy(['id'=>$id]);

        if (empty($topic) or !$topic->IsAuthorOf($topic, $this->getUser())) {
            return $this->redirectToRoute('error.html.twig');
        }

        $topic->setClose(true);

        $em->flush();

        return $this->redirectToRoute('form', ['id'=>$topic->getSection()->getId()]);
    }
}

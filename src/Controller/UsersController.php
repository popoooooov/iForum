<?php

/*
 * This file is part of the "php-paradise/array-keys-converter" package.
 * (c) Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Form\ChangeAvatarForm;
use App\Form\ChangePasswordForm;
use App\Form\EditUserForm;
use App\Model\Avatar;
use App\Model\ChangePassword;
use App\Model\UserEdit;
use App\Service\FileSystem\FileManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Users;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends Controller
{
    /**
     * This method allow u to change your userName and email
     *
     * @var newUser create and User exemplar
     * @var form create editing form
     *
     * @Route("/edit/user", name="edit_user")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function EditUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->findOneBy(['id'=>$this->getUser()->getId()]);

        $newUser = new UserEdit();
        $newUser->setUserName($user->getUsername());
        $newUser->setEmail($user->getEmail());

        $form = $this->createForm(EditUserForm::class, $newUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($newUser->getUserName());
            $user->setEmail($newUser->getEmail());

            $em->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('users/edituser.html.twig', ['form'=>$form->createView()]);
    }


    /**
     * This method show your profile if u're authorized
     *
     * @var user has your user info
     *
     * @Route("/profile", name="profile")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ShowUser()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository = $this->getDoctrine()->getRepository(Users::class);
        $user = $repository->findOneBy(['id'=>$this->getUser()->getId()]);

        return $this->render('users/user.html.twig', ['user'=>$user]);
    }

    /**
     * This method allow u to change your profile avatar
     *
     * @var form create form
     *
     * @Route("/change_avatar", name="changeAvatar")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ChangeImage(Request $request, FileManager $fileManager)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->findOneBy(['id'=>$this->getUser()->getId()]);

        $newUser = new Avatar();

        $form = $this->createForm(ChangeAvatarForm::class, $newUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($fileName = $fileManager->upload($newUser->getImage())) {
                $user->setImage($fileName);
            }
            $em->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('users/changeAvatar.html.twig', ['form' => $form->createView()]);
    }

    /**
     * This method allow u to change password
     *
     * @var user has your user info
     * @var form create form where u have to write your old and new password
     *
     * @Route("/change_password", name="changePassword")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ChangePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->findOneBy(['id'=>$this->getUser()->getId()]);

        $newUser = new ChangePassword();

        $form = $this->createForm(ChangePasswordForm::class, $newUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $newUser->getNewPassword());
            $user->setPassword($password);

            $em->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('users/changePassword.html.twig', ['form'=>$form->createView()]);
    }
}

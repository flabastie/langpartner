<?php
// src/LP/UserBundle/Controller/ManagerController.php;

namespace LP\UserBundle\Controller;

use LP\UserBundle\Entity\User;
use LP\UserBundle\Form\UserType;
use LP\UserBundle\Form\UserFullType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;


class UserManageController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction UserManagerAction
 * ---------------------------------------------------------------------------------------------------- */

  public function listUserAction(Request $request)
  {

    // recup session 
    $session = $this->getRequest()->getSession();

    // recup users
    $users  = $this ->getDoctrine()
                    ->getManager()
                    ->getRepository('LPUserBundle:User')
                    ->findAll();

    // rendering
    return $this->render('LPUserBundle:User:list-user.html.twig', array(
      'users' => $users
    ));

  }


/* ------------------------------------------------------------------------------------------------------
 *      fonction editUserAction
 * ---------------------------------------------------------------------------------------------------- */

  public function editUserAction(Request $request, $id)
  {

    if ($id < 1) 
    {
      $request->getSession()->getFlashBag()->add('info', 'Id user not found !');
      // redirection
      return $this->redirect($this->get('router')->generate('lp_user_list'));
    }

    // recup user
    $userToEdit  = $this  ->getDoctrine()
                          ->getManager()
                          ->getRepository('LPUserBundle:User')
                          ->find($id);

    if ($userToEdit == null) 
    {
      $request->getSession()->getFlashBag()->add('info', 'User not found !');
      return $this->redirect($this->get('router')->generate('lp_user_list'));
    }

    $form = $this->get('form.factory')->create(new UserFullType(), $userToEdit);

    // form treatment  ------------------------------------------------------------------------------------

    if ($request->isMethod('POST')) 
    {
        $form ->bind($request);
        $data = $form->getData();
        $valid = false;

      if (isset($_POST)) 
      {

        // username
        if (isset($_POST['username']) && !empty($_POST['username'])) 
        {
          $userToEdit->setUsername($_POST['username']);
          $valid = true;
        }
        else{
              $valid = false;
            }  

        // useremail
        if (isset($_POST['useremail']) && !empty($_POST['useremail'])) 
        {
          $userToEdit->setUseremail($_POST['useremail']);
          $valid = true;
        }
        else{
              $valid = false;
            } 

        // password
        if (isset($_POST['password']) && !empty($_POST['password'])) 
        {
          $userToEdit->setPassword($_POST['password']);
          $valid = true;
        }
        else{
              $valid = false;
            } 

        // rolesCheckbox
        if (isset($_POST['role_radio']) && !empty($_POST['role_radio'])) 
        {
          if (($userToEdit->getId() == 1) && ($_POST['role_radio'] != 'ROLE_ADMIN')) {
            $userToEdit->setRoles(array('ROLE_ADMIN'));
            $request->getSession()->getFlashBag()->add('info', 'Admin account cant\'t delete ROLE_ADMIN !');
          }
          else {
            $userToEdit->setRoles(array($_POST['role_radio']));
          }
          $valid = true;
        }
        else{
              $valid = false;
            } 
      }

      if ($valid ) 
      {
        $em = $this->getDoctrine()->getManager();
        $em->persist($userToEdit);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info', 'User well modified.');
        return $this->redirect($this->generateUrl('lp_user_list'));
      }
      else{
        $request->getSession()->getFlashBag()->add('info', 'User form error !');
        return $this->redirect($this->generateUrl('lp_user_list'));
      }

    }

    // rendering
    return $this->render('LPUserBundle:User:edit-user.html.twig', array(
      'form'        => $form->createView(),
      'userToEdit'  => $userToEdit
    ));

  }



/* ------------------------------------------------------------------------------------------------------
 *      fonction createUserAction
 * ---------------------------------------------------------------------------------------------------- */

  public function createUserAction(Request $request)
  {

    // creation user
    $userToCreate  = new User();

    $form = $this->get('form.factory')->create(new UserFullType(), $userToCreate);

    // form treatment  ------------------------------------------------------------------------------------

    if ($request->isMethod('POST')) 
    {
        $form ->bind($request);
        $data = $form->getData();
        $valid = false;

      if (isset($_POST)) 
      {

        // username
        if (isset($_POST['username']) && !empty($_POST['username'])) 
        {
          $userToCreate->setUsername($_POST['username']);
          $valid = true;
        }
        else{
              $valid = false;
            }  

        // useremail
        if (isset($_POST['useremail']) && !empty($_POST['useremail'])
        && preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST['useremail']))
        {
          $userToCreate->setUseremail($_POST['useremail']);
          $valid = true;
        }
        else{
              $valid = false;
            } 

        // password
        if (isset($_POST['password']) && !empty($_POST['password'])) 
        {
          $userToCreate->setPassword($_POST['password']);
          $valid = true;
        }
        else{
              $valid = false;
            } 

        // rolesCheckbox
        if (isset($_POST['role_radio']) && !empty($_POST['role_radio'])) 
        {
          $userToCreate->setRoles(array($_POST['role_radio']));
          $valid = true;
        }
        else{
              $valid = false;
            } 
      }

      if ($valid ) 
      {

        // verifications --------------------------------------------------------------------------------

        // recup users
        $users  = $this   ->getDoctrine()
                          ->getManager()
                          ->getRepository('LPUserBundle:User')
                          ->findAll();

        // loop users verif doublons
        foreach ($users as $user) 
        {
          if ($user->getUsername() === $userToCreate->getUsername()) {
            $request->getSession()->getFlashBag()->add('info', 'User name already used.');
            return $this->redirect($this->generateUrl('lp_user_list'));
          }
          if ($user->getUseremail() === $userToCreate->getUseremail()) {
            $request->getSession()->getFlashBag()->add('info', 'User email already used');
            return $this->redirect($this->generateUrl('lp_user_list'));
          }
        }

        // flush userTo --------------------------------------------------------------------------------

        $activation = md5(uniqid(rand(), true));      // activation code
        $userToCreate ->setAuth($activation);  
        $userToCreate ->setSalt("");                    // salt

        $em = $this->getDoctrine()->getManager();
        $em->persist($userToCreate);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info', 'User well created.');
        //return $this->redirect($this->generateUrl('lp_user_list'));
      }
      else{
        $request->getSession()->getFlashBag()->add('info', 'User form error !');
        return $this->redirect($this->generateUrl('lp_user_list'));
      }

    }

    // rendering
    return $this->render('LPUserBundle:Registration:create-user.html.twig', array(
      'form'        => $form->createView()
    ));

  }


}



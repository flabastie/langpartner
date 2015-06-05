<?php
// src/LP/UserBundle/Controller/ManagerController.php;

namespace LP\UserBundle\Controller;

use LP\UserBundle\Entity\User;
use LP\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;


class ManagerController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction UserManagerAction
 * ---------------------------------------------------------------------------------------------------- */

  public function UserManagerAction(Request $request)
  {

    // recup session 
    $session = $this->getRequest()->getSession();

    // recup users
    $users  = $this ->getDoctrine()
                    ->getManager()
                    ->getRepository('LPUserBundle:User')
                    ->findAll();
/*
    // recup get vars ----------------------------------------------------------------------------------

    if (isset($_GET['email']) && !empty($_GET['email'])) 
    {
      $session->set('userEmail', $_GET['email']);
    } 

    if (isset($_GET['key']) && !empty($_GET['key'])) {
      $session->set('key', $_GET['key']);
    }

    // form  -------------------------------------------------------------------------------------------

    $data = array();
    $form = $this   ->createFormBuilder($data)
                    ->add('password', 'text')
                    ->add('password2', 'text')       
                    ->add('save', 'submit')
                    ->getForm();

    if ($request->isMethod('POST')) 
    {
        $form ->bind($request);
        $data = $form->getData();

        if ($session->get('key') && $session->get('userEmail')) 
        {
          $userEmail = $session->get('userEmail');
          $key = $session->get('key');

          // recup user
          $user  = $this  ->getDoctrine()
                          ->getManager()
                          ->getRepository('LPUserBundle:User')
                          ->findOneBy(array('useremail' => $userEmail));

          if ($user->getAuth() === $key) 
          {
            // user found ok
            if ($data['password'] === $data['password2']) 
            {
              // ok identical passwords
              $user->setPassword($data['password']);

              // persist & flush
              $em = $this->getDoctrine()->getManager();
              $em->persist($user);
              $em->flush();

              $request->getSession()->getFlashBag()->add('info', 'Congratulation ! Your password was changed.' );
              return $this->redirect($this->generateUrl('login'));
            }
            else 
            {
              // passwords are different
              $request->getSession()->getFlashBag()->add('info', 'Error password match !' );
            }

          }
          else {
            $request->getSession()->getFlashBag()->add('info', 'ERROR ! User not found !' );
          }
        }

    }

    // rendering
    return $this->render('LPUserBundle:Security:usermanager.html.twig', array(
      'form' => $form->createView()
    ));
*/
    // rendering
    return $this->render('LPUserBundle:User:list-user.html.twig', array(
      'users' => $users
    ));

  }


}



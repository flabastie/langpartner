<?php
// src/LP/UserBundle/Controller/ResetController.php;

namespace LP\UserBundle\Controller;

use LP\UserBundle\Entity\User;
use LP\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;


class ResetController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction resetAction
 * ---------------------------------------------------------------------------------------------------- */

  public function resetAction(Request $request)
  {

    $userEmail    = null;
    $userName     = null;
    $userByName   = null;
    $userByEmail  = null;
    $emailToSend  = null;
    $auth         = null;

    // searchform  -------------------------------------------------------------------------------------

    $data = array();
    $form = $this   ->createFormBuilder($data)
                    ->add('username')
                    ->add('useremail')          
                    ->add('save', 'submit')
                    ->getForm();

  // treatment -----------------------------------------------------------------------------------------

  if ($request->isMethod('POST')) 
  {
    
    if (isset($_POST['_username']) && !empty($_POST['_username'])) // username treatment
    {
      $userName = $_POST['_username'];

      // recup user by username
      $userByName   = $this  ->getDoctrine()
                    ->getManager()
                    ->getRepository('LPUserBundle:User')
                    ->findOneBy(array('username' => $userName));

      if ($userByName != null) 
      {
        $emailToSend = $userByName->getUseremail();
        $auth = $userByName->getAuth();
      }

    }

    if (isset($_POST['_email']) && !empty($_POST['_email'])) // email treatment
    {
      $userEmail = $_POST['_email'];

      // recup user by useremail
      $userByEmail  = $this  ->getDoctrine()
                    ->getManager()
                    ->getRepository('LPUserBundle:User')
                    ->findOneBy(array('useremail' => $userEmail));

      if ($userByEmail != null) 
      {
        $emailToSend = $userByEmail->getUseremail();
        $auth = $userByEmail->getAuth();
      }

    }

    if ($userByName && $userByEmail)
    {
      if ($userByName->getAuth() != $userByEmail->getAuth()) 
      {
        $request->getSession()->getFlashBag()->add('info', 'Warning ! User name & user mail don\'t match !' );
        $emailToSend = $userByEmail->getUseremail();
        $auth = $userByEmail->getAuth();
      }
      else 
      {
        $emailToSend = $userByEmail->getUseremail();
        $auth = $userByEmail->getAuth();
      }

    }

    if ($emailToSend && $auth) 
    {
        $subject = 'Language Partner reset password';
        $siteUrl = $this->container->getParameter('site_url');
        $text    = "To reset your password, please click on this link :\n\n";
        $text   .= $siteUrl . 'changepwd?email=' . urlencode($emailToSend) . "&key=$auth";

        $mailService = $this->container->get('lp_user.mail'); // recup mail service
        $sendEmail   = $mailService->sendMail($emailToSend, $subject, $text); // sending mail
        $request     ->getSession()->getFlashBag()->add('info', 'A reset link was sent to your email !'); // confirmation message
    }
    else
    {
        $request->getSession()->getFlashBag()->add('info', 'Sorry ! No user was found !' );
    }

  }

    // rendering
    return $this->render('LPUserBundle:Security:reset.html.twig', array(
    'form' => $form->createView()
    ));

  }


/* ------------------------------------------------------------------------------------------------------
 *      fonction changePasswordAction
 * ---------------------------------------------------------------------------------------------------- */

  public function changePasswordAction(Request $request)
  {

    // recup session 
    $session = $this->getRequest()->getSession();

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
    return $this->render('LPUserBundle:Reset:change.html.twig', array(
      'form' => $form->createView()
    ));

  }


}



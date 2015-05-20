<?php
// src/LP/UserBundle/Controller/SecurityController.php;

namespace LP\UserBundle\Controller;

use LP\UserBundle\Entity\User;
use LP\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;


class SecurityController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction loginAction
 * ---------------------------------------------------------------------------------------------------- */

  public function loginAction(Request $request)
  {
    // Si le visiteur est déjà identifié, on le redirige vers l'accueil
    if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
      return $this->redirect($this->generateUrl('lp_partner_member_list'));
    }

    $session = $request->getSession();

    // On vérifie s'il y a des erreurs d'une précédente soumission du formulaire
    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
      $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
    } else {
      $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
      $session->remove(SecurityContext::AUTHENTICATION_ERROR);
    }

    return $this->render('LPUserBundle:Security:login.html.twig', array(
      // Valeur du précédent nom d'utilisateur entré par l'internaute
      'last_username' => $session->get(SecurityContext::LAST_USERNAME),
      'error'         => $error,
    ));
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction registerAction
 * ---------------------------------------------------------------------------------------------------- */

  public function registerAction(Request $request)
  {
    // if authentified => redirect
    if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) 
    {
      return $this->redirect($this->generateUrl('lp_partner_member_list'));
    }

    // recup user
    $session = $request->getSession();
    $newUser = new User();

    // form creation
    $form = $this->get('form.factory')->create(new UserType(), $newUser);

    // form treatment --------------------------------------------------------------------------------

    if ($request->isMethod('POST')) {

      if (isset($_POST['form'])) 
      {

        $valid = true;
   
        if (isset($_POST['form']['username'])  && !empty($_POST['form']['username'])) {
          $newUser->setUsername($_POST['form']['username']);
        }
        else{
          $valid = false;
        }  

        if (isset($_POST['form']['useremail'])  && !empty($_POST['form']['useremail'])
          && preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST['form']['useremail'])) {
          $newUser->setUseremail($_POST['form']['useremail']);
        }
        else{
          $valid = false;
          $request->getSession()->getFlashBag()->add('info', 'Email error !');
          return $this->render('LPUserBundle:Security:register.html.twig', array(
            'form' => $form->createView(),
          ));
        }  

        if ( isset($_POST['form']['password'])  && !empty($_POST['form']['password'])
          && isset($_POST['form']['password_bis'])  && !empty($_POST['form']['password_bis'])
            && $_POST['form']['password'] === $_POST['form']['password_bis']) 
        {
          $newUser->setPassword($_POST['form']['password']);
        }
        else{
          $valid = false;
        }  

        // 
        if ($valid) {

          $activation = md5(uniqid(rand(), true));      // activation code
          $newUser    ->setAuth($activation);  
          $newUser    ->setSalt("");                    // salt
          $newUser    ->setRoles(array('ROLE_USER'));   // ROLE_USER definition

          // verifications --------------------------------------------------------------------------------

          // recup users
          $users  = $this   ->getDoctrine()
                            ->getManager()
                            ->getRepository('LPUserBundle:User')
                            ->findAll();

          // loop users verif doublons
          foreach ($users as $user) 
          {
            if ($user->getUsername() === $newUser->getUsername()) {
              $request->getSession()->getFlashBag()->add('info', 'User name already used.');
              return $this->redirect($this->generateUrl('register'));
            }
            if ($user->getUseremail() === $newUser->getUseremail()) {
              $request->getSession()->getFlashBag()->add('info', 'User email already used');
              return $this->redirect($this->generateUrl('register'));
            }
          }

          // persist & flush
          $em = $this->getDoctrine()->getManager();
          $em->persist($newUser);
          $em->flush();

          // Send the email --------------------------------------------------------------------------------

          // recup mail service
          //$mailService = $this->container->get('lp_user.mail');  

          $email   = $_POST['form']['useremail'];
          $subject = 'Language Partner : Registration confirmation';

          $siteUrl = $this->container->getParameter('site_url');

          $text    = "To activate your account, please click on this link :\n\n";
          //$text   .= 'http://flab-image.com/activate?email=' . urlencode($email) . "&key=$activation";
          $text   .= $siteUrl . 'activate?email=' . urlencode($email) . "&key=$activation";

          // recup mail service
          $mailService = $this->container->get('lp_user.mail'); 
          // sending mail
          $sendEmail = $mailService->sendMail($email, $subject, $text);

          $request->getSession()->getFlashBag()->add('info', 'Thank you for registering ! A confirmation email has been sent to ' . $email);

          return $this->render('LPUserBundle:Security:register.html.twig', array(
            'form' => $form->createView(),
          ));

        }
        else {
          $request->getSession()->getFlashBag()->add('info', 'Registration error !');
        }

      return $this->redirect($this->generateUrl('lp_partner_homepage'));
      }

    } // end form treatment

    return $this->render('LPUserBundle:Security:register.html.twig', array(
      'form' => $form->createView(),
    ));

  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction activateAction
 * ---------------------------------------------------------------------------------------------------- */

  public function activateAction(Request $request)
  {

    $valid      = false;
    $userEmail  = null;
    $key        = null;

    // form treatment -------------------------------------------------------------------------------

    if (isset($_GET['email']) && !empty($_GET['email'])) 
    {
      $userEmail = $_GET['email'];
      $valid = true;
    }
    else{
      $valid = false;
    }  

    if (isset($_GET['key']) && !empty($_GET['key'])) {
      $key = $_GET['key'];
      $valid = true;
    }
    else{
      $valid = false;
    }  

    // verifications --------------------------------------------------------------------------------

    // recup user
    $user  = $this  ->getDoctrine()
                    ->getManager()
                    ->getRepository('LPUserBundle:User')
                    ->findOneBy(array('useremail' => $userEmail));

    // verifications
    if (!$user == null && $valid==true && $user->getAuth() == $key) 
    {
      // login
      $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_USER'));
      $this->get('security.context')->setToken($token);
    }
    else 
    {
      $request->getSession()->getFlashBag()->add('info', 'Registration error !');
      // redirection
      $url = $this->get('router')->generate('register');
      return $this->redirect($url);
    }

    return $this->render('LPUserBundle:Security:activate.html.twig', array(
      'user' => $user
    ));
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction logoutAction
 * ---------------------------------------------------------------------------------------------------- */

  public function logoutAction(Request $request)
  {

    $request->getSession()->getFlashBag()->add('info', 'logout successfull ! Goodbye !');
    return $this->redirect($this->generateUrl('login'));

  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction resetAction
 * ---------------------------------------------------------------------------------------------------- */

  public function resetAction(Request $request)
  {

    $userEmail = null;
    $userName  = null;
    $validEmail = false;
    $validName = false;

    // searchform  -------------------------------------------------------------------------------------

    $data = array();
    $form = $this   ->createFormBuilder($data)
                    ->add('username')
                    ->add('useremail')          
                    ->add('save', 'submit')
                    ->getForm();

    // username treatment ------------------------------------------------------------------------------

    if (isset($_POST['_username']) && !empty($_POST['_username'])) 
    {
      $userName = $_POST['_username'];

      // recup user by username
      $user  = $this  ->getDoctrine()
                      ->getManager()
                      ->getRepository('LPUserBundle:User')
                      ->findOneBy(array('username' => $userName));

      if ($user != null) 
      {
        $validName = true;
      }

    }

    // email treatment ---------------------------------------------------------------------------------

    if (isset($_POST['_email']) && !empty($_POST['_email'])) 
    {
      $userEmail = $_POST['_email'];

      // recup user by username
      $user  = $this  ->getDoctrine()
                      ->getManager()
                      ->getRepository('LPUserBundle:User')
                      ->findOneBy(array('useremail' => $userEmail));

      if ($user != null) 
      {
        $valiEmail = true;
      }

    }

    // decision ----------------------------------------------------------------------------------------

    if (isset($_POST) && !empty($_POST)) 
    {

      if ($validName == true || $validEmail == true) 
      {

        $email    = $user->getUseremail();
        $subject  = 'Language Partner reset password';
        $activation = $user->getAuth();

        $siteUrl = $this->container->getParameter('site_url');

        $text    = "To reset your password, please click on this link :\n\n";
        //$text   .= 'http://flab-image.com/changepwd?email=' . urlencode($email) . "&key=$activation";
        $text   .= $siteUrl . 'changepwd?email=' . urlencode($email) . "&key=$activation";

        // recup mail service
        $mailService = $this->container->get('lp_user.mail'); 
        // sending mail
        $sendEmail = $mailService->sendMail($email, $subject, $text);
        // confirmation message
        $request->getSession()->getFlashBag()->add('info', 'A reset link was sent to your email !');

      }
      elseif ($validName == false) 
      {
        $request->getSession()->getFlashBag()->add('info', 'No user was found with ' . $userName . ' name !' );
        //return $this->redirect($this->generateUrl('login'));
      }
      elseif ($validEmail == false) 
      {
        $request->getSession()->getFlashBag()->add('info', 'No user was found with ' . $userEmail . ' email !' );
        //return $this->redirect($this->generateUrl('reset'));
      }
      else 
      {
        $request->getSession()->getFlashBag()->add('info', 'Reset Error !' );
        //return $this->redirect($this->generateUrl('reset'));
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
    return $this->render('LPUserBundle:Security:changepwd.html.twig', array(
      'form' => $form->createView()
    ));

  }


}



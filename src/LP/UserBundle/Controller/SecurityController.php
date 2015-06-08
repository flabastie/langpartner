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

          // test admin
          if ($_POST['form']['username']=="admin") {
            $newUser    ->setRoles(array('ROLE_ADMIN'));   // ROLE_ADMIN definition
          }
          else {
            $newUser    ->setRoles(array('ROLE_USER'));   // ROLE_USER definition
          }
          
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

}



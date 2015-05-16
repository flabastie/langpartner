<?php
// src/LP/UserBundle/Controller/SecurityController.php;

namespace LP\UserBundle\Controller;

use LP\UserBundle\Entity\User;
use LP\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;


class SecurityController extends Controller
{
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



  public function registerAction(Request $request)
  {
    // Si le visiteur est déjà identifié, on le redirige vers l'accueil
    if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
      return $this->redirect($this->generateUrl('lp_partner_member_list'));
    }

    $session = $request->getSession();

    $newUser = new User();

    // On crée le FormBuilder grâce au service form factory
    $formBuilder = $this->get('form.factory')->createBuilder('form', $newUser);

    $form = $this->get('form.factory')->create(new UserType(), $newUser);

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

        if ($valid) {

          // On ne se sert pas du sel pour l'instant
          $newUser->setSalt('');
          // On définit uniquement le role ROLE_USER qui est le role de base
          $newUser->setRoles(array('ROLE_USER'));


          // verifications --------------------------------------------------------------------------------

          // recup users
          $users  = $this   ->getDoctrine()
                            ->getManager()
                            ->getRepository('LPUserBundle:User')
                            ->findAll();

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

          $em = $this->getDoctrine()->getManager();
          $em->persist($newUser);
          $em->flush();


          // Send the email --------------------------------------------------------------------------------

          // Create a unique  activation code:
          $activation = md5(uniqid(rand(), true));
          $email = $_POST['form']['useremail'];
          $text = " To activate your account, please click on this link :\n\n";
          $text .= 'http://flab-image.com/activate?email=' . urlencode($email) . "&key=$activation";

          $message = \Swift_Message::newInstance()
              ->setSubject('Registration Confirmation')
              ->setFrom('contact@flab-image.com')
              ->setTo($email)
              ->setBody($this->renderView('LPUserBundle:Security:email.txt.twig', array('text' => $text)));
          $this->get('mailer')->send($message);

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

    }

    return $this->render('LPUserBundle:Security:register.html.twig', array(
      'form' => $form->createView(),
    ));


  }


  public function activateAction(Request $request)
  {

echo "<pre>";
print_r($_GET);
echo "</pre>";

    return $this->render('LPUserBundle:Security:activate.html.twig');
  }


  public function logoutAction(Request $request)
  {

    $request->getSession()->getFlashBag()->add('info', 'logout successfull ! Goodbye !');
    return $this->redirect($this->generateUrl('login'));

  }

}



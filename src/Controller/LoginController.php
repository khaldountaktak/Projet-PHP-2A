<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, MemberRepository $memberRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($this->getUser()) {
            // Retrieve the ID of the logged-in user
            $user =  $memberRepository->findOneBy([ "email" => $this->getUser()->getUserIdentifier()])  ;
            // Redirect to the member's page
            return $this->redirectToRoute('app_member_show', ['id' => $user->getId()]);
        }


        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET', 'POST'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        dump("logout");
        // throw new \Exception('Don\'t forget to activate logout in security.yaml');
        return new Response();
    }
    #[Route('/post-login-redirect', name: 'app_post_login_redirect')]
    public function postLoginRedirect(MemberRepository $memberRepository): RedirectResponse
    {
        // Ensure user is authenticated
        if (!$this->getUser()) {
            throw new AccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Retrieve the logged-in user's ID
        $user = $memberRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        // Redirect to the user's profile page
        return $this->redirectToRoute('app_member_show', ['id' => $user->getId()]);
    }
}

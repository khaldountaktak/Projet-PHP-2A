<?php

namespace App\Security;

use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;
    private $memberRepository;

    public function __construct(RouterInterface $router, MemberRepository $memberRepository)
    {
        $this->router = $router;
        $this->memberRepository = $memberRepository;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Get the logged-in user
        $user = $token->getUser();

        // Fetch the user's profile ID
        $member = $this->memberRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Redirect to the member's profile page
        return new RedirectResponse($this->router->generate('app_member_show', [
            'id' => $member->getId(),
        ]));
    }
}

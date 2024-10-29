<?php
namespace App\Controller;

use App\Repository\MemberRepository;
use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MemberController extends AbstractController
{
    #[Route('/member', name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {

        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    #[Route('/member/{id}', name: 'app_member_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }
}

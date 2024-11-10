<?php
namespace App\Controller;

use App\Repository\MemberRepository;
use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MemberController extends AbstractController
{
    #[Route('/member', name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {

        if ($user = $this->getUser()) {
            $idUser = $memberRepository->findOneBy(['email' => $user->getUserIdentifier()])->getId();
            return $this->redirectToRoute('app_member_show', ['id' => $idUser]);
        }
        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    #[Route('/member/{id}', name: 'app_member_show')]
    public function show(int $id, MemberRepository $membreRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // Replace 'app_login' with your actual login route name
        }
        
        $member = $membreRepository->find($id);
        
        if (!$member) {
            throw $this->createNotFoundException('Le membre demandé n\'existe pas');
        }
        
        // Get the currently logged-in user
        $user = $this->getUser();
        $idUser = $membreRepository->findOneBy(['email' => $user->getUserIdentifier()])->getId();

        // Check if the user is an admin or if they are accessing their own profile
        if (!$this->isGranted('ROLE_ADMIN') && $idUser !== $id) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette page.');
        }
        
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }
}

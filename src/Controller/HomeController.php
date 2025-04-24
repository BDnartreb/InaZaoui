<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    #[Route('/guests', name: 'guests')]
    public function guests(UserRepository $userRepository): Response
    {
        //$allGuests = $this->em->getRepository(User::class)->findAll();
        $allGuests = $userRepository->findAllWithMedias();
        $guests = array_filter($allGuests, function ($user) {
            $roles = $user->getRoles();
            return !in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_FROZEN', $roles);
        });

        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    #[Route('/guest/{id}', name: 'guest')]
    public function guest(int $id): Response
    {
        $guest = $this->em->getRepository(User::class)->find($id);
        if (in_array('ROLE_FROZEN', $guest->getRoles())){
            $this->addFlash('error', 'Accès refusé');
            return $this->redirectToRoute('guests');
        }

        // if (in_array('ROLE_FROZEN', $guest->getRoles())) {
        //     throw $this->createAccessDeniedException('Cet utilisateur est gelé et ne peut pas accéder à cette page.');
        // }

        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    #[Route('/portfolio/{id}', name: 'portfolio')]
    public function portfolio(?int $id = null): Response
    {  
        $albums = $this->em->getRepository(Album::class)->findAll();
        $album = $id ? $this->em->getRepository(Album::class)->find($id) : null;
        $user = $this->getUser();

        if ($user instanceof User) {
            // throw new \LogicException('L\'utilisateur connecté n\'est pas valide.');
            $medias = $album
            ? $this->em->getRepository(Media::class)->findByAlbum($album)
            : $this->em->getRepository(Media::class)->findByUser($user);

        // if user is ['ROLE_FROZEN'], do not display his medias
            $medias = array_filter($medias, function ($media) {
            return !in_array('ROLE_FROZEN', $media->getUser()->getRoles());
            });
        } else {
            $medias = $album
            ? $this->em->getRepository(Media::class)->findByAlbum($album)
            : [];
        }  

        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('front/about.html.twig');
    }
}
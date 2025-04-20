<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'home')]
    public function home()
    {
        return $this->render('front/home.html.twig');
    }

    #[Route('/guests', name: 'guests')]
    public function guests()
    {
        $allGuests = $this->em->getRepository(User::class)->findAll();

        $guests = array_filter($allGuests, function ($user) {
            $roles = $user->getRoles();
            return !in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_FROZEN', $roles);
        });

        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    #[Route('/guest/{id}', name: 'guest')]
    public function guest(int $id)
    {
        $guest = $this->em->getRepository(User::class)->find($id);
        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    #[Route('/portfolio/{id}', name: 'portfolio')]
    public function portfolio(?int $id = null)
    {  
        $albums = $this->em->getRepository(Album::class)->findAll();
        $album = $id ? $this->em->getRepository(Album::class)->find($id) : null;
        $user = $this->getUser();
        
        $medias = $album
        ? $this->em->getRepository(Media::class)->findByAlbum($album)
        : $this->em->getRepository(Media::class)->findByUser($user);

        // if user of the media has no role [''], get rid of user's medias
        $medias = array_filter($medias, function ($media) {
            return !in_array('ROLE_FROZEN', $media->getUser()->getRoles());
        });

        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about()
    {
        return $this->render('front/about.html.twig');
    }
}
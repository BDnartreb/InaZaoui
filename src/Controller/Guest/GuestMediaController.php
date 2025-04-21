<?php

namespace App\Controller\Guest;

use App\Entity\Media;
use App\Form\GuestMediaType;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function PHPUnit\Framework\throwException;

final class GuestMediaController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/guest/media', name: 'guest_media_index')]
    public function index(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $criteria = [];
        $criteria['user'] = $this->getUser();

        $medias = $this->em->getRepository(Media::class)->findBy(
            $criteria,
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );

        $total = $this->em->getRepository(Media::class)->count([]);

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/guest/media/add', name: 'guest_media_add')]
    public function add(Request $request)
    {
        $media = new Media();
        $media->setUser($this->getUser());
        //$form = $this->createForm(MediaType::class, $media, ['roles' => $this->isGranted('ROLE_ADMIN')]);
        $form = $this->createForm(GuestMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media->setPath('uploads/' . md5(uniqid()) . '.' . $media->getFile()->guessExtension());
            $media->getFile()->move('uploads/', $media->getPath());
            $this->em->persist($media);
            $this->em->flush();
            return $this->redirectToRoute('guest_media_index');
        }

        return $this->render('guest_media/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/guest/media/delete/{id}', name: 'guest_media_delete')]
    public function delete(int $id)
    {
        $media = $this->em->getRepository(Media::class)->find($id);
        $user = $media->getUser();

        if ($media && $user = $this->getUser()){
            $filePath = $media->getPath();
            $this->em->remove($media);
            $this->em->flush();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } else {
            
        }

        return $this->redirectToRoute('guest_media_index');
    }
}

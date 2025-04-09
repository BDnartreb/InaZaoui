<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Media;
use App\Form\AlbumType;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    // /**
    //  * @Route("/admin/album", name="admin_album_index")
    //  */
    #[Route('/admin/album', name: 'admin_album_index')]
    public function index(EntityManagerInterface $em)
    {
        //$albums = $this->getDoctrine()->getRepository(Album::class)->findAll();
        $albums = $em->getRepository(Album::class)->findAll();
    
        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    // /**
    //  * @Route("/admin/album/add", name="admin_album_add")
    //  */
    #[Route('/admin/album/add', name: 'admin_album_add')]
    public function add(Request $request)
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $this->getDoctrine()->getManager()->persist($album);
            // $this->getDoctrine()->getManager()->flush();
            $this->em->persist($album);
            $this->em->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', ['form' => $form->createView()]);
    }

    // /**
    //  * @Route("/admin/album/update/{id}", name="admin_album_update")
    //  */
    #[Route('/admin/album/update/{id}', name: 'admin_album_update')]
    public function update(Request $request, int $id)
    {
        //$album = $this->getDoctrine()->getRepository(Album::class)->find($id);
        $album = $this->em->getRepository(Album::class)->find($id);
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$this->getDoctrine()->getManager()->flush();
            $this->em->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', ['form' => $form->createView()]);
    }

    // /**
    //  * @Route("/admin/album/delete/{id}", name="admin_album_delete")
    //  */
    #[Route('/admin/album/delete/{id}', name: 'admin_album_delete')]
    public function delete(int $id)
    {
        // $media = $this->getDoctrine()->getRepository(Album::class)->find($id);
        // $this->getDoctrine()->getManager()->remove($media);
        // $this->getDoctrine()->getManager()->flush();
        $media = $this->em->getRepository(Album::class)->find($id);
        $this->em->remove($media);
        $this->em->flush();

        return $this->redirectToRoute('admin_album_index');
    }
}
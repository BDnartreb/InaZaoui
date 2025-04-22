<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class GuestController extends AbstractController
{
    protected EntityManagerInterface $em;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;
    }


    #[Route('/admin/guests', name: 'admin_guests_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $allGuests = $em->getRepository(User::class)->findAll();  
        $guests = array_filter($allGuests, function ($user) {
            return !in_array('ROLE_ADMIN', $user->getRoles());
        });
        return $this->render('admin/guest/index.html.twig', ['guests' => $guests]);
    }
    
    #[Route('/admin/guest/add', name: 'admin_guest_add')]
    public function add(Request $request): Response
    {
        $guest = new User();
        $form = $this->createForm(UserType::class, $guest);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $guest->setRoles(['ROLE_USER']);
            $guest->setPassword($this->userPasswordHasher->hashPassword($guest, 'password'));
            $this->em->persist($guest);
            $this->em->flush();
            return $this->redirectToRoute('admin_guests_index');
        }
        return $this->render('admin/guest/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/admin/guest/update/{id}', name: 'admin_guest_update')]
    public function update(Request $request, int $id): Response
    {
        $guest = $this->em->getRepository(User::class)->find($id);
        $form = $this->createForm(UserUpdateType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
    
                if (!empty($plainPassword)) {
                    $hashedPassword = $this->userPasswordHasher->hashPassword($guest, $plainPassword);
                    $guest->setPassword($hashedPassword);
                }
            $this->em->persist($guest);
            $this->em->flush();

            return $this->redirectToRoute('admin_guests_index');
        }

        return $this->render('admin/guest/update.html.twig', ['form' => $form->createView()]);
    }

   
    #[Route('/admin/guest/delete/{id}', name: 'admin_guest_delete')]
    public function delete(int $id): Response
    {
        $guest = $this->em->getRepository(User::class)->find($id);
        $this->em->remove($guest);
        $this->em->flush();
        //unlink($guest->getPath());

        return $this->redirectToRoute('admin_guests_index');
    }



}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="registro")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        //Manejar o encargarse del pedido o solicitud
        $form->handleRequest($request);

        /* 
        Deben cumplirse 2 condiciones
        1. si el formulario fue enviado
        2. Si es valido
        */
        if($form->isSubmitted() && $form->isValid()){
            //Referenciar el entity manager
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($passwordEncoder->encodePassword($user,$form['password']->getData()));
            //Persitir los datos que se estan cargando
            $em->persist($user);
            //Crear y ejecutar la consulta SQL
            $em->flush();
            $this->addFlash('exito',User::REGISTRO_EXITOSO);
            return $this->redirectToRoute('registro');
        }

        return $this->render('registro/index.html.twig', [
            'formulario' => $form->createView()
        ]);
    }
}

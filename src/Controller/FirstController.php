<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{

    #[Route('/order/{maVar}', name: 'test.order.route')]
    public function testOrderRuote($maVar) {
        return new Response("
        <html><body>$maVar</body></html>
        ");
    }

    #[Route('/first', name: 'first')]
    public function index(): Response
    {
        // chercher au la abse de données vos users
        return $this->render('first/index.html.twig', [
            'name' => 'Sellaouti',
            'firstname' => 'Aymen'
        ]);
    }

    #[Route('/sayHello/{name}/{firstname}', name: 'say.hello')]
    public function sayHello(Request $request, $name, $firstname): Response
    {
        return $this->render('first/hello.html.twig', [
            'nom' => $name,
            'prenom' => $firstname
        ]);
    }

    #[Route(
        'multi/{entier1<\d+>}/{entier2<\d+>}',
        name: 'multiplication'
    )]
    public function multiplication($entier1, $entier2) {
        $resultat = $entier1 * $entier2;
        return new Response("<h1>$resultat</h1>");
    }
}

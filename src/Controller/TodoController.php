<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        // Afficher notre tableau de todo
        // sinon je l'initialise puis j'affiche
        if (!$session->has('todos')) {
            $todos =[
                'achat'=>'acheter clé usb',
                'cours'=>'Finaliser mon cours',
                'correction'=>'corriger mes examens'
            ];
            $session->set('todos', $todos);
        }
        // si j ai mon tableau de todo dans ma session je ne fait que l'afficher
        return $this->render('todo/index.html.twig');
    }
    #[Route('/todo/{name}/{content}', name: 'todo.add')]
    public function addTodo(Request $request, $name, $content) {
        // Vérifier si j ai mon tableau de todo dans la session
            // si oui

            // si non
                // afficher une erreur et on va redirger vers le controlleur index
    }
}

<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('personne')]
class PersonneController extends AbstractController
{

    public function __construct(private LoggerInterface $logger, private Helpers $helper)
    {}

    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }
    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin'=> $ageMin,
            'ageMax' => $ageMax]
        );
    }

    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response {
        echo ($this->helper->sayCc());
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        // 24
        $nbrePage = ceil($nbPersonne / $nbre) ;

        $personnes = $repository->findBy([], [],$nbre, ($page - 1 ) * $nbre);

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response {
        if(!$personne) {
            $this->addFlash('error', "La personne n'existe pas ");
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/detail.html.twig', ['personne' => $personne]);
    }
    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mailer
    ): Response
    {
        $new = false;
        //$this->getDoctrine() : Version Sf <= 5
        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }

        // $personne est l'image de notre formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        // Mn formulaire va aller traiter la requete
        $form->handleRequest($request);
        //Est ce que le formulaire a été soumis
        if($form->isSubmitted() && $form->isValid()) {
            // si oui,
            // on va ajouter l'objet personne dans la base de données
            $photo = $form->get('photo')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('personne_directory');
                $personne->setImage($uploaderService->uploadFile($photo, $directory));
            }

            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            // Afficher un mssage de succès
            if($new) {
                $message = " a été ajouté avec succès";
            } else {
                $message = " a été mis à jour avec succès";
            }
            $mailMessage = $personne->getFirstname().' '.$personne->getName().' '.$message;
            $this->addFlash('success',$personne->getName(). $message );
            $mailer->sendEmail(content: $mailMessage);
            // Rediriger verts la liste des personne
            return $this->redirectToRoute('personne.list');
        } else {
            //Sinon
            //On affiche notre formulaire
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView()
            ]);
        }

    }
    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse {
        // Récupérer la personne
        if ($personne) {
            // Si la personne existe => le supprimer et retourner un flashMessage de succés
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($personne);
            // Exécuter la transacition
            $manager->flush();
            $this->addFlash('success', "La personne a été supprimé avec succès");
        } else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Personne innexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age) {
        //Vérifier que la personne à mettre à jour existe
        if ($personne) {
            // Si la personne existe => mettre a jour notre personne + message de succes
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            $this->addFlash('success', "La personne a été mis à jour avec succès");
        }  else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "Personne innexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
}

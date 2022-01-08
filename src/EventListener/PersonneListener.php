<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener
{
    public function __construct(private LoggerInterface $logger) {
}
    public function onPersonneAdd(AddPersonneEvent $event){
        $this->logger->debug("cc je suis entrain d'écouter l'evenement personne.add et une personne vient d'être ajoutée et c'est ". $event->getPersonne()->getName());
    }
    public function onListAllPersonnes(ListAllPersonnesEvent $event){
        $this->logger->debug("Le nombre de personne dans la base est ". $event->getNbPersonne());
    }
    public function onListAllPersonnes2(ListAllPersonnesEvent $event){
        $this->logger->debug("Le second Listener avec le nbre :". $event->getNbPersonne());
    }

    public function logKernelRequest(KernelEvent $event){
        dd($event->getRequest());
    }
}
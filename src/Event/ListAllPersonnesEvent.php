<?php

namespace App\Event;

use App\Entity\Personne;
use Symfony\Contracts\EventDispatcher\Event;

class ListAllPersonnesEvent extends Event
{
    const LIST_ALL_PERSONNE_EVENT = 'personne.list_alls';

    public function __construct(private int $nbPersonne) {}

    public function getNbPersonne(): int {
        return $this->nbPersonne;
    }

}
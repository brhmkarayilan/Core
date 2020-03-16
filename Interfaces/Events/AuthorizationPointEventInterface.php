<?php
namespace exface\Core\Interfaces\Events;

use exface\Core\Interfaces\Security\AuthorizationPointInterface;

interface AuthorizationPointEventInterface extends EventInterface
{
    /**
     * Returns the authorization point, for which the event was triggered.
     * 
     * @return AuthorizationPointInterface
     */
    public function getAuthorizationPoint() : AuthorizationPointInterface;
}
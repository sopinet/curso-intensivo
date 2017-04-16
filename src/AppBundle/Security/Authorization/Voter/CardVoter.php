<?php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CardVoter implements VoterInterface
{
    const CREATE = 'create';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::CREATE,
            self::VIEW,
            self::EDIT,
            self::DELETE
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'AppBundle\Entity\Card';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @var \AppBundle\Entity\Card $card
     */
    public function vote(TokenInterface $token, $card, array $attributes)
    {
        // Comprobamos que la clase sea del tipo que queremos revisar
        if (!$this->supportsClass(get_class($card))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // Comprobamos que sólo se ha pasado un atributo
        if(1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
            );
        }

        // Cogemos el primer atributo pasado por parámetro
        $attribute = $attributes[0];

        // Si el atributo no está soportado se devuelve un valor neutro / abstain
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // Se coge el usuario logueado
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::CREATE:
                // Devuelve GRANTED, simplemente porque hay un usuario logueado
                return VoterInterface::ACCESS_GRANTED;
                break;

            case self::VIEW:
                // Sea de quien sea la carta, se devuelve GRANTED
                return VoterInterface::ACCESS_GRANTED;
                break;

            case self::EDIT:
                // Si la carta tiene usuario asignado y es el usuario logueado, se devuelve GRANTED
                if ($card->getUser() != null && $user->getId() === $card->getUser()->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::DELETE:
                // Si la carta tiene usuario asignado y es el usuario logueado, se devuelve GRANTED
                if ($card->getUser() != null && $user->getId() === $card->getUser()->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
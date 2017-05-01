<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Card;
use AppBundle\Form\CardType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Card controller.
 *
 * @Route("/panel/card")
 */
class CardController extends Controller
{

    /**
     * Lists all Card entities.
     *
     * @Route("/", name="panel_card")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $entities = $user->getCards();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Card entity.
     *
     * @Route("/", name="panel_card_create")
     * @Method("POST")
     * @Template("AppBundle:Card:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Card();
        if (false === $this->get('security.context')->isGranted('create', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('panel_card_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Card entity.
     * @Security("is_granted('create', card)")
     * @param Card $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Card $entity)
    {
        $form = $this->createForm(new CardType($this->get('doctrine.orm.entity_manager')), $entity, array(
            'action' => $this->generateUrl('panel_card_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Card entity.
     *
     * @Route("/new", name="panel_card_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Card();
        if (false === $this->get('security.context')->isGranted('create', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Card entity.
     *
     * @Route("/{id}", name="panel_card_show")
     * @Security("is_granted('view', card)")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Card $card)
    {
        $deleteForm = $this->createDeleteForm($card->getId());

        return array(
            'entity'      => $card,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Card entity.
     *
     * @Route("/{id}/edit", name="panel_card_edit")
     * @Security("is_granted('edit', card)")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Card $card)
    {
        $editForm = $this->createEditForm($card);
        $deleteForm = $this->createDeleteForm($card->getId());

        return array(
            'entity'      => $card,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Card entity.
    * @Security("is_granted('edit', card)")
    * @param Card $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Card $card)
    {
        $form = $this->createForm(new CardType($this->get('doctrine.orm.entity_manager')), $card, array(
            'action' => $this->generateUrl('panel_card_update', array('id' => $card->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Card entity.
     *
     * @Route("/{id}", name="panel_card_update")
     * @Security("is_granted('edit', card)")
     * @Method("PUT")
     * @Template("AppBundle:Card:edit.html.twig")
     */
    public function updateAction(Request $request, Card $card)
    {
        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($card->getId());
        $editForm = $this->createEditForm($card);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_card_edit', array('id' => $card->getId())));
        }

        return array(
            'entity'      => $card,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Card entity.
     * @Security("is_granted('delete', card)")
     * @Route("/{id}", name="panel_card_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Card $card)
    {
        $form = $this->createDeleteForm($card->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($card);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('panel_card'));
    }

    /**
     * Creates a form to delete a Card entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panel_card_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

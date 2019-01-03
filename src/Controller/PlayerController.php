<?php
/**
 * Created by PhpStorm.
 * User: vince
 * Date: 30/11/18
 * Time: 10:46
 */

namespace App\Controller;

use App\Entity\Player;
use App\Entity\StatusEvent;
use App\Form\PlayerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;

class PlayerController extends AbstractController
{

    /**
     * @Route("/manager/player/{id}", name="player", requirements={"id"="\d+"}, methods="GET|POST")
     * @param Request $request
     * @param Event $event
     * @return Response
     */
    public function addPlayer(Request $request, Event $event): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player->addEvent($event);
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            // check if number of players max is reached
            if (count($event->getPlayers()) === $event->getFormatEvent()->getNumberOfPlayers()) {
                // change event's status to full status
                $statutEvent = $em->getRepository(StatusEvent::class)
                    ->findOneBy(['state' => $event->getStatusEvent()->getFullState()], []);

                $event->setStatusEvent($statutEvent);
                $em->flush();
            }

            $this->addFlash(
                'success',
                'Votre participant a été ajouté.'
            );

            return $this->redirectToRoute('player', ['id' => $event->getId()]);
        }

        return $this->render('player/index.html.twig', [
            'players' => $event->getPlayers(),
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    /**
     * @Route("/manager/player/{id}/delete/{player}", name="player_delete",
     *     requirements={"id"="\d+", "player_id"="\d+"}, methods="DELETE")
     * @param Request $request
     * @param Event $event
     * @param Player $player
     * @return Response
     */
    public function delete(Request $request, Event $event, Player $player): Response
    {
        if ($this->isCsrfTokenValid('delete' . $player->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($player);
            $em->flush();

            // if event's status is full and the number of players is lower than number of players format
            if (count($event->getPlayers()) < $event->getFormatEvent()->getNumberOfPlayers()) {
                // change event's status to registration status
                $statutEvent = $em->getRepository(StatusEvent::class)
                    ->findOneBy(['state' => $event->getStatusEvent()->getRegistrationState()], []);

                $event->setStatusEvent($statutEvent);
                $em->flush();
            }

            $this->addFlash(
                'success',
                'Votre participant a été supprimé !'
            );
        }

        return $this->redirectToRoute('player', ['id' => $event->getId()]);
    }
}

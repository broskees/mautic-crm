<?php

namespace MauticPlugin\CustomCrmBundle\EventListener;

use Mautic\CalendarBundle\CalendarEvents;
use Mautic\CoreBundle\Helper\DateTimeHelper;
use MauticPlugin\CustomCrmBundle\Entity\Task;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private \Mautic\CoreBundle\Factory\MauticFactory $factory
    ) {}

    public static function getSubscribedEvents(): array
    {
        return array(
            # TODO: Where are these events defined now?
            # CalendarEvents::CALENDAR_ON_GENERATE => array('onCalendarGenerate', 0),
        );
    }

    /**
     * Adds created/completed tasks to the calendar
     *
     * @param CalendarGeneratorEvent $event
     */
    public function onCalendarGenerate(CalendarGeneratorEvent $event)
    {
        $em = $this->factory->getEntityManager();
        $tasks = $em->getRepository('CustomCrmBundle:Task')->findAll();

        $router = $this->factory->getRouter();
        $events = array();
        // All tasks
        foreach ($tasks as $task) {
            /** @var Task $task */
            $events[] = array(
                'start' => (new DateTimeHelper($task->getDateAdded()))->toLocalString(\DateTime::ISO8601),
                'url'   => $router->generate('mautic_contact_action', array('objectAction' => 'view', 'objectId' => $task->getLead()->getId()), true),
                'title' => $this->factory->getTranslator()->trans('ddi.lead_actions.tasks.calendar.created',
                    array(
                        '%name%' => $task->getName()
                    )
                ),
                'iconClass' => 'fa fa-fw fa-tasks'
            );

            if ($task->getIsCompleted()) {
                $events[] = array(
                    'start' => (new DateTimeHelper($task->getDateCompleted()))->toLocalString(\DateTime::ISO8601),
                    'url'   => $router->generate('mautic_contact_action', array('objectAction' => 'view', 'objectId' => $task->getLead()->getId()), true),
                    'title' => $this->factory->getTranslator()->trans('ddi.lead_actions.tasks.calendar.completed',
                        array(
                            '%name%' => $task->getName()
                        )
                    ),
                    'iconClass' => 'fa fa-fw fa-check'
                );
            } else if ($task->getDueDate()) {
                $events[] = array(
                    'start' => (new DateTimeHelper($task->getDueDate()))->toLocalString(\DateTime::ISO8601),
                    'url'   => $router->generate('mautic_contact_action', array('objectAction' => 'view', 'objectId' => $task->getLead()->getId()), true),
                    'title' => $this->factory->getTranslator()->trans('ddi.lead_actions.tasks.calendar.due_date',
                        array(
                            '%name%' => $task->getName()
                        )
                    ),
                    'iconClass' => 'fa fa-fw fa-clock-o'
                );
            }
        }

        foreach ($this->factory->getModel('customcrm.opportunity')->getEntities() as $opportunity) {
            $events[] = array(
                'start' => (new DateTimeHelper($opportunity->getDateAdded()))->toLocalString(\DateTime::ISO8601),
                'url'   => $router->generate('mautic_customcrm_opportunity_action', array('objectAction' => 'edit', 'objectId' => $opportunity->getId()), true),
                'title' => $opportunity->getValue() . ' (' . $opportunity->getValueType() . ')',
                'iconClass' => 'fa fa-fw fa-dollar'
            );
        }
        $event->addEvents($events);
    }
}

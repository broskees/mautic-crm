<?php

namespace MauticPlugin\CustomCrmBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents() {
        return array(
            CampaignEvents::CAMPAIGN_ON_BUILD => array('onCampaignBuild', 0)
        );
    }

    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $action = array(
            'label'       => 'mautic.customcrm.opportunity.events.create',
            'description' => 'mautic.customcrm.opportunity.events.create_descr',
            'formType'    => 'customcrm_campaign_opportunity',
            'callback'    => array('\\MauticPlugin\\CustomCrmBundle\\Helper\\CampaignEventHelper', 'createOpportunity')
        );

        $event->addAction('opportunity.create', $action);

        $action = array(
            'label' => 'ddi.lead_actions.tasks.campaign.event.add',
            'description' => 'ddi.lead_actions.tasks.campaign.event.add.description',
            'callback' => array('\\MauticPlugin\\CustomCrmBundle\\Helper\\CampaignEventHelper', 'addTaskAction'),
            'formType' => 'task',
            'formTypeOptions' => array('type' => 'campaignBuilder'),
        );

        $event->addAction('task.add', $action);
    }
}

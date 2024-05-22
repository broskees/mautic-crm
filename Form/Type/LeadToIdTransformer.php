<?php

namespace MauticPlugin\CustomCrmBundle\Form\Type;

use Mautic\LeadBundle\Model\LeadModel;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LeadToIdTransformer implements DataTransformerInterface
{
    public function __construct(
        private LeadModel $model,
    ) {}

    public function transform($lead)
    {
        if (null === $lead) {
            return '';
        }

        return $lead->getId();
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return;
        }

        $lead = $this->model->getEntity($value);

        if (null === $value) {
            throw new TransformationFailedException(sprintf(
                'An Lead with id "%s" does not exist!',
                $value
            ));
        }

        return $lead;
    }
}

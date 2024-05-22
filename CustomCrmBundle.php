<?php

namespace MauticPlugin\CustomCrmBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Mautic\PluginBundle\Entity\Plugin;
use Mautic\CoreBundle\Factory\MauticFactory;

class CustomCrmBundle extends PluginBundleBase
{
    public function getParent()
    {
        return 'MauticLeadBundle';
    }

    /**
     * @param Plugin $plugin
     * @param MauticFactory $factory
     * @param array $metadata
     * @param null $installedSchema
     *
     * @throws \Exception
     */
    public static function onPluginInstall(Plugin $plugin, MauticFactory $factory, $metadata = null, $installedSchema = null): void
    {
        if (! is_array($metadata)) {
            return;
        }

        foreach ($metadata as $key => $entity) {
            /** @var \Doctrine\ORM\Mapping\ClassMetadata $entity */
            if ($factory->getDatabase()->getSchemaManager()->tablesExist($entity->getTableName())) {
                unset($metadata[$key]);
            }
        }

        self::installPluginSchema($metadata, $factory);
    }
}

<?php
defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use PontoMega\Plugin\Content\Pix\Extension\Pix;
use Joomla\Event\DispatcherInterface;

return new class implements ServiceProviderInterface {
    public function register(Container $container) {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                $plugin     = new Pix(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('content', 'pix')
                );

                return $plugin;
            }
        );
    }
};

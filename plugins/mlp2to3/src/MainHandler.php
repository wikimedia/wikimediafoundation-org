<?php

namespace Inpsyde\MultilingualPress2to3;

use Exception;
use Inpsyde\MultilingualPress2to3\Config\ConfigAwareTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Handler\ControllerTrait;
use Inpsyde\MultilingualPress2to3\Handler\CompositeHandlerTrait;
use Psr\Container\ContainerInterface;
use Traversable;

/**
 * A composite handler that runs sub-handlers.
 *
 * This is typically an application's main class.
 *
 * @package MultilingualPress2to3
 */
class MainHandler implements HandlerInterface
{
    use ControllerTrait;

    use ConfigAwareTrait;

    use CompositeHandlerTrait;

    protected $handlers;

    /**
     * Handler constructor.
     *
     * @param ContainerInterface $config The configuration of this handler.
     */
    public function __construct(ContainerInterface $config, $handlers)
    {
        $this->_setConfigContainer($config);
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->_hook();
        $this->_run();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _hook()
    {
        add_action(
            'plugins_loaded',
            function () {
                $this->loadTranslations();
            }
        );
    }

    /**
     * Loads the plugin translations.
     *
     * @throws Exception If problem loading.
     */
    protected function loadTranslations()
    {
        $base_dir = $this->_getConfig('base_dir');
        $translations_dir = trim($this->_getConfig('translations_dir'), '/');
        $rel_path = basename($base_dir);

        load_plugin_textdomain('product-code-for-woocommerce', false, "$rel_path/$translations_dir");
    }

    /**
     * Retrieves the list of handlers associated with this instance.
     *
     * @return HandlerInterface[]|Traversable A list of handlers.
     */
    protected function _getHandlers()
    {
        return $this->handlers;
    }
}

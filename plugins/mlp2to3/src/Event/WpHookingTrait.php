<?php

namespace Inpsyde\MultilingualPress2to3\Event;

/**
 * Functionality for adding actions and hooks to WP.
 *
 * @package MultilingualPress2to3
 */
trait WpHookingTrait
{
    /**
     * Binds a handler to an action key.
     *
     * @param string $key The action key.
     * @param callable $handler The action handler.
     * @param int $priority The priority of the handler.
     * @param int $acceptedArgs The number of arguments to pass to the handler.
     *
     * @return void
     */
    protected function _addAction($key, $handler, $priority = 10, $acceptedArgs = 1)
    {
        add_action($key, $handler, $priority, $acceptedArgs);
    }

    /**
     * Binds a handler to a filter key.
     *
     * @param string $key The filter key.
     * @param callable $handler The filter handler.
     * @param int $priority The priority of the handler.
     * @param int $acceptedArgs The number of arguments to pass to the handler.
     *
     * @return void
     */
    protected function _addFilter($key, $handler, $priority = 10, $acceptedArgs = 1)
    {
        add_filter($key, $handler, $priority, $acceptedArgs);
    }
}

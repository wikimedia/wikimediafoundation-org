<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3\Event;

/**
 * Functionality for dispatching events in WP.
 *
 * @package MultilingualPress2to3
 */
trait WpTriggerCapableTrait
{
    /**
     * Triggers an event.
     *
     * @param string $name The name, or key, of the event.
     * @param array $data The data of the event.
     * @return array The data of the event, possibly modified by handlers.
     */
    protected function _trigger(string $name, array $data = []): array
    {
        $data = (object) $data;
        $data->event_name = $name;

        do_action($name, $data);

        return (array) $data;
    }
}

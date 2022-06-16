<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Schedule\Action;

use Inpsyde\MultilingualPress\Framework\Message\MessageInterface;

/**
 * Trait ResponseTrait
 * @package Inpsyde\MultilingualPress\Schedule\Action
 */
trait ResponseTrait
{
    /**
     * @var array
     */
    private $successMessages;

    /**
     * @param string $actionName
     * @param array $errors
     */
    protected function sendResponseFor(string $actionName, array $errors)
    {
        $errors = array_filter($errors);
        $hasErrors = count($errors);

        $response = !$hasErrors
            ? $this->successMessage($actionName)
            : $this->errorMessage($errors);

        wp_send_json(
            [
                'success' => !$hasErrors,
                'content' => $response,
            ]
        );

        $this->die();
    }

    /**
     * @param string $actionName
     * @return string
     */
    protected function successMessage(string $actionName): string
    {
        return $this->successMessages[$actionName]
            ?? sprintf(
                // translators: %s is the action name.
                esc_html__(
                    'Action %s executed successfully.',
                    'multilingualpress'
                ),
                $actionName
            );
    }

    /**
     * @param array $errors
     * @return string
     */
    protected function errorMessage(array $errors): string
    {
        $message = esc_html__(
            'Ops! Something goes wrong when performing the action, more info below.',
            'multilingualpress'
        );

        $text = $this->reduceMessages($errors);

        return "<p><b>{$message}</b></p> {$text}";
    }

    /**
     * @param array $messages
     * @return string
     */
    protected function reduceMessages(array $messages): string
    {
        $content = '<ul>';

        /** @var MessageInterface $message */
        foreach ($messages as $message) {
            if ($message instanceof MessageInterface) {
                $content .= "<li>{$message->content()}</li>";
            }
        }

        $content .= '</ul>';

        return $content;
    }

    /**
     * @return void
     * @uses die
     */
    protected function die()
    {
        die();
    }
}

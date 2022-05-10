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

namespace Inpsyde\MultilingualPress\Framework\Asset;

/**
 * Managing instance for all asset-specific tasks.
 */
class AssetManager
{
    /**
     * Store the asset handle to prevent to add data multiple times
     *
     * @var array
     */
    private static $dataAddedFor = [];

    /**
     * @var Script[]
     */
    private $scripts = [];

    /**
     * @var Style[]
     */
    private $styles = [];

    /**
     * Register the given script.
     *
     * @param Script $script
     * @return static
     */
    public function registerScript(Script $script): AssetManager
    {
        $this->scripts[$script->handle()] = $script;

        $this->register(
            static function () use ($script) {

                $url = MaybeMinifiedAssetUrl::fromLocation($script->location());
                $version = $script->version() ?: $url->version();

                wp_register_script(
                    $script->handle(),
                    (string)$url,
                    $script->dependencies(),
                    $version ?: null
                );
            }
        );

        return $this;
    }

    /**
     * Register the given style.
     *
     * @param Style $style
     * @return AssetManager
     */
    public function registerStyle(Style $style): AssetManager
    {
        $this->styles[$style->handle()] = $style;

        $this->register(
            static function () use ($style) {

                $url = MaybeMinifiedAssetUrl::fromLocation($style->location());
                $version = $style->version() ?: $url->version();

                wp_register_style(
                    $style->handle(),
                    (string)$url,
                    $style->dependencies(),
                    $version ?: null
                );
            }
        );

        return $this;
    }

    /**
     * Enqueues the script with the given handle.
     *
     * @param string $handle
     * @param bool $inFooter
     * @param string $enqueueAction
     * @return AssetManager
     * @throws AssetException
     */
    public function enqueueScript(
        string $handle,
        bool $inFooter = true,
        string $enqueueAction = null
    ): self {

        if (!\array_key_exists($handle, $this->scripts)) {
            throw AssetException::forWhenEnqueuingScriptNotRegistered($handle);
        }

        $script = $this->scripts[$handle];

        $this->enqueue(
            function () use ($handle, $script, $inFooter) {
                $url = MaybeMinifiedAssetUrl::fromLocation($script->location());
                $version = $script->version() ?: $url->version();

                if (!wp_script_is($handle, 'registered')) {
                    return;
                }

                $this->handleScriptData($script);

                wp_enqueue_script(
                    $handle,
                    (string)$url,
                    $script->dependencies(),
                    $version ?: null,
                    $inFooter
                );
            },
            $enqueueAction
        );

        return $this;
    }

    /**
     * Enqueues the script with the given handle.
     *
     * @param string $handle
     * @param string $objectName
     * @param array $data
     * @param bool $inFooter
     * @return AssetManager
     * @throws AssetException
     */
    public function enqueueScriptWithData(
        string $handle,
        string $objectName,
        array $data,
        bool $inFooter = true
    ): self {

        $this
            ->addScriptData($handle, $objectName, $data)
            ->enqueueScript($handle, $inFooter);

        return $this;
    }

    /**
     * Enqueues the style with the given handle.
     *
     * @param string $handle
     * @param string $enqueueAction
     * @return AssetManager
     * @throws AssetException
     */
    public function enqueueStyle(string $handle, string $enqueueAction = null): self
    {
        if (!\array_key_exists($handle, $this->styles)) {
            throw AssetException::forWhenEnqueuingScriptNotRegistered($handle);
        }

        $this->enqueue(
            static function () use ($handle) {
                wp_enqueue_style($handle);
            },
            $enqueueAction
        );

        return $this;
    }

    /**
     * Adds the given data to the given script, and handles it in case the script
     * has been enqueued already.
     *
     * @param string $handle
     * @param string $objectName
     * @param array $data
     * @return AssetManager
     * @throws AssetException
     */
    private function addScriptData(string $handle, string $objectName, array $data): AssetManager
    {
        if (!\array_key_exists($handle, $this->scripts)) {
            throw AssetException::addingScriptDataWhenScriptDoesNotExists($handle);
        }

        $script = $this->scripts[$handle];
        $script->addData($objectName, $data);

        return $this;
    }

    /**
     * Handles potential data that has been added to the script after it was
     * enqueued, and then clears the data.
     *
     * @param Script $script
     */
    private function handleScriptData(Script $script)
    {
        $handle = $script->handle();
        if (in_array($handle, self::$dataAddedFor, true)) {
            return;
        }

        self::$dataAddedFor[] = $handle;
        $data = $script->data();

        array_walk(
            $data,
            static function (array $data, string $objectName) use ($handle) {
                wp_localize_script($handle, $objectName, $data);
            }
        );
    }

    /**
     * Either executes the given callback or hooks it to the appropriate enqueue
     * action, depending on the context.
     *
     * @param callable $callback
     * @param string $enqueueAction
     */
    private function enqueue(callable $callback, string $enqueueAction = null)
    {
        $enqueueAction = $enqueueAction ?: $this->enqueueAction();

        if (did_action($enqueueAction)) {
            $callback();

            return;
        }

        add_action($enqueueAction, $callback);
    }

    /**
     * Register assets
     *
     * @param callable $callback
     */
    protected function register(callable $callback)
    {
        $action = 'init';

        if (did_action($action)) {
            return;
        }

        add_action($action, $callback);
    }

    /**
     * Returns the appropriate action for enqueueing assets.
     *
     * @return string
     */
    private function enqueueAction(): string
    {
        if (0 === strpos(ltrim(add_query_arg([]), '/'), 'wp-login.php')) {
            return empty($GLOBALS['interim_login'])
                ? 'login_enqueue_scripts'
                : '';
        }

        if (is_admin()) {
            return 'admin_enqueue_scripts';
        }

        if (is_customize_preview()) {
            return 'customize_controls_enqueue_scripts';
        }

        return 'wp_enqueue_scripts';
    }
}

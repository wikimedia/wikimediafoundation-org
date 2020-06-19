<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Framework\Http\Request;

use function Inpsyde\MultilingualPress\callExit;

/**
 * Class PhpRedirector
 * @package Inpsyde\MultilingualPress\Module\Redirect
 */
final class PhpRedirector implements Redirector
{
    /**
     * @var LanguageNegotiator
     */
    private $languageNegotiator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var NoRedirectStorage
     */
    private $noRedirectStorage;

    /**
     * @param LanguageNegotiator $languageNegotiator
     * @param NoRedirectStorage $noRedirectStorage
     * @param Request $request
     */
    public function __construct(
        LanguageNegotiator $languageNegotiator,
        NoRedirectStorage $noRedirectStorage,
        Request $request
    ) {

        $this->languageNegotiator = $languageNegotiator;
        $this->noRedirectStorage = $noRedirectStorage;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function redirect()
    {
        $value = (string)$this->request->bodyValue(
            NoredirectPermalinkFilter::QUERY_ARGUMENT,
            INPUT_GET,
            FILTER_SANITIZE_STRING
        );

        if ($value !== '') {
            $this->noRedirectStorage->addLanguage($value);
            return;
        }

        $target = $this->languageNegotiator->redirectTarget();
        if (!$target->url() || $target->siteId() === get_current_blog_id()) {
            /**
             * Do Action if Target was not Found
             *
             * @param RedirectTarget $target
             */
            do_action(self::ACTION_TARGET_NOT_FOUND, $target);
            return;
        }

        $this->noRedirectStorage->addLanguage($target->language());

        wp_redirect($target->url());
        callExit();
    }
}

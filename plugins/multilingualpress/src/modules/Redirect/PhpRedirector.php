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

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Framework\Http\Request;

use function Inpsyde\MultilingualPress\callExit;
use function Inpsyde\MultilingualPress\siteLanguageTag;

/**
 * Class PhpRedirector
 * @package Inpsyde\MultilingualPress\Module\Redirect
 */
class PhpRedirector implements Redirector
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
     * @var AcceptLanguageParser
     */
    private $acceptLanguageParser;

    /**
     * @param LanguageNegotiator $languageNegotiator
     * @param NoRedirectStorage $noRedirectStorage
     * @param Request $request
     * @param AcceptLanguageParser $acceptLanguageParser
     */
    public function __construct(
        LanguageNegotiator $languageNegotiator,
        NoRedirectStorage $noRedirectStorage,
        Request $request,
        AcceptLanguageParser $acceptLanguageParser
    ) {

        $this->languageNegotiator = $languageNegotiator;
        $this->noRedirectStorage = $noRedirectStorage;
        $this->request = $request;
        $this->acceptLanguageParser = $acceptLanguageParser;
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

        if ($this->requestLanguageIsSameAsCurrentSiteLanguage()) {
            return;
        }

        $target = $this->languageNegotiator->redirectTarget();

        if (!$target->url()) {
            /**
             * Do Action if Target was not Found
             *
             * @param RedirectTarget $target
             */
            do_action(self::ACTION_TARGET_NOT_FOUND, $target);
            return;
        }

        $this->noRedirectStorage->addLanguage($target->language());

        //phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
        wp_redirect($target->url());
        callExit();
        //phpcs:enable
    }

    /**
     * Check if the Request language coming from 'Accept-Language' header
     * is the same as the current site language
     *
     * @return bool
     */
    protected function requestLanguageIsSameAsCurrentSiteLanguage(): bool
    {
        $requestAcceptLanguageHeader = $this->request->header('Accept-Language');
        $acceptLanguage = $this->acceptLanguageParser->parseHeader($requestAcceptLanguageHeader);
        $currentSiteLanguage = strtok(siteLanguageTag(get_current_blog_id()), '-');

        if (!empty($acceptLanguage) && key($acceptLanguage) === $currentSiteLanguage) {
            return true;
        }

        return false;
    }
}

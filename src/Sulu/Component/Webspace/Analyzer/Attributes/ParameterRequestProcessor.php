<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Webspace\Analyzer\Attributes;

use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Takes the parameters from the requests, and tries to find a suiting portal. It checks for the parameters _locale and
 * _portal. Based on these it will load the best matching portal information, and uses it to analyze the request.
 */
class ParameterRequestProcessor implements RequestProcessorInterface
{
    /**
     * @param string $environment
     */
    public function __construct(private WebspaceManagerInterface $webspaceManager, private $environment)
    {
    }

    public function process(Request $request, RequestAttributes $requestAttributes)
    {
        if (!$request->get('_locale') || !$request->get('_portal')) {
            return new RequestAttributes();
        }

        $portalInformations = $this->webspaceManager->findPortalInformationsByPortalKeyAndLocale(
            $request->get('_portal'),
            $request->get('_locale'),
            $this->environment
        );

        if (!$portalInformations) {
            return new RequestAttributes();
        }

        return new RequestAttributes(
            [
                'portalInformation' => \reset($portalInformations),
            ]
        );
    }

    public function validate(RequestAttributes $attributes)
    {
        return true;
    }
}

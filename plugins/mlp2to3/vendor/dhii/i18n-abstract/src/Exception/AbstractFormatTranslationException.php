<?php

namespace Dhii\I18n\Exception;

/**
 * Common functionality for format translation exceptions.
 *
 * @since 0.1
 */
abstract class AbstractFormatTranslationException extends AbstractStringTranslationException
{
    /**
     * The parameters used for interpolation.
     *
     * @since 0.1
     *
     * @var array
     */
    protected $interpolationParams;

    /**
     * Associates interpolation parameters with this instance.
     *
     * @since 0.1
     *
     * @param array $params The parameters.
     *
     * @return $this This instance.
     */
    protected function _setInterpolationParams($params)
    {
        $this->interpolationParams = $params;

        return $this;
    }

    /**
     * Retrieves the interpolation parameters used for translation.
     *
     * @since 0.1
     *
     * @return array|null The parameters, if any.
     */
    protected function _getInterpolationParams()
    {
        return $this->interpolationParams;
    }
}

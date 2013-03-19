<?php

namespace Rezzza\Vaultage\Backend;

/**
 * Factory
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Factory
{
    /**
     * @param string $backend backend
     *
     * @return BackendInterface
     */
    public static function create($backend)
    {
        switch ($backend) {
            case 'basic':
                return new Basic\Backend();
                break;
            case 'gpg':
                return new GPG\Backend();
                break;
            default:
                throw new \OutOfBoundsException(sprintf('Backend "%s" is not supported, use "%s"', $backend, implode(', ', self::getAvailableBackends())));
                break;
        }
    }

    /**
     * @return array
     */
    public static function getAvailableBackends()
    {
        return array(
            'basic', 'gpg',
        );
    }
}

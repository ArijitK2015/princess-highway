<?php
/**
 * Custom Exceptions for the CloudFiles API
 *
 * Requres PHP 5.x (for Exceptions and OO syntax)
 *
 * See COPYING for license information.
 *
 * @author Eric "EJ" Johnson <ej@racklabs.com>
 * @copyright Copyright (c) 2008, Rackspace US, Inc.
 * @package php-cloudfiles-exceptions
 */

/**
 * Custom Exceptions for the CloudFiles API
 * @package php-cloudfiles-exceptions
 */
class SyntaxException extends Exception { }

/**
 * Class AuthenticationException
 */
class AuthenticationException extends Exception { }

/**
 * Class InvalidResponseException
 */
class InvalidResponseException extends Exception { }

/**
 * Class NonEmptyContainerException
 */
class NonEmptyContainerException extends Exception { }

/**
 * Class NoSuchObjectException
 */
class NoSuchObjectException extends Exception { }

/**
 * Class NoSuchContainerException
 */
class NoSuchContainerException extends Exception { }

/**
 * Class NoSuchAccountException
 */
class NoSuchAccountException extends Exception { }

/**
 * Class MisMatchedChecksumException
 */
class MisMatchedChecksumException extends Exception { }

/**
 * Class IOException
 */
class IOException extends Exception { }

/**
 * Class CDNNotEnabledException
 */
class CDNNotEnabledException extends Exception { }

/**
 * Class BadContentTypeException
 */
class BadContentTypeException extends Exception { }

/**
 * Class InvalidUTF8Exception
 */
class InvalidUTF8Exception extends Exception { }

/**
 * Class ConnectionNotOpenException
 */
class ConnectionNotOpenException extends Exception { }

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>

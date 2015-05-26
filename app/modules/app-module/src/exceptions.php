<?php

/**
 * This file is part of the 2015_04_makeUP_starter
 * Copyright (c) 2015
 *
 * @package exceptions.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule;



/**
 * @author Pavel Paulík <pavel.paulik1@gmail.com>
 */
interface Exception
{

}



/**
 * @author Pavel Paulík <pavel.paulik1@gmail.com>
 */
class InvalidListenerException extends \RuntimeException implements Exception
{

}



/**
 * @author Pavel Paulík <pavel.paulik1@gmail.com>
 */
class InvalidStateException extends \RuntimeException implements Exception
{

}



/**
 * @author Pavel Paulík <pavel.paulik1@gmail.com>
 */
class OutOfRangeException extends \OutOfRangeException implements Exception
{

}



/**
 * The exception that is thrown when accessing a class member (property or method) fails.
 *
 * @author Pavel Paulík <pavel.paulik1@gmail.com>
 */
class MemberAccessException extends \LogicException implements Exception
{

}


/**
 * @author Pavel Paulík <pavel.paulik1@gmail.com>
 */
class NotSupportedException extends \LogicException implements Exception
{

}


/**
 * @author Pavel Paulík <pavel.paulik1@gmail.com>
 */
class AuthenticationFBExistException extends \LogicException implements Exception
{

}

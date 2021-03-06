<?php
/*
 * +------------------------------------------------------------------------+
 * | Phalcon Framework |
 * +------------------------------------------------------------------------+
 * | Copyright (c) 2011-2014 Phalcon Team (http://www.phalconphp.com) |
 * +------------------------------------------------------------------------+
 * | This source file is subject to the New BSD License that is bundled |
 * | with this package in the file docs/LICENSE.txt. |
 * | |
 * | If you did not receive a copy of the license and are unable to |
 * | obtain it through the world-wide-web, please send an email |
 * | to license@phalconphp.com so we can send you a copy immediately. |
 * +------------------------------------------------------------------------+
 * | Authors: Andres Gutierrez <andres@phalconphp.com> |
 * | Eduar Carvajal <eduar@phalconphp.com> |
 * +------------------------------------------------------------------------+
 */
namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\ValidatorInterface;

/**
 * Phalcon\Mvc\Model\Validator\Email
 *
 * Allows to validate if email fields has correct values
 *
 * <code>
 * use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
 *
 * class Subscriptors extends \Phalcon\Mvc\Model
 * {
 *
 * public function validation()
 * {
 * $this->validate(new EmailValidator(array(
 * 'field' => 'electronic_mail'
 * )));
 * if ($this->validationHasFailed() == true) {
 * return false;
 * }
 * }
 *
 * }
 * </code>
 */
class Email2 extends Validator implements ValidatorInterface
{

    /**
     * Executes the validator
     *
     * @param
     *            Phalcon\Mvc\ModelInterface record
     * @return boolean
     */
    public function validate($record)
    {
        $field = $this->getOption("field");
        if (is_string($field) != "string") {
            throw new Exception("Field name must be a string");
        }
        
        $value = $record->readAttribute($field);
        
        if ($this->isSetOption("allowEmpty") && empty($value)) {
            return true;
        }
        
        /**
         * Filters the format using FILTER_VALIDATE_EMAIL
         */
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            
            $message = $this->getOption("message");
            if (empty($message)) {
                $message = "Value of field ':field' must have a valid e-mail format";
            }
            
            $this->appendMessage(strtr($message, [
                ":field" => $field
            ]), $field, "Email");
            return false;
        }
        
        return true;
    }
}
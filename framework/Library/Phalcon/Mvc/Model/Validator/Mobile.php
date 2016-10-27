<?php
namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\ValidatorInterface;

/**
 * Phalcon\Mvc\Model\Validator\Mobile
 *
 * Allows to validate if Idcard fields has correct values
 *
 * <code>
 * use Phalcon\Mvc\Model\Validator\Mobile as MobileValidator;
 *
 * class Subscriptors extends \Phalcon\Mvc\Model
 * {
 *
 * public function validation()
 * {
 * $this->validate(new MobileValidator(array(
 * 'field' => 'mobile'
 * )));
 * if ($this->validationHasFailed() == true) {
 * return false;
 * }
 * }
 *
 * }
 * </code>
 */
class Mobile extends Validator implements ValidatorInterface
{

    /**
     * Executes the validator
     *
     * @param Phalcon\Mvc\ModelInterface $record            
     * @return boolean
     */
    public function validate($record)
    {
        $field = $this->getOption("field");
        if (! is_string($field)) {
            throw new Exception("Field name must be a string");
        }
        
        $mobile = $record->readAttribute($field);
        
        if ($this->isSetOption("allowEmpty") && empty($mobile)) {
            return true;
        }
        
        if (strlen($mobile) != 11) {
            $message = $this->getOption("lengthInvalid");
            if (empty($message)) {
                $message = "字段':field'的值不是一个有效的中国大陆手机号码；长度不为11";
            }
            
            $this->appendMessage(strtr($message, [
                ":field" => $field
            ]), $field, "Mobile");
            return false;
        }
        
        if (preg_replace('/\d+/', '', $mobile) != '') {
            $message = $this->getOption("illegalCharacter");
            if (empty($message)) {
                $message = "字段':field'的值不是一个有效的手机号码；包含了非数字字符";
            }
            
            $this->appendMessage(strtr($message, [
                ":field" => $field
            ]), $field, "Mobile");
            return false;
        }
        
        $prefix = substr($mobile, 0, 2);
        if (! in_array((int) $prefix, [
            13,
            14,
            15,
            17,
            18
        ])) {
            $message = $this->getOption("message");
            if (empty($message)) {
                $message = "字段':field'的值不是一个有效的中国大陆手机号码。";
            }
            
            $this->appendMessage(strtr($message, [
                ":field" => $field
            ]), $field, "Mobile");
            return false;
        }
        
        return true;
    }
}

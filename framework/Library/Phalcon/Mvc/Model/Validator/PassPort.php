<?php
namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\ValidatorInterface;

/**
 * Phalcon\Mvc\Model\Validator\Idcard
 *
 * Allows to validate if Idcard fields has correct values
 * 
 * @link http://www.soso.io/article/34001.html
 *      
 *       <code>
 *       use Phalcon\Mvc\Model\Validator\Idcard as IdcardValidator;
 *      
 *       class Subscriptors extends \Phalcon\Mvc\Model
 *       {
 *      
 *       public function validation()
 *       {
 *       $this->validate(new IdcardValidator(array(
 *       'field' => 'id_card'
 *       )));
 *       if ($this->validationHasFailed() == true) {
 *       return false;
 *       }
 *       }
 *      
 *       }
 *       </code>
 *      
 */
class PassPort extends Validator implements ValidatorInterface
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
        
        $_card = $record->readAttribute($field);
        
        if ($this->isSetOption("allowEmpty") && empty($_card)) {
            return true;
        }
        $message = $this->getOption("message");
        if (empty($message)) {
            $message = "字段':field'的值不是一个有效的护照号码，长度校验错误。";
        }
        
        $this->appendMessage(strtr($message, [
            ":field" => $field
        ]), $field, "PassPort");
        return false;
    }

    /**
     * Gets message taking into account default message if one is not set.
     *
     * @param \Phalcon\Validation $validator
     *            validator
     *            
     * @return string message
     */
    protected function getMessage(\Phalcon\Validation $validator)
    {
        // get message
        $message = $this->getOption('message');
        if (! $message) {
            $message = $this->message;
        }
        return $message;
    }

    /**
     * Gets appropriate label.
     *
     * @param \Phalcon\Validation $validator
     *            validator
     * @param string $attribute
     *            attribute
     *            
     * @return string label
     */
    protected function getLabel(\Phalcon\Validation $validator, $attribute)
    {
        $label = $this->getOption('label');
        
        if (! $label) {
            $label = $validator->getLabel($attribute);
        }
        if (! $label) {
            $label = $attribute;
        }
        
        return $label;
    }
}

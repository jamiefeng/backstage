<?php
namespace Phalcon\Mvc\Model\Validator;

use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\ValidatorInterface;

/**
 * Phalcon\Mvc\Model\Validator\MedialCard111
 *
 * 儿童医院就诊卡验证 
 * 
 *      
 *       <code>
 *       use Phalcon\Mvc\Model\Validator\IdCard as IdcardValidator;
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
class MedicalCard111 extends Validator implements ValidatorInterface
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
        $trueName = $this->getOption("trueName");
        
        $param = [
            "unit_id" => "111",
            "card_type" => "01",
            "card_no" => $_card,
            "name" => $trueName,
        ];        
        $data  = 'data='.json_encode($param,JSON_UNESCAPED_UNICODE);        
        $config = \Joy::$config->toArray();
        if(empty($config['globalSetting'])){
            $this->appendMessage('就诊卡验证接口地址有误');
            return false;
        }
        if(empty($config['globalSetting']['medialCardApiUrl'])){
            $this->appendMessage('就诊卡验证接口地址有误');
            return false;
        }
        $http = new \Phalcon\Net\Http();
        $result  = $http->post($config['globalSetting']['medialCardApiUrl'],  $data);
        if(!isset($result['result'])) {
            $this->appendMessage('调用就诊卡验证接口失败');
            return false;
        }
        if($result['result']!=1) {
            $this->appendMessage($result['msg']);
            return false;
        }
        return true;       
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

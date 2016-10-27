<?php
namespace Phalcon\Validation\Validator;

/**
 * Validator user Password.
 * Custom options:
 * - lengthInvalid => optional,
 * 密码长度错误时显示
 *
 * <code>
 * $validation = new \Phalcon\Validation();
 * $validation->add('Password', new \Phalcon\Validation\Validator\Password() );
 * $messages = $validation->validate(array(
 * 'Mobile' => '13312896711',
 * ));
 * </code>
 *
 * @package Phalcon\Validation\Validator\Upload
 */
class Password extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface
{
    private $message = '表单 ":field" 输入的身份证校验失败';

    /**
     * 验证密码的长度及有效性
     *
     * @param \Phalcon\Validation $validator
     *            validator
     * @param string $attribute
     *            attribute
     *            
     * @return \Phalcon\Validation\Message\Group
     */
    public function validate($validator, $attribute)
    {
        $message = $this->getMessage($validator);
        
        $label = $this->getLabel($validator, $attribute);
        
        $mobile = $validator->getValue($attribute);
        
        $verify = preg_match('/^0?(1[34578])[0-9]{9}$/', $mobile);
        if (! $verify) {
            $validator->appendMessage(new \Phalcon\Validation\Message(str_replace(array(
                ':field'
            ), array(
                $label
            ), $this->getMessage($validator)), $attribute, 'Mobile'));
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

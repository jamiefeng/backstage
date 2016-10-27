<?php
namespace Phalcon\Validation\Validator;

/**
 * Validator user Idcard.
 * Custom options:
 * - lengthInvalid => optional,
 * 身份证号码长度错误时显示
 *
 * <code>
 * $validation = new \Phalcon\Validation();
 * $validation->add('Idcard', new \Phalcon\Validation\Validator\IdCard(
 * array(
 * 'lengthInvalid' => '表单 ":field" 输入的身份证号码长度错误，要求15或18位；实际 :length位。',
 * )
 * ));
 * $messages = $validation->validate(array(
 * 'Idcard' => '42041119911125732x',
 * ));
 * </code>
 *
 * @package Phalcon\Validation\Validator\Upload
 */
class IdCard extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface
{

    private $lengthInvalidMessage = '请输入正确的身份证号码';

    private $message = '请输入正确的身份证号码';

    /**
     * 验证身份证号码的长度及有效性
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
        
        $_card = $validator->getValue($attribute);
        $card = self::to18Card($_card);
        if ($card === false) {
            $message = $this->getOption('lengthInvalid');
            if (! $message) {
                $message = $this->lengthInvalidMessage;
            }
            $validator->appendMessage(new \Phalcon\Validation\Message(str_replace(array(
                ':field',
                ':length'
            ), array(
                $label,
                strlen($_card)
            ), $message), $attribute, 'Idcard'));
            return false;
        }
        
        $verify = self::getVerifyNum(substr($card, 0, 17));
        if ($verify != strtoupper(substr($card, 17, 1))) {
            $validator->appendMessage(new \Phalcon\Validation\Message(str_replace(array(
                ':field'
            ), array(
                $label
            ), $this->getMessage($validator)), $attribute, 'Idcard'));
            return false;
        }
        
        return true;
    }

    /**
     * 将15位的身份证号码处理为18位以兼容校验算法
     * 
     * @param string $card            
     *
     * @return string|boolean
     */
    private static function to18Card($card)
    {
        $card = trim($card);
        $length = strlen($card);
        
        if ($length == 18) {
            return $card;
        }
        
        if ($length != 15) {
            return false;
        }
        
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($card, 12, 3), array(
            '996',
            '997',
            '998',
            '999'
        )) !== false) {
            $card = substr($card, 0, 6) . '18' . substr($card, 6, 9);
        } else {
            $card = substr($card, 0, 6) . '19' . substr($card, 6, 9);
        }
        $card = $card . self::getVerifyNum($card);
        return $card;
    }

    /**
     * 使用国家标准gb 11643-1999计算身份证的验证码
     * 
     * @param string $cardBase            
     * @return boolean|string
     */
    private static function getVerifyNum($cardBase)
    {
        if (strlen($cardBase) != 17) {
            return false;
        }
        // 加权因子
        $factor = array(
            7,
            9,
            10,
            5,
            8,
            4,
            2,
            1,
            6,
            3,
            7,
            9,
            10,
            5,
            8,
            4,
            2
        );
        
        // 校验码对应值
        $verify_number_list = array(
            '1',
            '0',
            'X',
            '9',
            '8',
            '7',
            '6',
            '5',
            '4',
            '3',
            '2'
        );
        
        $checksum = 0;
        for ($i = 0; $i < strlen($cardBase); $i ++) {
            $checksum += substr($cardBase, $i, 1) * $factor[$i];
        }
        
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        
        return $verify_number;
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

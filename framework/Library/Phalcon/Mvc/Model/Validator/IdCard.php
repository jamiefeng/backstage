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
class IdCard extends Validator implements ValidatorInterface
{
    private static  $prefix = [
                  11=>"北京",
				  12=>"天津",
				  13=>"河北",
				  14=>"山西",
				  15=>"内蒙",
				  21=>"辽宁",
				  22=>"吉林",
				  23=>"黑龙",
				  31=>"上海",
				  32=>"江苏",
				  33=>"浙江",
				  34=>"安徽",
				  35=>"福建",
				  36=>"江西",
				  37=>"山东",
				  41=>"河南",
				  42=>"湖北",
				  43=>"湖南",
				  44=>"广东",
				  45=>"广西",
				  46=>"海南",
				  50=>"重庆",
				  51=>"四川",
				  52=>"贵州",
				  53=>"云南",
				  54=>"西藏",
				  61=>"陕西",
				  62=>"甘肃",
				  63=>"青海",
				  64=>"宁夏",
				  65=>"新疆",
				  71=>"台湾",
				  81=>"香港",
				  82=>"澳门",
				  91=>"国外"
				 ];
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
        
        $card = self::to18Card($_card);
        if ($card === false) {
            $message = $this->getOption("lengthInvalid");
            if (empty($message)) {
                $message = "请输入正确的身份证号码。";
            }
            
            $this->appendMessage(strtr($message, [
                ":field" => $field
            ]), $field, "Idcard");
            
            return false;
        }
        
        if (!self::checkCard($card)) {
            $message = $this->getOption("message");
            if (empty($message)) {
                $message = "请输入正确的身份证号码。";
            }
            
            $this->appendMessage(strtr($message, [
                ":field" => $field
            ]), $field, "Idcard");
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
    public static function to18Card($card)
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

    protected function checkCard($card){
    	$f = 0;
    	$ncard = $card;
    
    	if(!preg_match('/^\d{17}(\d|x)$/i', $ncard, $match)){
    		return false;
    	}
    	$ncard = preg_replace('/x$/i',"a", $ncard);
    	if(!array_key_exists(substr($ncard, 0, 2), self::$prefix)){
    		return false;
    	}
    	$brathday = substr($ncard, 6, 8);
    	if(!strtotime($brathday)){
    		return false;
    	}
    	//年龄大于150或小于6岁，非正常身份证
    	if(date("Y") - substr($brathday, 0, 4) > 150 || date("Y") - substr($brathday, 0, 4) < 0){
    		return false;
    	}
    
    	for($i = 17; $i >=0; $i--){
    		$now = substr($ncard, 17-$i, 1);
    		if($now == 'a'){
    	  $now = 10;
    		}
    		$f += (pow(2, $i) % 11) * $now;
    	}
    
    	if($f%11 != 1){
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
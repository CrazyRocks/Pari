<?php
/**
 * 基础服务类
 * 本服务相等于一个容器,如果要使用到Phalcon框架中的di则继承
 */

namespace Pari\Services;


use Phalcon\Mvc\User\Component;
use Phalcon\Validation\Message\Group as MessageGroup;
use Phalcon\Mvc\Model\Message as ModelMessage;
use Phalcon\Validation\Message as ValidationMessage;

/**
 * Class BaseService
 * @package Pari\Services
 */
class BaseService extends Component
{
    public $eachPage = 10;
    

    /**
     * @var 保存 Phalcon\Mvc\Model\Message 和 Phalcon\Validation\Message
     */
    protected $errorMessages;

    /**
     * 如果有错误信息返回真
     * @return bool
     */
    public function validationHasFailed()
    {
        if ($this->errorMessages instanceof MessageGroup) {
            return $this->errorMessages->count() > 0;
        }
        return false;
    }

    /**
     * 设置错误信息
     * @param $messages
     * @return bool
     */
    public function setMessages($messages)
    {
        if (!$this->errorMessages instanceof MessageGroup) {
            $this->errorMessages = new MessageGroup();
        }
        if (count($messages) > 0 && !empty($messages)) {
            foreach ($messages as $msg) {
                if ($msg instanceof ModelMessage) {
                    $tmp = new ValidationMessage($msg->getMessage(), $msg->getField(), $msg->getType());
                    $this->errorMessages->appendMessage($tmp);
                } else {
                    $this->errorMessages->appendMessage($msg);
                }
            }
        }
    }

    /**
     * 取错误信息
     * @param string $filter
     * @return array
     */
    public function getMessages($filter = '')
    {
        if (is_string($filter) && !empty($filter)) {
            $filtered = new MessageGroup();
            foreach ($this->errorMessages as $message) {
                if (is_object($message) && method_exists($message, 'getField')) {
                    if ($message->getField() == $filter) {
                        $filtered->appendMessage($message);
                    }
                }
            }
            return $filtered;
        }
        return $this->errorMessages;
    }

    /**
     * 此方法由子类的getOne类型方法调用，用于执行查询前生成phalcon原生find/findFirst方法所需要的参数数组
     * @access protected
     * @param  array|string   $conditions  条件数组（键名为数据库字段名，键值为目标值）或字符串（同原生findFirst方法）
     * @param  array   $params  find/findFirst方法参数数组（除conditions以外）
     * @return array    phalcon原生find/findFirst方法参数数组
     * @todo   to be optimized
     */
    protected function toParams($conditions, array $params = [], $is_sql = 0)
    {
        /****检查conditions****/
        if (is_string($conditions)) {
            $conditions = trim($conditions);

        } elseif (is_array($conditions)) {
            foreach ($conditions as $key => $val) {
                $conditions[$key] = trim($val);
                if ($conditions[$key] === '') {
                    unset($conditions[$key]);
                }
            }
        } else {
            return false;
        }
        if (!$conditions) return false;

        /****过滤参数****/
        foreach ($params as $key => $val) {
            if (empty($val)) unset($params[$key]);
        }
        $params['conditions'] = $conditions;

        /****生成（或覆盖）conditions 和 bind参数****/
        $sql = ' 1 ';
        $old = $conditions;
        if (is_array($conditions)) {
            $params['bind'] = array_values($conditions);
            $conditions = array_keys($conditions);
            foreach ($conditions as $key => $val) {
                $conditions[$key] = "{$val} = ?{$key}";
            }
            $params['conditions'] = implode(' AND ', $conditions);
        } else {
            $params['conditions'] = $conditions;
        }
        
        foreach ($old as $k => $v) {
            $sql .=" and ".$k." = '".$v."'";
        }
        
        if($is_sql == 1) {
            return $sql;
        } else {
            return $params; 
        }       
    }

    /**
     * 更新一个数据对象及其对应的数据库记录
     * @param  array  $data     新数据数组（键名为数据库字段名，键值为待更新值）
     * @param  object $objData  待更新的对象
     * @return boolean           是否更新成功
     */
    protected function updateOne(array $data, $objData)
    {
        if (!is_object($objData) || !method_exists($objData, 'update')) return false;

        /*****检查新数据数组****/
        foreach ($data as $key => $val) {
            if (!property_exists($objData, $key)) {
                unset($data[$key]);
                continue;
            }
            $data[$key] = trim($val);
        }
        if (!$data) return false;

        /*****执行更新****/
        return $objData->update($data);
    }

}

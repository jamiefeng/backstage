<?php

/**
 * 控制器基础类
 * 系统提供了对自动动作参数绑定的支持（这里的自动参数绑定不是指Phalcon原有的针对URL中的参数变量的绑定）。
 * 就是说，控制器动作可以定义命名的参数，参数的值将由框架自动从 $_GET 填充
 * 同时，为了方便开发，框架提供了一种简化数据操作的方法，即可以通过在动作参数中指定变量类型为模型：
 * 以下为实例：
 * ```
 * public function editAction(\Joy\Admin\Models\Content $content){
 *     if ( $id === null )
 *     {
 *         $this->flashSession->error('文章不存在');
 *         $this->dispatcher->forward(['action'=>'list']);
 *     }
 * }
 * ```
 * 请求的URL为：
 * $_GET=['content_id'=>1];
 * 上述实例中，框架会自动调用用户提供的参数中的键名为content_id的值，并使用调用以下方法：
 * ```
 * \Joy\Admin\Models\Content::findById($_GET['content_id']);
 * ```
 * 若请求的GET参数为：
 * $_GET=['id'=>1];
 * 上述实例中，框架会自动调用用户提供的参数中的键名为id的值，并使用调用以下方法：
 * ```
 * \Joy\Admin\Models\Content::findById($_GET['id']);
 * ```
 *
 * 来获取关于$content的一个实例.
 * 请注意，如果你定义了一个类型为Phalcon\Mvc\Model子类的变量，但是并没有提供此参数名为键值的参数时，
 * 如果参数没有指定默认值，那么程序会抛出一个异常。
 *
 * 要实现Model的findFirstById请将主键字段的Model类属性名设置为id。
 * 具体细节请参考http://docs.phalconphp.com/en/latest/reference/models.html
 *
 * @author dancebear <dancebear@gmail.com>
 * @since 1.0.0
 * @category Joy
 * @package Web
 * @copyright New BSD License
 * @copyright dancebear 2003-2014 <dancebear@ym85.com>
 *
 */
namespace Joy\Web;

use Joy\Web\BadRequestHttpException;
/**
 *
 * @property \Phalcon\Db\Adapter\Pdo $db
 * @property \Phalcon\Cache\Backend $cache
 * @property \Joy\Application $app
 * @property \Phalcon\Logger\Adapter $logger
 */
class Controller extends \Phalcon\Mvc\Controller
{
    /**
     * 登录使用的URL，如果无需登录请不要设置此参数
     * @var string
     */
    protected $loginRoute=null;
    
    /**
     * 解析URL及用户提交所获取到的数据
     * @var array
     */
    protected $data;
    /**
     * Initializes the controller
     * 在本方法里，我们对设置的GET、Route中取得属性及模型类中data属性进行了合并，
     * 并使用反射将数据绑定到了应用程序的动作的参数上。
     * 同时本方法还会将所有的数据设置给模型类的data属性；如果你需要对data属性是否为空进行
     * 判断，请在调用本方法前进行；
     * 如果你需要重新本方法，请务必使用``parent::initialize()``调用本方法以实现参数绑定
     * @throws BadRequestHttpException
     */
    public function initialize()
    {
        if ($this->data !== null) {
            $params = array_merge($this->request->getQuery(),$this->request->getPost(),$this->dispatcher->getParams(), $this->data);
        } else {
            $params = array_merge($this->request->getQuery(),$this->request->getPost(),$this->dispatcher->getParams());
        }
        $method = new \ReflectionMethod($this, $this->dispatcher->getActionName().'Action');
        $actionParams = $missing = [];
        foreach ($method->getParameters() as $parameter) {
            // 取参数名
            $name = $parameter->getName();
            
            // Get the expected model name
            if ($parameter->getClass() !== null) {
                $className = $parameter->getClass()->name;

                // Check if the parameter expects a model instance
                if (is_subclass_of($className, 'Phalcon\Mvc\Model')) {
                    if (isset($params[$name . '_id'])) {
                        $id = $params[$name . '_id'];
                    } else
                        if (isset($params['id'])) {
                            $id = $params['id'];
                        } else
                            if ($parameter->isDefaultValueAvailable()) {
                                $actionParams[$name] = $parameter->getDefaultValue();
                            } else
                                throw new BadRequestHttpException(\Joy::t('Joy', 'Invalid data received for parameter "{param}".', [
                                    'param' => $name
                                ]));
                    $model = $className::findById($id);
                    // Override the parameters by the model instance
                    $actionParams[$name] = $model instanceof \Phalcon\Mvc\Model ? $model : null;
                } else
                    $actionParams[$name] = null; // 非Model的对象直接设置为null
            } else {
                if (array_key_exists($name, $params)) {
                    //需要注意的是，如果需要定义参数为数组，请设置参数类型为array
                    if ($parameter->isArray()) {
                        $actionParams[$name] = is_array($params[$name]) ? $params[$name] : [
                            $params[$name]
                        ];
                    } elseif (! is_array($params[$name])) {
                        $actionParams[$name] = $params[$name];
                    } else {
                        throw new BadRequestHttpException(\Joy::t('Joy', 'Invalid data received for parameter "{param}".', [
                            'param' => $name
                        ]));
                    }
                    unset($params[$name]);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $actionParams[$name] = $parameter->getDefaultValue();
                } else {
                    $missing[] = $name;
                }
            }
        }
        if (! empty($missing)) {
            throw new BadRequestHttpException(\Joy::t('Joy', 'Missing required parameters: {params}', [
                'params' => implode(', ', $missing)
            ]));
        }
        $this->dispatcher->setParams($actionParams);
        $this->data = $params;
    }

    /**
     * 执行控制器前执行的操作
     *
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     * @throws \Joy\Web\HttpException
     * @return boolean
     */
    public function beforeExecuteRoute($dispatcher)
    {
        $module = $dispatcher->getModuleName();
        $action = $dispatcher->getActionName();
        $controller = $dispatcher->getControllerName();
        if($this->checkAccess(substr(md5($module . $controller . $action), 0, 6))) {
            return true;
        }else{
            if($this->loginRoute){                
                $this->response->redirect($this->loginRoute,true,301)->sendHeaders();
                exit;
            }else{
                throw new HttpException(403);
            }
        }
    }

    /**
     * 检查用户权限；你可以重写此方法以校验用户权限
     *
     * @param string $accessToken
     * @return boolean
     */
    public function checkAccess($accessToken)
    {
        return true;
    }

    /**
     * 渲染JSON
     *
     * @param array $data
     * @return \Phalcon\Http\ResponseInterface
     */
    protected function renderJson($data)
    {
        $this->response->setHeader('Content-Type', 'text/json');
        $this->response->setJsonContent($data);
        $this->dispatcher->setReturnedValue($this->response);
    }

    /**
     * 渲染字符串为HTML
     *
     * @param string $data
     */
    protected function renderHtml($data)
    {
        $this->response->setHeader('Content-Type', 'text/html');
        $this->response->setContent($data);
        $this->dispatcher->setReturnedValue($this->response);
    }

    /**
     * 执行控制器后执行的操作
     * 通过此方法，我们可以允许在返回字符串或数组时屏蔽框架的自动渲染视图
     * 功能。而无需使用Joy::$app->useImplicitView(false);或使用$this->view->disable();来
     * 手动禁用视图渲染。
     *
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     */
    public function afterExecuteRoute($dispatcher)
    {
        $data = $dispatcher->getReturnedValue();
        if (is_string($data))
            return $this->renderHtml($data);
        if (is_array($data))
            return $this->renderJson($data);
    }

    /**
     * 渲染指定的模板
     *
     * @param string $actionName
     * @param string $controllerName
     * @return string
     */
    public function render($actionName, $controllerName = null)
    {
        $controllerName = $controllerName === null ? $this->router->getControllerName() : $controllerName;
        $actionName = $actionName === null ? $this->router->getActionName() : $actionName;
        $this->view->render($controllerName, $actionName);
        $this->view->finish();
        return $this->view->getContent();
    }
}
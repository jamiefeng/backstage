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
namespace Joy;

/**
 * Joy\Debug
 *
 * Provides debug capabilities to Phalcon applications
 */
class Debug
{

    private $_uri = "http://static.ym85.com/debug/1.2.0/";

    private $_view;

    /**
     * Change the base URI for static resources
     *
     * @param
     *            string uri
     * @return Joy\Debug
     */
    public function setUri($uri)
    {
        $this->_uri = $uri;
        return $this;
    }

    /**
     * 设置错误显示的页面
     *
     * @param string $view
     */
    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * Listen for uncaught exceptions and unsilent notices or warnings
     *
     * @param string $exceptions
     * @param string $fatalError
     * @param string $error
     * @return \Joy\Debug
     */
    public function listen($exceptions = true, $fatalError = true, $error = false)
    {
        if ($exceptions) {
            $this->listenExceptions();
        }
        if ($error) {
            $this->listenError();
        }
        if ($fatalError) {
            $this->listenFatalError();
        }
        return $this;
    }

    /**
     * Listen for uncaught exceptions
     *
     * @return Joy\Debug
     */
    public function listenExceptions()
    {
        set_exception_handler([
            $this,
            "onUncaughtException"
        ]);
        return $this;
    }

    /**
     * Listen for uncaught error
     *
     * @return Joy\Debug
     */
    public function listenError()
    {
        set_error_handler([
            $this,
            "onUncaughtError"
        ]);
        return $this;
    }

    /**
     * Listen for fatal error
     *
     * @return Joy\Debug
     */
    public function listenFatalError()
    {
        register_shutdown_function([
            $this,
            "onUncaughtFatalError"
        ]);
        return $this;
    }

    /**
     * Escapes a string with htmlentities
     *
     * @param
     *            string value
     * @return string
     */
    protected function _escapeString($value)
    {
        if (is_string($value)) {
            return htmlentities(str_replace("\n", "\\n", $value), ENT_COMPAT, "utf-8");
        }
        return $value;
    }

    /**
     * Returns the major framework's version
     *
     * @return string
     */
    public function getMajorVersion()
    {
        $parts = explode(" ", \Phalcon\Version::get());
        return $parts[0];
    }

    /**
     * Generates a link to the current version documentation
     *
     * @return string
     */
    public function getVersion()
    {
        return "<div class=\"version\">Phalcon Framework <a target=\"_new\" href=\"http://docs.phalconphp.com/en/" . $this->getMajorVersion() . "/\">" . \Phalcon\Version::get() . "</a></div>";
    }

    /**
     * Returns the css sources
     *
     * @return string
     */
    public function getCssSources()
    {
        $uri = $this->_uri;
        $sources = "<link href=\"" . $uri . "jquery/jquery-ui.css\" type=\"text/css\" rel=\"stylesheet\" />";
        $sources .= "<link href=\"" . $uri . "themes/default/style.css\" type=\"text/css\" rel=\"stylesheet\" />";
        return '';//$sources;
    }

    public function onUncaughtFatalError()
    {
        return true;
    }

    /**
     * Handles uncaught exceptions
     *
     * @param
     *            \Exception exception
     * @return boolean
     */
    public function onUncaughtException($exception)
    {
        $obLevel = ob_get_level();

        /**
         * Cancel the output buffer if active
         */
        if ($obLevel > 0) {
            ob_end_clean();
        }

        //$className = get_class($exception);

        if ($exception instanceof \Joy\Web\HttpException) {
            $code = $exception->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();
        } else {
            $code = 500;
            $name = 'Service Unavailable';
            $message = $exception->getMessage();
        }

        /**
         * Print the HTML, @TODO, add an option to store the html
         */
        $response = new \Phalcon\Http\Response();
        $response->setStatusCode($code, $name);
        if(\Joy::$config->render == 'json')
            $response->setJsonContent(['status'=>$code,'message'=>$message]);
        else
            $response->setContent($this->render($code, $name, $message));
        $response->send();

        return true;
    }

    /**
     * 渲染错误页面
     *
     * @param int $code
     * @param string $name
     * @param string $message
     * @return string
     */
    private function render($code,$name, $message)
    {
        if ($this->_view == null) {
            $html = $this->defaultRender($code,$name, $message);
        } else {
            ob_start();
            include $this->_view;
            $html = ob_get_contents();
            ob_end_clean();
        }
        return $html;
    }

    /**
     * 默认渲染的页面
     *
     * @param integer $code
     * @param string $name
     * @param string $message
     * @return string
     */
    private function defaultRender($code,$name, $message)
    {
        /**
         * CSS static sources to style the error presentation
         * Use the exception info as document"s title
         */
        $html = "<html><head><title>" . $name . "</title>";
        $html .= $this->getCssSources() . "</head><body>";

        /**
         * Get the version link
         */
        // $html .= $this->getVersion();

        /**
         * Main exception info
         */
        $html .= "<div align=\"center\"><div class=\"error-main\">";
        $html .= "<h1>" . $message . "</h1>";
        $html .= "<p>
        The above error occurred while the Web server was processing your request.
        </p>
        <p>
        Please contact us if you think this is a server error. Thank you.
        </p>";
        $html .= "</div>";
        $html .= "</div></body></html>";
        return $html;
    }
}
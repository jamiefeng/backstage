<?php

/**
 * 根据PHPDOC来判断是否执行缓存类
 * @author dancebear <dancebear@gmail.com>
 *
 */
namespace Joy\Web;

class CacheAnnotation extends \Phalcon\Mvc\User\Plugin
{

    /**
     * This event is executed before every route is executed in the dispatcher
     */
    public function beforeExecuteRoute($event, $dispatcher)
    {
        // Parse the annotations in the method currently executed
        $annotations = $this->annotations->getMethod($dispatcher->getActiveController(), $dispatcher->getActiveMethod());
        
        // Check if the method has an annotation 'Cache'
        if ($annotations->has('Cache')) {
            
            // The method has the annotation 'Cache'
            /**
             * @var \Phalcon\Annotations\Annotation $annotation
             */
            $annotation = $annotations->get('Cache');
            
            // Get the lifetime
            $lifetime = $annotation->getNamedArgument('lifetime');
            
            $options = array(
                'lifetime' => $lifetime
            );
            
            // Check if there is a user defined cache key
            if ($annotation->hasNamedArgument('key')) {
                $options['key'] = $annotation->hasNamedArgument('key');
            }
            
            // Enable the cache for the current method
            $this->view->cache($options);
        }
    }
}
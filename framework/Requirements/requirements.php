<?php
/**
 * These are the core requirements for the [[RequirementChecker]] instance.
 * These requirements are mandatory for any application.
 */

/* @var $this RequirementChecker */
return array(
    array(
        'name' => 'PHP version',
        'mandatory' => true,
        'condition' => version_compare(PHP_VERSION, '5.4.0', '>='),
        'by' => '<a href="http://www.php.com">Joy Framework</a>',
        'memo' => 'PHP 5.4.0 or higher is required.'
    ),
    array(
        'name' => 'Reflection extension',
        'mandatory' => true,
        'condition' => class_exists('Reflection', false),
        'by' => '<a href="http://www.php.com">Joy Framework</a>'
    ),
    array(
        'name' => 'PCRE extension',
        'mandatory' => true,
        'condition' => extension_loaded('pcre'),
        'by' => '<a href="http://www.php.com">Joy Framework</a>'
    ),
    array(
        'name' => 'SPL extension',
        'mandatory' => true,
        'condition' => extension_loaded('SPL'),
        'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>'
    ),
    array(
        'name' => 'MBString extension',
        'mandatory' => true,
        'condition' => extension_loaded('mbstring'),
        'by' => '<a href="http://www.php.net/manual/en/book.mbstring.php">Multibyte string</a> processing',
        'memo' => 'Required for multibyte encoding string processing.'
    ),
    array(
        'name' => 'Mcrypt extension',
        'mandatory' => false,
        'condition' => extension_loaded('mcrypt'),
        'by' => '<a href="http://www.php.com">Joy Framework</a>',
        'memo' => 'Required by encrypt and decrypt methods.'
    ),
    array(
        'name' => 'Fileinfo extension',
        'mandatory' => false,
        'condition' => extension_loaded('fileinfo'),
        'by' => '<a href="http://www.php.net/manual/en/book.fileinfo.php">File Information</a>',
        'memo' => 'Required for files upload to detect correct file mime-types.'
    ),
    array(
        'name' => 'DOM extension',
        'mandatory' => false,
        'condition' => extension_loaded('dom'),
        'by' => '<a href="http://php.net/manual/en/book.dom.php">Document Object Model</a>',
        'memo' => 'Required for REST API to send XML responses via <code>yii\web\XmlResponseFormatter</code>.'
    )
);

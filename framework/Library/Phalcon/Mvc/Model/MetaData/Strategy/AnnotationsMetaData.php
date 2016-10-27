<?php
namespace Phalcon\Mvc\Model\MetaData\Strategy;

use Phalcon\Mvc\ModelInterface, Phalcon\DiInterface, Phalcon\Mvc\Model\MetaData, Phalcon\Db\Column;

class AnnotationsMetaData
{

    /**
     * 初始化模型的元数据
     *
     * @param \Phalcon\Mvc\ModelInterface $model            
     * @param \Phalcon\DiInterface $di            
     *
     * @return array
     */
    public function getMetaData(ModelInterface $model, DiInterface $di)
    {
        $reflection = $di['annotations']->get($model);
        $properties = $reflection->getPropertiesAnnotations();
        
        $attributes = array();
        $nullables = array();
        $dataTypes = array();
        $dataTypesBind = array();
        $numericTypes = array();
        $primaryKeys = array();
        $nonPrimaryKeys = array();
        $identity = false;
        
        foreach ($properties as $name => $collection) {
            
            if ($collection->has('Column')) {
                
                $arguments = $collection->get('Column')->getArguments();
                
                /**
                 * Get the column's name
                 */
                if (isset($arguments['column'])) {
                    $columnName = $arguments['column'];
                } else {
                    $columnName = $name;
                }
                
                /**
                 * 处理列注释的类型参数
                 */
                if (isset($arguments['type'])) {
                    switch ($arguments['type']) {
                        case 'integer':
                            $dataTypes[$columnName] = Column::TYPE_INTEGER;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_INT;
                            $numericTypes[$columnName] = true;
                            break;
                        case 'string':
                            $dataTypes[$columnName] = Column::TYPE_VARCHAR;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'char':
                            $dataTypes[$columnName] = Column::TYPE_CHAR;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'text':
                            $dataTypes[$columnName] = Column::TYPE_TEXT;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'float':
                            $dataTypes[$columnName] = Column::TYPE_FLOAT;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'double':
                            $dataTypes[$columnName] = Column::TYPE_DOUBLE;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'decimal':
                            $dataTypes[$columnName] = Column::TYPE_DECIMAL;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_DECIMAL;
                            break;
                        case 'boolean':
                            $dataTypes[$columnName] = Column::TYPE_BOOLEAN;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_BOOL;
                            break;
                        case 'date':
                            $dataTypes[$columnName] = Column::TYPE_DATE;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                        case 'datetime':
                            $dataTypes[$columnName] = Column::TYPE_DATETIME;
                            $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                            break;
                    }
                } else {
                    $dataTypes[$columnName] = Column::TYPE_VARCHAR;
                    $dataTypesBind[$columnName] = Column::BIND_PARAM_STR;
                }
                
                /**
                 * 处理列注释的'nullable'参数
                 */
                if (! $collection->has('Identity')) {
                    if (isset($arguments['nullable'])) {
                        if (! $arguments['nullable']) {
                            $nullables[] = $columnName;
                        }
                    }
                }
                
                $attributes[] = $columnName;
                
                /**
                 * 检查是否为主键
                 */
                if ($collection->has('Primary')) {
                    $primaryKeys[] = $columnName;
                } else {
                    $nonPrimaryKeys[] = $columnName;
                }
                
                /**
                 * 属性是否表示为Identity
                 */
                if ($collection->has('Identity')) {
                    $identity = $columnName;
                }
            }
        }
        
        return array(
            
            // Every column in the mapped table
            MetaData::MODELS_ATTRIBUTES => $attributes,
            
            // Every column part of the primary key
            MetaData::MODELS_PRIMARY_KEY => $primaryKeys,
            
            // Every column that isn't part of the primary key
            MetaData::MODELS_NON_PRIMARY_KEY => $nonPrimaryKeys,
            
            // Every column that doesn't allows null values
            MetaData::MODELS_NOT_NULL => $nullables,
            
            // Every column and their data types
            MetaData::MODELS_DATA_TYPES => $dataTypes,
            
            // The columns that have numeric data types
            MetaData::MODELS_DATA_TYPES_NUMERIC => $numericTypes,
            
            // The identity column, use boolean false if the model doesn't have
            // an identity column
            MetaData::MODELS_IDENTITY_COLUMN => $identity,
            
            // How every column must be bound/casted
            MetaData::MODELS_DATA_TYPES_BIND => $dataTypesBind,
            
            // Fields that must be ignored from INSERT SQL statements
            MetaData::MODELS_AUTOMATIC_DEFAULT_INSERT => array(),
            
            // Fields that must be ignored from UPDATE SQL statements
            MetaData::MODELS_AUTOMATIC_DEFAULT_UPDATE => array()
        )
        ;
    }

    /**
     * 初始化模型
     *
     * @param \Phalcon\Mvc\ModelInterface $model            
     * @param \Phalcon\DiInterface $di            
     *
     * @return array
     */
    public function getColumnMaps(ModelInterface $model, DiInterface $di)
    {
        $reflection = $di['annotations']->get($model);
        
        $columnMap = array();
        $reverseColumnMap = array();
        
        $renamed = false;
        foreach ($reflection->getPropertiesAnnotations() as $name => $collection) {
            
            if ($collection->has('Column')) {
                
                $arguments = $collection->get('Column')->getArguments();
                
                /**
                 * 列名
                 */
                if (isset($arguments['column'])) {
                    $columnName = $arguments['column'];
                } else {
                    $columnName = $name;
                }
                
                $columnMap[$columnName] = $name;
                $reverseColumnMap[$name] = $columnName;
                
                if (! $renamed) {
                    if ($columnName != $name) {
                        $renamed = true;
                    }
                }
            }
        }
        
        if ($renamed) {
            return array(
                MetaData::MODELS_COLUMN_MAP => $columnMap,
                MetaData::MODELS_REVERSE_COLUMN_MAP => $reverseColumnMap
            );
        }
        
        return null;
    }
}
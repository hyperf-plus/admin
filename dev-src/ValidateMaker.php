<?php

namespace Mzh\DevTools;

use Mzh\Validate\Validate\Validate;
use Nette\PhpGenerator\PhpNamespace;

class ValidateMaker extends AbstractMaker
{
    public function make($model_class, $path, $config)
    {
        $arr = explode('\\', $model_class);
        $model_name = end($arr);
        $controller_name = $model_name . 'Validation';
        $class_namespace = $this->pathToNamespace($path);
        $save_path = BASE_PATH . '/' . $path . '/' . $controller_name . '.php';
        /** @var \Nette\PhpGenerator\ClassType $class */
        /** @var PhpNamespace $namespace */
        [
            $namespace,
            $class,
        ] = $this->getBaseClass($save_path, $class_namespace, $controller_name, Validate::class);
        $form = $config['form'] ?? [];
        if (!$form) {
            return false;
        }
        $rules = [];
        $field = [];
        $pkName = current($form)['field'] ?? 'id';
        $fillable = [];
        foreach ($form as $item) {
            if ($item['rule'] == '') {
                $item['rule'] = [];
            }
            if ($item['field'] == $pkName) {
                $item['rule'][] = 'require|integer|gt:0';
            }
            if (empty($item['rule'])) {
                $item['rule'][] = 'max:255';
            }
            if (isset($item['diy_rule'])) {
                $item['rule'][] = $item['diy_rule'];
            }
            $rules[$item['field']] = trim(implode('|', array_unique((array)$item['rule'])),'|');
            $field[$item['field']] = $item['label'];
            if ($item['field'] == $pkName) continue;
            $fillable[] = $item['field'];
        }

        $field['page'] = '页码';
        $field['limit'] = '每页条数';

        $rules['page'] = 'integer';
        $rules['limit'] = 'integer|gt:0';
        $scene = [
            'update' => $fillable,
            'list' => ['limit', 'page'],
            'sort' => ['sort'],
            'status' => ['status' => 'require|in:0,1'],
            'create' => $fillable
        ];
        $class->addProperty('rule', $rules)->setProtected();
        $class->addProperty('field', $field)->setProtected();
        $class->addProperty('scene', $scene)->setProtected();
        $code = $this->getNamespaceCode($namespace);
        if (file_put_contents($save_path, $code) === false) {
            return false;
        }
        return $class_namespace . '\\' . $controller_name;
    }
}

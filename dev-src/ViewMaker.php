<?php

namespace Mzh\DevTools;

use Mzh\Admin\Views\UiViewInterface;
use Nette\PhpGenerator\PhpNamespace;

class ViewMaker extends AbstractMaker
{
    public function make($model_class, $path, $config)
    {
        $arr = explode('\\', $model_class);
        $model_name = end($arr);
        $controller_name = $model_name . 'View';
        $class_namespace = $this->pathToNamespace($path);
        $save_path = BASE_PATH . '/' . $path . '/' . $controller_name . '.php';
        /** @var \Nette\PhpGenerator\ClassType $class */
        /** @var PhpNamespace $namespace */
        [
            $namespace,
            $class,
        ] = $this->getBaseClassImplements($save_path, $class_namespace, $controller_name, UiViewInterface::class);

        $form = $config['form'] ?? [];
        if (!$form) {
            return false;
        }
        $route = $this->splitToRouteName($model_name);
        $options = $this->optionsMake($config, $route);

        $class->addMethod('scaffoldOptions')->setBody("return " . $this->arrayStr($options) . ";");
        $code = $this->getNamespaceCode($namespace);
        if (file_put_contents($save_path, $code) === false) {
            return false;
        }
        return $class_namespace . '\\' . $controller_name;
    }

    public function splitToRouteName($greatHumpStr)
    {
        $arr = preg_split('/(?<=[a-z0-9])(?=[A-Z])/x', $greatHumpStr);
        return strtolower(implode("_", $arr));
    }

    public function optionsMake($data, $route)
    {
        $options = [];
        $yes = [
            'createAble',
            'exportAble',
            'deleteAble',
            'defaultList',
            'filterSyncToQuery',
        ];
        foreach ($yes as $item) {
            if (!in_array($item, $data['base_init'])) {
                $options[$item] = false;
            }
        }
        $not = [];
        foreach ($not as $item) {
            if (in_array($item, $data['base_init'])) {
                $options[$item] = false;
            }
        }
        $options['form'] = $this->transForm($data['form'] ?? []);
        if (in_array('editButton', $data['base_init'] ?? [])) {
            $options['table']['rowActions'][] = [
                'type' => 'jump',
                'target' => "/{$route}/{id}",
                'text' => '编辑',
            ];
        }
        if (in_array('deleteButton', $data['base_init'] ?? [])) {
            $options['table']['rowActions'][] = [
                'type' => 'api',
                'target' => "/{$route}/delete",
                'text' => '删除',
                'props' => [
                    'type' => 'danger',
                ],
            ];
        }

        return $options;
    }

    public function transForm($form)
    {
        $rules['id|#'] = '';
        $have_option_type = ['select', 'checkbox', 'radio'];
        foreach ($form as $item) {
            $key = $item['label'] ? $item['field'] . '|' . $item['label'] : $item['field'];
            $rules[$key] = [];
            if ($item['type'] !== 'input') {
                $rules[$key]['type'] = $item['type'];
            }
            if ($item['rule']) {
                $rules[$key]['rule'] = implode('|', $item['rule']);
            }
            if ($item['props']) {
                $rules[$key]['props'] = $item['props'];
            }
            if ($item['default']) {
                $rules[$key]['default'] = $item['default'];
            }
            if ($item['info'] ?? '') {
                $rules[$key]['info'] = $item['info'];
            }
            if (count($rules[$key]) == 1 && isset($rules[$key]['rule'])) {
                $rules[$key] = $rules[$key]['rule'];
            }
            if (in_array($item['type'] ?? '', $have_option_type) && isset($item['options']) && $item['options']) {
                $rules[$key]['options'] = $item['options'];
            }
            if ($item['field'] == 'id') {
                $rules[$key] = [];
            }
            if ($rules[$key] === []) {
                $rules[$key] = '';
            }
        }

        return $rules;
    }
}

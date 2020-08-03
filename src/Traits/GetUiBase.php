<?php

namespace Mzh\Admin\Traits;


use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use Mzh\Validate\Validate\Validate;

trait GetUiBase
{
    protected function formComputeConfig($form)
    {
        $compute_map = [];
        $options_map = [];
        foreach ($form as $item) {
            if (isset($item['depend'])) {
                $compute_map[$item['depend']['field']][$item['field']][] = [
                    'when' => [
                        [
                            $item['depend']['field'],
                            is_array($item['depend']['value']) ? 'not_in' : '!=',
                            $item['depend']['value'],
                        ],
                    ],
                    'set' => [
                        'type' => 'hidden',
                    ],
                ];
            }
            if (isset($item['hidden'])) {
                $compute_map[$item['field']][$item['hidden']['field']][] = [
                    'when' => [[$item['field'], '=', $item['hidden']['value']]],
                    'set' => [
                        'type' => 'hidden',
                    ],
                ];
            }
            if (isset($item['compute'])) {
                if (isset($item['compute']['when'])) {
                    foreach ($item['compute']['set'] as &$set) {
                        $set = $this->formComputeSetConvert($set);
                        unset($set);
                    }
                    foreach ($item['compute']['set'] as $key => $detail) {
                        $compute_map[$item['field']][$key][] = [
                            'when' => [array_merge([$item['field']], $item['compute']['when'])],
                            'set' => $detail,
                        ];
                    }
                }
                if (isset($item['compute'][0])) {
                    foreach ($item['compute'] as $each) {
                        foreach ($each['set'] as &$set) {
                            $set = $this->formComputeSetConvert($set);
                            unset($set);
                        }
                        foreach ($each['set'] as $key => $detail) {
                            $compute_map[$item['field']][$key][] = [
                                'when' => [array_merge([$item['field']], $each['when'])],
                                'set' => $detail,
                            ];
                        }
                    }
                }
            }
            if (isset($item['options'])) {
                foreach ($item['options'] as $each) {
                    $options_map[$item['field']][] = [
                        'value' => $each['value'],
                        'label' => $each['label'],
                    ];
                    if (!isset($each['disabled_when'])) {
                        continue;
                    }
                    if (is_string($each['disabled_when'][0])) {
                        $compute_map[$each['disabled_when'][0]][$item['field']][] = [
                            'when' => [$each['disabled_when']],
                            'set' => [
                                'options' => [
                                    [
                                        'value' => $each['value'],
                                        'label' => $each['label'],
                                        'disabled' => true,
                                    ],
                                ],
                            ],
                        ];
                        continue;
                    }
                    foreach ($each['disabled_when'] as $line) {
                        $compute_map[$line[0]][$item['field']][] = [
                            'when' => $each['disabled_when'],
                            'set' => [
                                'options' => [
                                    [
                                        'value' => $each['value'],
                                        'label' => $each['label'],
                                        'disabled' => true,
                                    ],
                                ],
                            ],
                        ];
                    }
                }
            }
        }
        $real_map = [];
        foreach ($compute_map as $change_field => $item) {
            foreach ($item as $affect_field => $parts) {
                $group = [];
                foreach ($parts as $part) {
                    $key = json_encode($part['when']);
                    if (!isset($group[$key])) {
                        $group[$key] = $part['set'];
                    } else {
                        $group[$key] = array_merge_recursive($group[$key], $part['set']);
                    }
                }
                $cell = [];
                foreach ($group as $when => $set) {
                    if (isset($set['options'])) {
                        $set['options'] = array_merge_node($options_map[$affect_field] ?? [], $set['options'], 'value');
                    }
                    $cell[] = [
                        'when' => json_decode($when, true),
                        'set' => $set,
                    ];
                }
                $real_map[$change_field][$affect_field] = $cell;
            }
        }
        return $real_map;
    }

    protected function formComputeSetConvert($cell)
    {
        if (isset($cell['rule'])) {
            $validate = $this->validateOptions('input', '', explode('|', $cell['rule']));
            $cell['validate'] = $validate;
            unset($cell['rule']);
        }
        foreach ($cell as $key => $value) {
            if (is_callable($value)) {
                $cell[$key] = call($value, [$cell]);
            }
        }
        return $cell;
    }

    protected function buttonConfigConvert($config)
    {
        $buttons = [];
        foreach ($config as $key => $item) {
            $buttons[$key]['text'] = $item['text'] ?? '';
            $buttons[$key]['type'] = isset($item['target']) ? $item['type'] ?? 'jump' : (isset($item['rules']) ? 'form' : (isset($item['api']) ? 'api' : 'jump'));
            $buttons[$key]['target'] = isset($item['target']) ? $item['target'] : (isset($item['api']) ? $item['api'] : ($item['action'] ?? ''));
            $buttons[$key]['props'] = isset($item['target']) ? $item['props'] ?? [] : [
                'icon' => $item['icon'] ?? '',
                'circle' => $item['circle'] ?? false,
                'size' => $item['size'] ?? 'small',
                'type' => $item['type'] ?? '',
            ];
            if (isset($item['rules'])) {
                $form = $this->formOptionsConvert($item['rules']);
                $buttons[$key]['rules'] = $this->formResponse(0, $form);
                unset($form);
            }
            $buttons[$key]['rules']['form_ui'] = $item['formUi'] ?? [];
            if (isset($item['when'])) {
                $buttons[$key]['when'] = $item['when'];
            }
            // 批量操作的数据过滤
            if (isset($item['selectFilter'])) {
                $buttons[$key]['selectFilter'] = $item['selectFilter'];
            }
            if (isset($item[0])) {
                $buttons[$key] = $this->buttonConfigConvert($item);
            }
        }
        return $buttons;
    }

    protected function formResponse($id, $form)
    {
        if (method_exists($this, 'meddleFormRule')) {
            $this->meddleFormRule($id, $form);
        }
        $compute_map = $this->formComputeConfig($form);
        return [
            'form' => $form,
            'compute_map' => (object)$compute_map,
            'form_ui' => (object)($this->options['formUI'] ?? []),
        ];
    }

    /**
     * 表单配置转换
     *
     * @param array $formOption 表单配置
     * @param bool $full 是否为全部, false 则会过滤虚拟字段
     * @param bool $edit 是否为编辑模式, 用于处理 新增或编辑 时, 字段的只读问题
     * @param bool $filter 是否为filter模式,用于处理id字段的type
     * @param array $default
     * @param int $depth 深度
     *
     * @return array
     */
    protected function formOptionsConvert($formOption = [], $full = false, $edit = true, $filter = false, $default = [], $depth = 0)
    {
        if (!$formOption) {
            $formOption = $this->options['form'] ?? [];
        }
        $form = [];
        foreach ($formOption as $key => $val) {
            $field_extra = explode('|', $key);
            $field = $field_extra[0];
            $title = $field_extra[1] ?? $field_extra[0];
            $biz = [];
            if (is_string($val)) {
                $biz['rule'] = $val;
                $biz['type'] = 'input';
            } else {
                $biz = $val;
            }
            if ($full === false && ($biz['form'] ?? true) === false) {
                continue;
            }
            $rule = $biz['rule'] ?? '';
            $rules = is_array($rule) ? $rule : explode('|', $rule);
            $_form = [
                'title' => $title,
                'field' => $field,
                'type' => $biz['type'] ?? 'input',
                'value' => Arr::get(array_merge(request()->all(), $default ?: []), $field, $biz['default'] ?? ''),
            ];
            switch ($_form['type']) {
                case 'checkbox':
                case 'cascader':
                    $_form['value'] = array_map('intval', is_array($_form['value']) ? $_form['value'] : (array)$_form['value']);
                    break;
                case 'image':
                    $biz['props']['limit'] = $biz['props']['limit'] ?? 1;
                    break;
                case 'select':
                    if (isset($biz['props']['selectApi']) && $_form['value']) {
                        $biz['options'] = select_options($biz['props']['selectApi'], is_array($_form['value']) ? $_form['value'] : explode(',', $_form['value']));
                    }
                    // fixme sub-form value 不好取, 先默认查一次
                    if (isset($biz['props']['selectApi']) && $depth) {
                        $biz['options'] = select_options($biz['props']['selectApi'], is_array($_form['value']) ? $_form['value'] : explode(',', $_form['value']));
                    }
                    break;
                default:
                    break;
            }
            $validate = $this->validateOptions($_form['type'], $_form['title'], $rules);
            if (isset($biz['children'])) {
                $biz['props']['rules'] = $this->formOptionsConvert($biz['children'], $full, $edit, $filter, Arr::get($field, $default, []), $depth + 1);
                $biz['props']['computeMap'] = (object)$this->formComputeConfig($biz['props']['rules']);
                $biz['props']['repeat'] = $biz['repeat'] ?? false;
                $_form['value'] = is_array($_form['value']) ? $_form['value'] : [];
            }
            if ($validate) {
                $_form['validate'] = $validate;
            }
            if (!$filter && $field == $this->getPk()) {
                $_form['type'] = 'hidden';
            }
            if ($biz['props'] ?? false) {
                $_form['props'] = $biz['props'];
            }
            if ($biz['col'] ?? false) {
                $_form['col'] = $biz['col'];
            }
            if ($biz['info'] ?? false) {
                $_form['info'] = $biz['info'];
            }
            if (isset($biz['depend'])) {
                $_form['depend'] = $biz['depend'];
            }
            if (isset($biz['hidden'])) {
                $_form['hidden'] = $biz['hidden'];
            }
            if (isset($biz['custom'])) {
                $_form['custom'] = (bool)$biz['custom'];
            }
            if (isset($biz['virtual_field'])) {
                $_form['virtual_field'] = (bool)$biz['virtual_field'];
            }
            if (isset($biz['readonly']) && $edit) {
                $_form['props']['disabled'] = (bool)$biz['readonly'];
            }
            if (isset($biz['section'])) {
                $_form['section'] = $biz['section'];
            }
            if (isset($biz['compute'])) {
                $_form['compute'] = $biz['compute'];
            }
            if (isset($biz['search_type'])) {
                $_form['search_type'] = $biz['search_type'];
            }
            if (isset($biz['options']) && is_callable($biz['options'])) {
                $_form['options'] = $biz['options']($field, $default);
            } elseif (($biz['options'] ?? false) && ($biz['type'] != 'cascader')) {
                $value_label = [];
                $first = current($biz['options']);
                if (!isset($first['value'])) {
                    foreach ($biz['options'] as $value => $label) {
                        $value_label[] = is_array($label) ? $label : [
                            'value' => $value,
                            'label' => $label,
                        ];
                    }
                } else {
                    $value_label = $biz['options'];
                }
                $_form['options'] = $value_label;
            }
            if ($filter
                && in_array($_form['type'], [
                    'radio',
                    'select',
                    'checkbox',
                ])
                && isset($_form['options'])) {
                $_form['type'] = 'select';
                unset($_form['value']);
                $options_labels = array_column($_form['options'], 'label');
                if (!isset($_form['props']['selectApi']) && !in_array('全部', $options_labels)) {
                    array_unshift($_form['options'], [
                        'value' => '',
                        'label' => '全部',
                    ]);
                }
            }
            if (isset($biz['copy_show'])) {
                $_form['copy_show'] = $biz['copy_show'];
            }
            if (isset($biz['render']) && is_callable($biz['render'])) {
                $biz['render']($field, $_form);
            }
            if (isset($biz['filterConvert'])) {
                $_form['filterConvert'] = $biz['filterConvert'];
            }
            $form[] = $_form;
        }
        return $form;
    }


    /**
     * 从form options中提取field对应的form options的key
     *
     * @return array
     */
    protected function getFormFieldMap()
    {
        $form_options = $this->options['form'] ?? [];
        if (empty($form_options)) {
            return [];
        }
        return collect(array_keys($form_options))->mapWithKeys(function ($item) {
            $field_extra = explode('|', $item);
            return [$field_extra[0] => $item];
        })->toArray();
    }

    /**
     * 获取列表的表头
     */
    protected function getListHeader()
    {
        $form = $this->formOptionsConvert();
        array_change_v2k($form, 'field');
        $table_options = $this->options['table']['columns'] ?? [];
        $headers = [];
        foreach ($table_options as $item) {
            if (is_string($item)) {
                $header = [
                    'title' => $form[$item]['title'] ?? $item,
                    'field' => $form[$item]['field'] ?? $item,
                    'type' => $form[$item]['type'] ?? '',
                    'virtual_field' => $form[$item]['virtual_field'] ?? false,
                    'sortable' => false,
                ];
            } else {
                $header = array_merge(!empty($form[$item['field']]) ? [
                    'type' => $item['type'] ?? $form[$item['field']]['type'],
                    'title' => $form[$item['field']]['title'],
                    'sortable' => $item['sortable'] ?? false,
                    'virtual_field' => $item['virtual_field'] ?? false,
                ] : [], $item);
            }
            if ($form[$header['field']]['options'] ?? false) {
                $options = [];
                foreach ($form[$header['field']]['options'] as $each) {
                    $options[$each['value']] = $each['label'];
                }
                $header['options'] = $options;
            }
            $headers[] = $header;
        }
        if (!$table_options) {
            foreach ($form as $item) {
                $headers[] = [
                    'title' => $item['title'],
                    'field' => $item['field'],
                    'sortable' => $item['sortable'] ?? false,
                    'virtual_field' => $item['virtual_field'] ?? false,
                ];
            }
        }
        return $headers;
    }

    /**
     * laravel-validation -> async-validator
     * 脚手架字段约束转换
     *
     * @param string $type 字段类型
     * @param string $title 字段中文名
     * @param array $rules 字段约束
     *
     * @return array
     */
    protected function validateOptions($type, $title, $rules)
    {
        $validates = [];
        foreach ($rules as $item) {
            $parts = explode(':', $item);
            $rule = array_shift($parts);
            switch ($rule) {
                case 'required':
                    $validates[] = [
                        'required' => true,
                        'message' => '请输入' . $title,
                        //'type' => $type,
                        'trigger' => 'blur',
                    ];
                    break;
            }
        }
        return $validates;
    }

    /**
     * 获取表单的约束
     */
    protected function getFormRules($options = null)
    {
        $formOptions = $options ? $options : ($this->options['form'] ?? []);
        $rules = [];
        foreach ($formOptions as $key => $val) {
            if (is_array($val) && ($val['form'] ?? true) === false) {
                continue;
            }
            if (is_string($val)) {
                $rules[$key] = $val;
                continue;
            }
            if (is_array($val) && ($val['rule'] ?? false)) {
                $rules[$key] = $val['rule'];
            } else {
                $rules[$key] = '';
            }
            if (isset($val['children']) && is_array($val['children'])) {
                $rules[$key] = [
                    'children' => [
                        'rules' => $this->getFormRules($val['children']),
                        'repeat' => $val['repeat'] ?? false,
                    ],
                ];
            }
        }
        return $rules;
    }

    protected function check($rules, $data, $obj = null, $options = [])
    {
        foreach ($data as $key => $val) {
            if (strpos($key, '.') !== false) {
                Arr::set($data, $key, $val);
                unset($data[$key]);
            }
        }
        $map = [];
        $real_rules = [];
        $white_data = [];
        foreach ($rules as $key => $rule) {
            $field_extra = explode('|', $key);
            $field = $field_extra[0];
            if (!$rule && Arr::get($data, $field)) {
                $white_data[$field] = Arr::get($data, $field);
                continue;
            }
            $title = $field_extra[1] ?? $field_extra[0];
            $rules = is_array($rule) ? $rule : explode('|', $rule);
            foreach ($rules as $index => &$item) {
                if ($index === 'children') {
                    $request_sub_data = Arr::get($data, $field);
                    if ($item['repeat']) {
                        foreach ($request_sub_data as $part_index => $part) {
                            [
                                $sub_data,
                                $sub_error,
                            ] = $this->check($item['rules'], $part);
                            if ($sub_error) {

                                $sub_error[] = $title . '的第' . ($part_index + 1) . '项 ' . current($sub_error);

                                return [$sub_data, $sub_error];
                            }
                        }
                    } else {
                        [
                            $sub_data,
                            $sub_error,
                        ] = $this->check($item, $request_sub_data);
                        if ($sub_error) {
                            $sub_error[0] = $title . '中的 ' . $sub_error[0];

                            return [$sub_data, $sub_error];
                        }
                    }
                    unset($item);
                    continue;
                }
                if ($item == 'json') {
                    $item = 'array';
                }
//                if (method_exists($this, $item)) {
//                    $item = $this->makeCustomRule($item);
//                } elseif (is_string($item) && Str::startsWith($item, 'call_')) {
//                    $item = $this->makeCustomRule(Str::replaceFirst('call_', '', $item));
//                } elseif (is_string($item) && Str::startsWith($item, 'cb_')) {
//                    $item = $this->makeObjectCallback(Str::replaceFirst('cb_', '', $item), $obj);
//                }
                unset($item);
            }
            if ($field == 'form' && is_array($rules)) {
                continue;
            }
            $real_rules[$field] = $rules;
            $map[$field] = $title;
        }
        $validator = new Validate();
        $validator->batch(true);
        $fails = $validator->check($data, $real_rules);
        $errors = [];
        if (!$fails) {
            $errors = $validator->getError();
            foreach ($errors as &$item) {
                $filed_keys = array_keys($map);
                $filed_keys = array_sort_by_value_length($filed_keys);
                $replace = [];
                foreach ($filed_keys as $k) {
                    $replace[] = $map[$k];
                }
                $map = array_sort_by_key_length($map);
                $filed_keys = array_map(function ($key) {
                    if (strpos($key, '.') === false) {
                        return str_replace('_', ' ', $key);
                    }
                    return $key;
                }, $filed_keys);
                if (preg_match('/.*当 (.*) 是 (.*)/', $item, $m)) {
                    if (isset($m[1]) && isset($m[2])) {
                        $field = str_replace(' ', '_', $m[1]);
                        $option = $options[$field][$m[2]];
                        $item = preg_replace('/是 .*/', '是 ' . $option, $item);
                    }
                }

                $item = str_replace($filed_keys, $replace, $item);
                $item = str_replace('字段', '', $item);
                unset($item);
            }

            return [
                null,
                $errors,
            ];
        }

        return [
            !$fails ? null : $data,
            $errors,
        ];
    }
}
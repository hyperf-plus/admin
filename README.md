#### 开发中..
#### 以插件形式开箱即用
#### 可以做到无需VUE前端可实现快速开发各种表单
#### 后台可视化生成控制器、模型、验证器、View代码等
#### 后台表单、列表生成

#### 安装 (开发中，暂时不要使用)
```
 composer require mzh/hyperf-admin-plugin
```

#### 本项目在hyperf-admin/hyperf-admin基础上优化、并简化处理，搭配验证器，swagger文档，让您开发后台更加迅速、开箱即用

![Image 注解](./screenshot/1.png)
![Image 文档](./screenshot/2.png)
![Image 注解](./screenshot/3.png)
![Image 文档](./screenshot/4.png)

#### 脚手架工具 在控制器只需引用一下代码即可生成对应接口并注册路由、生成Swagger文档。简单易用，无需过多步骤。
    以下操作也可以在控制面板可视化生成
    
    - 全功能接口
    - 如需要以下全部功能 只需引用  use GetApiAction;
    
    - 自定义选择单项接口
    -前端UI接口       只需 use GetApiUi;
    -列表            只需 use GetApiList; （钩子中支持增加搜索条件、参数见下文钩子）
    -创建            只需 use GetApiCreate; （支持验证提交内容、创建前数据检测、参数见下文钩子）
    -删除            只需 use GetApiDelete; 
    -批量删除         只需 use GetApiDelete; 
    -更新            只需 use GetApiUpdate; 
    -排序            只需 use GetApiSort; 
    -启用禁用         只需 use GetApiState; 
    -单字段内容修改    只需 use GetApiRowChange; 
    
#### 钩子
   - _ +  method 方法 + _before 操作前 主要针对 入参验证、数据重构、字段修改等
   - _ +  method 方法 + _after 操作后 主要针对操作后数据返回客户端前的修改，例如数据重构、隐藏字段、删除敏感信息等
    
    0: _list_before       查询列表前
    1: _list_after       查询列表后       
    2: meddleFormRule       UI表单生成前       
    3: beforeFormResponse       UI表单生成后       
    4: _update_before       更新前       
    5: _update_after       更新后       
    6: _create_before       创建前       
    7: _create_after       创建后       
    8: _sort_before       排序前       
    9: _delete_before       删除前       
    10: _delete_after       删除后       
    11: _state_before       状态更新前       
    12: _state_after       状态更新后       


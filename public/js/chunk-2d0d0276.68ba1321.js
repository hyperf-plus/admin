(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-2d0d0276"],{6787:function(e,n,t){"use strict";t.r(n);var r={props:{loading:{default:!1}},data:function(){return{form:{name:void 0,user_agent:void 0}}},methods:{handleFormSubmit:function(e){var n=0<arguments.length&&void 0!==e&&e;this.$emit("submit",this.form,n)},handleFormReset:function(){this.$refs.form.resetFields()}}},o=t("2877"),a=Object(o.a)(r,function(){var n=this,e=n.$createElement,t=n._self._c||e;return t("el-form",{ref:"form",staticStyle:{"margin-bottom":"-18px"},attrs:{inline:!0,model:n.form,size:"mini"}},[t("el-form-item",{attrs:{label:"名称",prop:"name"}},[t("el-input",{attrs:{"prefix-icon":"el-icon-search",placeholder:"可输入应用安装包名称",clearable:!0},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&n._k(e.keyCode,"enter",13,e.key,"Enter")?null:n.handleFormSubmit(!0)}},model:{value:n.form.name,callback:function(e){n.$set(n.form,"name",e)},expression:"form.name"}})],1),t("el-form-item",{attrs:{label:"系统标识",prop:"user_agent"}},[t("el-input",{attrs:{"prefix-icon":"el-icon-search",placeholder:"可输入系统标识",clearable:!0},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&n._k(e.keyCode,"enter",13,e.key,"Enter")?null:n.handleFormSubmit(!0)}},model:{value:n.form.user_agent,callback:function(e){n.$set(n.form,"user_agent",e)},expression:"form.user_agent"}})],1),t("el-form-item",[t("el-button",{attrs:{type:"primary",disabled:n.loading},on:{click:function(e){return n.handleFormSubmit(!0)}}},[t("cs-icon",{attrs:{name:"search"}}),n._v("\n      查询\n    ")],1)],1),t("el-form-item",[t("el-button",{on:{click:n.handleFormReset}},[t("cs-icon",{attrs:{name:"refresh"}}),n._v("\n      重置\n    ")],1)],1)],1)},[],!1,null,null,null);n.default=a.exports}}]);
(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-0315a6c8"],{"1dcf":function(t,e,n){},"47f7":function(t,e,n){"use strict";var a=n("1dcf");n.n(a).a},8704:function(t,e,n){"use strict";n.r(e);n("8e6e"),n("456d"),n("ac4d"),n("8a81"),n("ac6a");var a=n("bd86"),i=n("7830"),r=n("1213");function o(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),n.push.apply(n,a)}return n}var c={name:"cs-storage",mixins:[i.a],components:{PageFooter:function(){return n.e("chunk-2d2102da").then(n.bind(null,"b77f"))}},props:{confirm:{type:Function}},data:function(){return{visible:!1,loading:!0,naviData:[],checkList:[],currentTableData:[],isCheckDirectory:!1,form:{name:"",storage_id:0,order_type:"desc",order_field:"storage_id"},page:{current:1,size:48,total:0}}},watch:{"form.storage_id":{handler:function(t){var e=this;Object(r.g)(t).then(function(t){e.naviData=t.data})}}},methods:{handleStorageDlg:function(){this.visible=!0,this.handleSubmit()},switchDirectory:function(t){this.form.name=null,this.form.storage_id=t||0,this.handleSubmit()},handleOpen:function(t){2===this.currentTableData[t].type&&this.switchDirectory(this.currentTableData[t].storage_id)},handlePaginationChange:function(t){var e=this;this.page=t,this.$nextTick(function(){e.handleSubmit()})},handleSubmit:function(){var e=this;this.checkList=[],this.loading=!0,Object(r.f)(function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?o(n,!0).forEach(function(t){Object(a.a)(e,t,n[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):o(n).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))})}return e}({},this.form,{page_no:this.page.current,page_size:this.page.size})).then(function(t){e.page.total=t.data.total_result,e.currentTableData=0<t.data.total_result?t.data.items:[]}).finally(function(){e.loading=!1})},handleConfirm:function(){var t=[],e=!0,n=!1,a=void 0;try{for(var i,r=this.currentTableData[Symbol.iterator]();!(e=(i=r.next()).done);e=!0){var o=i.value;-1!==this.checkList.indexOf(o.storage_id)&&t.push(o)}}catch(t){n=!0,a=t}finally{try{e||null==r.return||r.return()}finally{if(n)throw a}}this.visible=!1,this.$emit("confirm",t)},handleSearch:function(){this.page.current=1,this.form.storage_id=0,this.handleSubmit()}}},s=(n("47f7"),n("2877")),l=Object(s.a)(c,function(){var n=this,t=n.$createElement,a=n._self._c||t;return a("el-dialog",{attrs:{title:"资源选取",visible:n.visible,"append-to-body":!0,"close-on-click-modal":!1,width:"769px"},on:{"update:visible":function(t){n.visible=t}}},[a("el-form",{staticStyle:{"margin-top":"-25px"},attrs:{model:n.form,size:"small"},nativeOn:{submit:function(t){t.preventDefault()}}},[a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{span:8}},[a("el-form-item",[a("el-button-group",[a("el-button",{on:{click:n.allCheckBox}},[a("cs-icon",{attrs:{name:"check-square-o"}}),n._v("\n              全选\n            ")],1),a("el-button",{on:{click:n.reverseCheckBox}},[a("cs-icon",{attrs:{name:"minus-square-o"}}),n._v("\n              反选\n            ")],1),a("el-button",{on:{click:n.cancelCheckBox}},[a("cs-icon",{attrs:{name:"square-o"}}),n._v("\n              取消\n            ")],1)],1)],1)],1),a("el-col",{attrs:{span:16}},[a("el-form-item",{attrs:{prop:"name"}},[a("el-input",{attrs:{placeholder:"输入资源名称进行搜索",clearable:!0,size:"small"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&n._k(t.keyCode,"enter",13,t.key,"Enter")?null:n.handleSearch()}},model:{value:n.form.name,callback:function(t){n.$set(n.form,"name",t)},expression:"form.name"}},[a("el-button",{attrs:{slot:"append",icon:"el-icon-search"},on:{click:n.handleSearch},slot:"append"})],1)],1)],1)],1)],1),a("el-breadcrumb",{staticClass:"breadcrumb cs-mb",attrs:{"separator-class":"el-icon-arrow-right"}},[a("el-breadcrumb-item",[a("a",{staticStyle:{cursor:"pointer"},on:{click:function(t){return n.switchDirectory(0)}}},[n._v("资源管理")])]),n._l(n.naviData,function(e){return a("el-breadcrumb-item",{key:e.storage_id},[a("a",{staticStyle:{cursor:"pointer"},on:{click:function(t){return n.switchDirectory(e.storage_id)}}},[n._v(n._s(e.name))])])})],2),a("el-checkbox-group",{model:{value:n.checkList,callback:function(t){n.checkList=t},expression:"checkList"}},[a("ul",{directives:[{name:"loading",rawName:"v-loading",value:n.loading,expression:"loading"}],staticClass:"storage-list"},n._l(n.currentTableData,function(t,e){return a("li",{key:e},[a("dl",[a("dt",[a("div",{staticClass:"picture cs-m-5"},[2!==t.type?a("el-checkbox",{staticClass:"check",attrs:{label:t.storage_id}},[n._v(" ")]):n._e(),a("el-image",{attrs:{fit:"fill",src:n._f("getImageThumb")(t),lazy:""},nativeOn:{click:function(t){return n.handleOpen(e)}}})],1),a("el-tooltip",{attrs:{placement:"top",enterable:!1,"open-delay":300}},[a("div",{attrs:{slot:"content"},slot:"content"},[a("span",[n._v("名称："+n._s(t.name))]),a("br"),a("span",[n._v("日期："+n._s(t.create_time))]),a("br"),0===t.type?a("span",[n._v("尺寸："+n._s(t.pixel.width+","+t.pixel.height))]):a("span",[n._v("类型："),a("cs-icon",{attrs:{name:n._f("getFileTypeIocn")(t.type)}})],1)]),a("span",{staticClass:"storage-name cs-ml-5"},[n._v(n._s(t.name))])])],1)])])}),0)]),a("page-footer",{staticStyle:{margin:"0",padding:"20px 0 0 0"},attrs:{current:n.page.current,size:n.page.size,total:n.page.total},on:{change:n.handlePaginationChange}}),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{size:"small"},on:{click:function(t){n.visible=!1}}},[n._v("取消")]),a("el-button",{attrs:{type:"primary",size:"small"},on:{click:n.handleConfirm}},[n._v("确定")])],1)],1)},[],!1,null,"62e97892",null);e.default=l.exports}}]);
(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-00064ab7"],{"5a91":function(e,t,a){"use strict";var n=a("a46f");a.n(n).a},a46f:function(e,t,a){},cdd4:function(e,t,a){"use strict";a.r(t);a("ac6a"),a("7f7f");var n=a("5646"),l=a("7878"),s={created:function(){var t=this;Object(l.c)().then(function(e){t.typeList=e}).then(function(){t._validationAuth()})},props:{loading:{default:!1},tableData:{default:function(){return[]}},unreadData:{default:function(){}},typeData:{default:function(){return[]}}},data:function(){return{tabPane:0,typeList:{},currentTableData:[],multipleSelection:[],form:{type:null,is_read:null},auth:{read:!1,read_all:!1,del:!1,del_all:!1}}},watch:{tableData:{handler:function(e){this.currentTableData=e,this.multipleSelection=[]},immediate:!0},"form.is_read":{handler:function(e){this.form.is_read=e,this.$emit("submit",!0)}},tabPane:{handler:function(e){if(this.typeData.hasOwnProperty(e)){var t=this.typeData[e];this.form.type="total"!==t.value?t.value:null,this.$emit("submit",!0,!0)}}}},filters:{getTabPaneName:function(e,t){return!t.hasOwnProperty(e.value)||t[e.value]<=0?e.name:e.name+"(".concat(t[e.value],")")}},methods:{_validationAuth:function(){this.auth.read=this.$has("/system/message/user/read"),this.auth.read_all=this.$has("/system/message/user/read_all"),this.auth.del=this.$has("/system/message/user/del"),this.auth.del_all=this.$has("/system/message/user/del_all")},_getIdList:function(e){null===e&&(e=this.multipleSelection);var t=[];return Array.isArray(e)?e.forEach(function(e){t.push(e.message_id)}):t.push(this.currentTableData[e].message_id),t},handleSelectionChange:function(e){this.multipleSelection=e},sortChange:function(e){var t=e.column,a=e.prop,n=e.order,l={order_type:void 0,order_field:void 0};t&&n&&(l.order_type="ascending"===n?"asc":"desc",l.order_field=a),this.$emit("sort",l)},setMessageRead:function(e){var t=this.currentTableData[e].type;this.currentTableData[e].is_read=1,this.$emit("minus",t,1)},openMessage:function(e){var t=this.currentTableData[e];if(t.is_read||this.setMessageRead(e),t.url)return Object(n.g)(t.message_id),void this.$open(t.url);this.$router.push({name:"system-message-user-view",params:{message_id:t.message_id}})},handleRead:function(){var e=this,t=this._getIdList(null);0!==t.length?this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then(function(){Object(n.m)(t).then(function(){e.$emit("submit"),e.$message.success("操作成功")})}).catch(function(){}):this.$message.error("请选择要操作的数据")},handleReadAll:function(){var e=this;this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then(function(){Object(n.l)().then(function(){e.$emit("submit"),e.$message.success("操作成功")})}).catch(function(){})},handleDelete:function(){var e=this,t=this._getIdList(null);0!==t.length?this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then(function(){Object(n.d)(t).then(function(){e.$emit("submit",!0),e.$message.success("操作成功")})}).catch(function(){}):this.$message.error("请选择要操作的数据")},handleDeleteAll:function(){var e=this;this.$confirm("确定要执行该操作吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning",closeOnClickModal:!1}).then(function(){Object(n.c)().then(function(){e.$emit("submit",!0),e.$message.success("操作成功")})}).catch(function(){})}}},i=(a("5a91"),a("2877")),r=Object(i.a)(s,function(){var a=this,e=a.$createElement,n=a._self._c||e;return n("div",{staticClass:"cs-p"},[n("el-form",{attrs:{inline:!0,size:"small"}},[n("el-form-item",[n("el-radio-group",{attrs:{disabled:a.loading},model:{value:a.form.is_read,callback:function(e){a.$set(a.form,"is_read",e)},expression:"form.is_read"}},[n("el-radio-button",{attrs:{label:null}},[a._v("全部")]),n("el-radio-button",{attrs:{label:"0"}},[a._v("未读")]),n("el-radio-button",{attrs:{label:"1"}},[a._v("已读")])],1)],1),a.auth.read||a.auth.read_all?n("el-form-item",[n("el-button-group",[a.auth.read?n("el-button",{attrs:{disabled:a.loading},on:{click:a.handleRead}},[a._v("\n          标记已读\n        ")]):a._e(),a.auth.read_all?n("el-button",{attrs:{disabled:a.loading},on:{click:a.handleReadAll}},[a._v("\n          全部已读\n        ")]):a._e()],1)],1):a._e(),a.auth.del||a.auth.del_all?n("el-form-item",[n("el-button-group",[a.auth.del?n("el-button",{attrs:{disabled:a.loading},on:{click:a.handleDelete}},[a._v("\n          批量删除\n        ")]):a._e(),a.auth.del_all?n("el-button",{attrs:{disabled:a.loading},on:{click:a.handleDeleteAll}},[a._v("\n          全部删除\n        ")]):a._e()],1)],1):a._e(),n("el-form-item",[n("el-button-group",[n("el-button",{attrs:{disabled:a.loading},on:{click:function(e){return a.$emit("submit")}}},[a._v("\n          刷新\n        ")])],1)],1)],1),n("el-tabs",{directives:[{name:"loading",rawName:"v-loading",value:a.loading,expression:"loading"}],staticClass:"tab-box",model:{value:a.tabPane,callback:function(e){a.tabPane=e},expression:"tabPane"}},a._l(a.typeData,function(e,t){return n("el-tab-pane",{key:t,attrs:{label:a._f("getTabPaneName")(e,a.unreadData),name:t.toString()}},[t.toString()===a.tabPane?n("el-table",{attrs:{data:a.currentTableData},on:{"selection-change":a.handleSelectionChange,"sort-change":a.sortChange}},[n("el-table-column",{attrs:{type:"selection",width:"35"}}),n("el-table-column",{attrs:{align:"center",width:"20"},scopedSlots:a._u([{key:"default",fn:function(e){return[e.row.is_read?a._e():n("el-badge",{staticClass:"message-badge",attrs:{"is-dot":"",type:"primary"}})]}}],null,!0)}),n("el-table-column",{attrs:{label:"标题",prop:"title"},scopedSlots:a._u([{key:"default",fn:function(t){return[t.row.url?n("el-tooltip",{attrs:{placement:"top",content:"外部链接："+t.row.url}},[n("cs-icon",{attrs:{name:"link"}})],1):a._e(),n("span",{class:"message-title "+(t.row.is_read?"read":""),on:{click:function(e){return a.openMessage(t.$index)}}},[a._v("\n              "+a._s(t.row.title)+"\n            ")]),t.row.is_top?n("el-badge",{attrs:{value:"Top"}}):a._e()]}}],null,!0)}),n("el-table-column",{attrs:{label:"类型",prop:"type",sortable:"custom",width:"200"},scopedSlots:a._u([{key:"default",fn:function(e){return[a._v("\n            "+a._s(a.typeList[e.row.type])+"\n          ")]}}],null,!0)}),n("el-table-column",{attrs:{label:"日期",prop:"create_time",sortable:"custom",align:"center",width:"200"}})],1):a._e()],1)}),1)],1)},[],!1,null,"500ebbf1",null);t.default=r.exports}}]);
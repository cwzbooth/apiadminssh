(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-01b04964"],{"8a34":function(t,e,a){"use strict";var n=a("d0e8"),r=a.n(n);r.a},b562:function(t,e,a){"use strict";a.d(e,"g",(function(){return r})),a.d(e,"h",(function(){return o})),a.d(e,"f",(function(){return i})),a.d(e,"c",(function(){return s})),a.d(e,"b",(function(){return p})),a.d(e,"a",(function(){return l})),a.d(e,"d",(function(){return c})),a.d(e,"e",(function(){return u}));var n=a("66df"),r=function(t){return n["b"].request({url:"App/index",method:"get",params:t})},o=function(){return n["b"].request({url:"App/refreshAppSecret",method:"get"})},i=function(t,e){return n["b"].request({url:"App/getAppInfo",method:"get",params:{id:t,uid:e}})},s=function(t){return n["b"].request({url:"App/del",method:"get",params:{id:t}})},p=function(t,e){return n["b"].request({url:"App/changeStatus",method:"get",params:{status:t,id:e}})},l=function(t){return n["b"].request({url:"App/add",method:"post",data:t})},c=function(t){return n["b"].request({url:"App/edit",method:"post",data:t})},u=function(t){return n["b"].request({url:"App/getAppId",method:"get",params:{uid:t}})}},d0e8:function(t,e,a){},e1af:function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("Row",[a("Col",{attrs:{span:"24"}},[a("Card",{staticClass:"margin-bottom-10"},[a("Form",{attrs:{inline:""}},[a("FormItem",{staticClass:"margin-bottom-0"},[a("Select",{staticStyle:{width:"120px"},attrs:{clearable:"",placeholder:"请选择状态"},model:{value:t.searchConf.status,callback:function(e){t.$set(t.searchConf,"status",e)},expression:"searchConf.status"}},[a("Option",{attrs:{value:1}},[t._v("启用")]),a("Option",{attrs:{value:0}},[t._v("禁用")])],1)],1),a("FormItem",{staticClass:"margin-bottom-0"},[a("Select",{staticStyle:{width:"150px"},attrs:{clearable:"",placeholder:"请选择类别"},model:{value:t.searchConf.type,callback:function(e){t.$set(t.searchConf,"type",e)},expression:"searchConf.type"}},[a("Option",{attrs:{value:1}},[t._v("AppId")]),a("Option",{attrs:{value:2}},[t._v("应用名称")]),a("Option",{attrs:{value:3}},[t._v("所属应用组标识")]),a("Option",{attrs:{value:4}},[t._v("所属用户UID")])],1)],1),a("FormItem",{staticClass:"margin-bottom-0"},[a("Input",{attrs:{placeholder:""},model:{value:t.searchConf.keywords,callback:function(e){t.$set(t.searchConf,"keywords",e)},expression:"searchConf.keywords"}})],1),a("FormItem",{staticClass:"margin-bottom-0"},[a("Button",{attrs:{type:"primary"},on:{click:t.search}},[t._v(t._s(t.$t("find_button"))+"/"+t._s(t.$t("refresh_button")))])],1)],1)],1)],1)],1),a("Row",[a("Col",{attrs:{span:"24"}},[a("Card",[a("div",{staticClass:"margin-bottom-15"},[a("Button",{directives:[{name:"has",rawName:"v-has",value:"App/add",expression:"'App/add'"}],attrs:{type:"primary",icon:"md-add"},on:{click:t.alertAdd}},[t._v(t._s(t.$t("add_button")))])],1),a("div",[a("Table",{attrs:{loading:t.listLoading,columns:t.columnsList,data:t.tableData,border:"","disabled-hover":""}})],1),a("div",{staticClass:"margin-top-15",staticStyle:{"text-align":"center"}},[a("Page",{attrs:{total:t.tableShow.listCount,current:t.tableShow.currentPage,"page-size":t.tableShow.pageSize,"show-elevator":"","show-sizer":"","show-total":""},on:{"on-change":t.changePage,"on-page-size-change":t.changeSize}})],1)])],1)],1),a("Modal",{attrs:{width:"668",styles:{top:"30px"}},on:{"on-visible-change":t.doCancel},model:{value:t.modalSetting.show,callback:function(e){t.$set(t.modalSetting,"show",e)},expression:"modalSetting.show"}},[a("p",{staticStyle:{color:"#2d8cf0"},attrs:{slot:"header"},slot:"header"},[a("Icon",{attrs:{type:"md-alert"}}),a("span",[t._v(t._s(t.formItem.id?"编辑":"新增")+"应用")])],1),a("Form",{ref:"myForm",attrs:{rules:t.ruleValidate,model:t.formItem,"label-width":80}},[a("FormItem",{attrs:{label:"应用名称",prop:"app_name"}},[a("Input",{attrs:{placeholder:"请输入应用名称"},model:{value:t.formItem.app_name,callback:function(e){t.$set(t.formItem,"app_name",e)},expression:"formItem.app_name"}})],1),a("FormItem",{attrs:{label:"AppId",prop:"app_id"}},[a("Input",{staticStyle:{width:"300px"},attrs:{disabled:"",placeholder:"请输入AppId"},model:{value:t.formItem.app_id,callback:function(e){t.$set(t.formItem,"app_id",e)},expression:"formItem.app_id"}}),a("Tag",{staticClass:"margin-left-5",attrs:{color:"error"}},[t._v("系统自动生成，不允许修改")])],1),a("FormItem",{attrs:{label:"AppSecret",prop:"app_secret"}},[a("Input",{staticStyle:{width:"300px"},attrs:{disabled:"",placeholder:"请输入AppSecret"},model:{value:t.formItem.app_secret,callback:function(e){t.$set(t.formItem,"app_secret",e)},expression:"formItem.app_secret"}},[a("Button",{attrs:{slot:"append",icon:"md-refresh"},on:{click:t.refreshAppSecret},slot:"append"})],1)],1),a("FormItem",{attrs:{label:"应用组",prop:"app_group"}},[a("Select",{staticStyle:{width:"200px"},model:{value:t.formItem.app_group,callback:function(e){t.$set(t.formItem,"app_group",e)},expression:"formItem.app_group"}},t._l(t.appGroup,(function(e,n){return a("Option",{key:e.hash,attrs:{value:e.hash,kk:n}},[t._v(" "+t._s(e.name))])})),1)],1),a("FormItem",{attrs:{label:"应用描述",prop:"app_info"}},[a("Input",{attrs:{type:"textarea"},model:{value:t.formItem.app_info,callback:function(e){t.$set(t.formItem,"app_info",e)},expression:"formItem.app_info"}})],1),a("FormItem",{attrs:{label:"接口访问",prop:"app_api"}},[a("div",{staticClass:"api-box"},t._l(t.groupList,(function(e,n){return a("div",{key:n,staticClass:"api-group"},[a("div",{staticStyle:{"border-bottom":"1px solid #e9e9e9","padding-bottom":"6px","margin-bottom":"6px"}},[a("Checkbox",{attrs:{indeterminate:t.checkAllIndeterminate[n],value:t.checkAllStatus[n]},nativeOn:{click:function(e){return e.preventDefault(),t.handleCheckAll(n)}}},[t._v(" "+t._s(t.groupInfo[n])+"\n              ")])],1),a("CheckboxGroup",{on:{"on-change":function(e){return t.checkAllGroupChange(n)}},model:{value:t.formItem.app_api[n],callback:function(e){t.$set(t.formItem.app_api,n,e)},expression:"formItem.app_api[groupId]"}},t._l(e,(function(e,n){return a("Checkbox",{key:n,attrs:{label:e.hash}},[t._v("\n                "+t._s(e.info)+"\n              ")])})),1)],1)})),0)])],1),a("div",{attrs:{slot:"footer"},slot:"footer"},[a("Button",{staticClass:"margin-right-10",attrs:{type:"text"},on:{click:t.cancel}},[t._v("取消")]),a("Button",{attrs:{type:"primary",loading:t.modalSetting.loading},on:{click:t.submit}},[t._v("确定")])],1)],1)],1)},r=[],o=(a("ac6a"),a("b562")),i=a("e412"),s=function(t,e,a,n){if(t.buttonShow.edit)return e("Button",{props:{type:"primary"},style:{margin:"0 5px"},on:{click:function(){Object(i["e"])(a.uid).then((function(e){t.appGroup=e.data.data.list,t.formItem.id=a.id,t.formItem.uid=a.uid,t.formItem.app_name=a.app_name,t.formItem.app_info=a.app_info,t.formItem.app_id=a.app_id,t.formItem.app_secret=a.app_secret,t.formItem.app_group=a.app_group,Object(o["f"])(a.id,a.uid).then((function(e){var a=e.data;for(var n in t.groupInfo=a.data.groupInfo,t.groupList=a.data.apiList,t.groupInfo)if(null===a.data.app_detail||"undefined"===typeof a.data.app_detail[n])t.$set(t.checkAllStatus,n,!1),t.$set(t.checkAllIndeterminate,n,!1),t.$set(t.formItem.app_api,n,[]);else{var r=a.data.app_detail[n].length;0===r?(t.$set(t.checkAllStatus,n,!1),t.$set(t.checkAllIndeterminate,n,!1),t.$set(t.formItem.app_api,n,[])):t.groupList[n].length===r?(t.$set(t.checkAllStatus,n,!0),t.$set(t.checkAllIndeterminate,n,!1),t.$set(t.formItem.app_api,n,a.data.app_detail[n])):(t.$set(t.checkAllStatus,n,!1),t.$set(t.checkAllIndeterminate,n,!0),t.$set(t.formItem.app_api,n,a.data.app_detail[n]))}})),t.modalSetting.show=!0,t.modalSetting.index=n}))}}},t.$t("edit_button"))},p=function(t,e,a,n){if(t.buttonShow.del)return e("Poptip",{props:{confirm:!0,title:"您确定要删除这条数据吗? ",transfer:!0},on:{"on-ok":function(){Object(o["c"])(a.id).then((function(e){t.tableData.splice(n,1),t.$Message.success(e.data.msg)})),a.loading=!1}}},[e("Button",{style:{margin:"0 5px"},props:{type:"error",placement:"top",loading:a.isDeleting}},t.$t("delete_button"))])},l={name:"interface_list",data:function(){var t=this;return{appGroup:[],columnsList:[{title:"序号",type:"index",width:65,align:"center"},{title:"应用名称",align:"center",key:"app_name",minWidth:130},{title:"AppId",align:"center",key:"app_id",width:120},{title:"AppSecret",align:"center",key:"app_secret",minWidth:285},{title:"请求量",align:"center",key:"hits",width:120,sortable:!0},{title:"所属用户",align:"center",width:120,sortable:!0,render:function(t,e){var a=e.row.username+"   ("+e.row.uid+")";return t("span",a)}},{title:"所属应用组",align:"center",width:150,sortable:!0,render:function(t,e){var a=e.row.app_group_name+"   ("+e.row.app_group+")";return t("span",a)}},{title:"应用说明",align:"center",key:"app_info",width:160},{title:"应用状态",align:"center",width:100,fixed:"right",render:function(e,a){var n=t;return e("i-switch",{attrs:{size:"large"},props:{"true-value":1,"false-value":0,value:a.row.app_status,disabled:!n.buttonShow.changeStatus},on:{"on-change":function(t){Object(o["b"])(t,a.row.id).then((function(t){n.$Message.success(t.data.msg),n.getList()}))}}},[e("span",{slot:"open"},n.$t("open_choose")),e("span",{slot:"close"},n.$t("close_choose"))])}},{title:"操作",align:"center",width:200,fixed:"right",render:function(e,a){return e("div",[s(t,e,a.row,a.index),p(t,e,a.row,a.index)])}}],tableData:[],groupInfo:{},groupList:{},tableShow:{currentPage:1,pageSize:10,listCount:0},searchConf:{type:"",keywords:"",status:""},modalSetting:{show:!1,loading:!1,index:0},formItem:{app_name:"",app_id:"",app_secret:"",app_info:"",app_api:{},app_group:"default",id:0,uid:0},ruleValidate:{app_name:[{required:!0,message:"应用名称不能为空",trigger:"blur"}]},checkAllStatus:{},checkAllIndeterminate:{},buttonShow:{to:!0,edit:!0,del:!0,changeStatus:!0},listLoading:!1}},created:function(){var t=this;t.getList(),t.hasRule("App/edit").then((function(e){t.buttonShow.edit=e})),t.hasRule("App/del").then((function(e){t.buttonShow.del=e})),t.hasRule("App/changeStatus").then((function(e){t.buttonShow.changeStatus=e}))},methods:{alertAdd:function(){var t=this;Object(i["e"])().then((function(e){t.appGroup=e.data.data.list})),Object(o["f"])().then((function(e){var a=e.data;for(var n in t.formItem.app_id=a.data.app_id,t.formItem.app_secret=a.data.app_secret,t.groupInfo=a.data.groupInfo,t.groupList=a.data.apiList,t.groupInfo)t.$set(t.checkAllStatus,n,!1),t.$set(t.checkAllIndeterminate,n,!1),t.$set(t.formItem.app_api,n,[])})),t.modalSetting.show=!0},submit:function(){var t=this;t.$refs["myForm"].validate((function(e){e&&(t.modalSetting.loading=!0,0===t.formItem.id?Object(o["a"])(t.formItem).then((function(e){t.$Message.success(e.data.msg),t.getList(),t.cancel()})).catch((function(){t.modalSetting.loading=!1})):Object(o["d"])(t.formItem).then((function(e){t.$Message.success(e.data.msg),t.getList(),t.cancel()})).catch((function(){t.modalSetting.loading=!1})))}))},cancel:function(){this.formItem.id=0,this.$refs["myForm"].resetFields(),this.modalSetting.show=!1,this.modalSetting.loading=!1,this.modalSetting.index=0},changePage:function(t){this.tableShow.currentPage=t,this.getList()},changeSize:function(t){this.tableShow.pageSize=t,this.getList()},handleCheckAll:function(t){if(this.checkAllStatus[t]?this.checkAllStatus[t]=!1:this.checkAllStatus[t]=!this.checkAllStatus[t],this.checkAllIndeterminate[t]=!1,this.checkAllStatus[t]){var e=this;this.groupList[t].forEach((function(a){e.formItem.app_api[t].push(a.hash)}))}else this.formItem.app_api[t]=[]},checkAllGroupChange:function(t){this.formItem.app_api[t].length===this.groupList[t].length?(this.checkAllIndeterminate[t]=!1,this.checkAllStatus[t]=!0):this.formItem.app_api[t].length>0?(this.checkAllIndeterminate[t]=!0,this.checkAllStatus[t]=!1):(this.checkAllIndeterminate[t]=!1,this.checkAllStatus[t]=!1)},search:function(){this.tableShow.currentPage=1,this.getList()},refreshAppSecret:function(){var t=this;Object(o["h"])().then((function(e){t.formItem.app_secret=e.data.data.app_secret}))},getList:function(){var t=this;t.listLoading=!0,Object(o["g"])({page:t.tableShow.currentPage,size:t.tableShow.pageSize,type:t.searchConf.type,keywords:t.searchConf.keywords,status:t.searchConf.status}).then((function(e){t.tableData=e.data.data.list,t.tableShow.listCount=e.data.data.count,t.listLoading=!1}))},doCancel:function(t){t||(this.formItem.id=0,this.$refs["myForm"].resetFields(),this.modalSetting.loading=!1,this.modalSetting.index=0)}}},c=l,u=(a("8a34"),a("2877")),d=Object(u["a"])(c,n,r,!1,null,"44eee5b2",null);e["default"]=d.exports},e412:function(t,e,a){"use strict";a.d(e,"f",(function(){return r})),a.d(e,"c",(function(){return o})),a.d(e,"b",(function(){return i})),a.d(e,"a",(function(){return s})),a.d(e,"d",(function(){return p})),a.d(e,"e",(function(){return l})),a.d(e,"g",(function(){return c}));var n=a("66df"),r=function(t){return n["b"].request({url:"AppGroup/index",method:"get",params:t})},o=function(t){return n["b"].request({url:"AppGroup/del",method:"get",params:{hash:t}})},i=function(t,e){return n["b"].request({url:"AppGroup/changeStatus",method:"get",params:{status:t,id:e}})},s=function(t){return n["b"].request({url:"AppGroup/add",method:"post",data:t})},p=function(t){return n["b"].request({url:"AppGroup/edit",method:"post",data:t})},l=function(t){return n["b"].request({url:"AppGroup/getAll",method:"get",params:{uid:t}})},c=function(t){return n["b"].request({url:"AppGroup/getWeb",method:"get",params:{uid:t}})}}}]);
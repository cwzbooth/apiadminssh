(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6201930c"],{"2eb4":function(t,e,n){"use strict";n.r(e);var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("Row",[n("Col",{attrs:{span:"24"}},[n("Card",{staticClass:"margin-bottom-10"},[n("Form",{attrs:{inline:""}},[n("FormItem",{staticClass:"margin-bottom-0"},[n("Select",{staticStyle:{width:"120px"},attrs:{clearable:"",placeholder:"请选择状态"},model:{value:t.searchConf.status,callback:function(e){t.$set(t.searchConf,"status",e)},expression:"searchConf.status"}},[n("Option",{attrs:{value:1}},[t._v("启用")]),n("Option",{attrs:{value:0}},[t._v("禁用")])],1)],1),n("FormItem",{staticClass:"margin-bottom-0"},[n("Select",{staticStyle:{width:"120px"},attrs:{clearable:"",placeholder:"请选择类别"},model:{value:t.searchConf.type,callback:function(e){t.$set(t.searchConf,"type",e)},expression:"searchConf.type"}},[n("Option",{attrs:{value:1}},[t._v("用户账号")]),n("Option",{attrs:{value:2}},[t._v("用户昵称")])],1)],1),n("FormItem",{staticClass:"margin-bottom-0"},[n("Input",{attrs:{placeholder:""},model:{value:t.searchConf.keywords,callback:function(e){t.$set(t.searchConf,"keywords",e)},expression:"searchConf.keywords"}})],1),n("FormItem",{staticClass:"margin-bottom-0"},[n("Button",{attrs:{type:"primary"},on:{click:t.search}},[t._v(t._s(t.$t("find_button"))+"/"+t._s(t.$t("refresh_button")))])],1)],1)],1)],1)],1),n("Row",[n("Col",{attrs:{span:"24"}},[n("Card",[n("div",{staticClass:"margin-bottom-15"},[n("Button",{directives:[{name:"has",rawName:"v-has",value:"User/add",expression:"'User/add'"}],attrs:{type:"primary",icon:"md-add"},on:{click:t.alertAdd}},[t._v(t._s(t.$t("add_button")))])],1),n("div",[n("Table",{attrs:{loading:t.listLoading,columns:t.columnsList,data:t.tableData,border:"","disabled-hover":""}})],1),n("div",{staticClass:"margin-top-15",staticStyle:{"text-align":"center"}},[n("Page",{attrs:{total:t.tableShow.listCount,current:t.tableShow.currentPage,"page-size":t.tableShow.pageSize,"show-elevator":"","show-sizer":"","show-total":""},on:{"on-change":t.changePage,"on-page-size-change":t.changeSize}})],1)])],1)],1),n("Modal",{attrs:{width:"668",styles:{top:"30px"}},on:{"on-visible-change":t.doCancel},model:{value:t.modalSetting.show,callback:function(e){t.$set(t.modalSetting,"show",e)},expression:"modalSetting.show"}},[n("p",{staticStyle:{color:"#2d8cf0"},attrs:{slot:"header"},slot:"header"},[n("Icon",{attrs:{type:"md-alert"}}),n("span",[t._v(t._s(t.formItem.id?"编辑":"新增")+"用户")])],1),n("Form",{ref:"myForm",attrs:{rules:t.ruleValidate,model:t.formItem,"label-width":80}},[n("FormItem",{attrs:{label:"用户账号",prop:"username"}},[n("Input",{attrs:{placeholder:"请输入账号"},model:{value:t.formItem.username,callback:function(e){t.$set(t.formItem,"username",e)},expression:"formItem.username"}})],1),n("FormItem",{attrs:{label:"用户昵称",prop:"nickname"}},[n("Input",{attrs:{placeholder:"请输入昵称"},model:{value:t.formItem.nickname,callback:function(e){t.$set(t.formItem,"nickname",e)},expression:"formItem.nickname"}})],1),n("FormItem",{attrs:{label:"用户密码",prop:"password"}},[n("Input",{attrs:{type:"password",placeholder:"用户密码"},model:{value:t.formItem.password,callback:function(e){t.$set(t.formItem,"password",e)},expression:"formItem.password"}})],1),n("FormItem",{attrs:{label:"权限组",prop:"group_id"}},[n("CheckboxGroup",{model:{value:t.formItem.group_id,callback:function(e){t.$set(t.formItem,"group_id",e)},expression:"formItem.group_id"}},t._l(t.groupList,(function(e){return n("Checkbox",{key:e.id,attrs:{label:e.id+""}},[t._v(t._s(e.name))])})),1)],1)],1),n("div",{attrs:{slot:"footer"},slot:"footer"},[n("Button",{staticClass:"margin-right-10",attrs:{type:"text"},on:{click:t.cancel}},[t._v("取消")]),n("Button",{attrs:{type:"primary",loading:t.modalSetting.loading},on:{click:t.submit}},[t._v("确定")])],1)],1)],1)},r=[],o=n("c24f"),s=n("3786"),i=function(t,e,n,a){if(t.buttonShow.edit)return e("Button",{props:{type:"primary"},style:{margin:"0 5px"},on:{click:function(){t.formItem.id=n.id,t.formItem.username=n.username,t.formItem.nickname=n.nickname,t.formItem.password="ApiAdmin",Object(s["g"])().then((function(e){t.groupList=e.data.data.list})),t.formItem.group_id=n.group_id,t.modalSetting.show=!0,t.modalSetting.index=a}}},t.$t("edit_button"))},l=function(t,e,n,a){if(t.buttonShow.del)return e("Poptip",{props:{confirm:!0,title:"您确定要删除这条数据吗? ",transfer:!0},on:{"on-ok":function(){Object(o["c"])(n.id).then((function(e){t.tableData.splice(a,1),t.$Message.success(e.data.msg)})),n.loading=!1}}},[e("Button",{style:{margin:"0 5px",display:0!==n.leavel?"":"none"},props:{type:"error",placement:"top",loading:n.isDeleting}},t.$t("delete_button"))])},u={name:"system_user",data:function(){var t=this;return{columnsList:[{title:"序号",type:"index",width:65,align:"center"},{title:"用户账号",align:"center",key:"username",minWidth:120},{title:"用户昵称",align:"center",key:"nickname",width:160},{title:"用户级别",align:"center",key:"leavel",width:160},{title:"登录次数",align:"center",render:function(t,e){return t("span",null===e.row.userData?"":e.row.userData.login_times)},width:100},{title:"最后登录时间",align:"center",render:function(t,e){return t("span",null===e.row.userData?"":e.row.userData.last_login_time)},width:170},{title:"最后登录IP",align:"center",render:function(t,e){return t("span",null===e.row.userData?"":e.row.userData.last_login_ip)},width:160},{title:"状态",align:"center",width:100,render:function(e,n){var a=t;return e("i-switch",{attrs:{size:"large"},props:{"true-value":1,"false-value":0,value:n.row.status,disabled:!a.buttonShow.changeStatus},on:{"on-change":function(t){Object(o["b"])(t,n.row.id).then((function(t){a.$Message.success(t.data.msg),a.getList()}))}}},[e("span",{slot:"open"},a.$t("open_choose")),e("span",{slot:"close"},a.$t("close_choose"))])}},{title:"操作",align:"center",width:200,render:function(e,n){return e("div",[i(t,e,n.row,n.index),l(t,e,n.row,n.index)])}}],tableData:[],groupList:[],tableShow:{currentPage:1,pageSize:10,listCount:0},searchConf:{type:"",keywords:"",status:""},modalSetting:{show:!1,loading:!1,index:0},formItem:{username:"",nickname:"",password:"",group_id:[],id:0},ruleValidate:{username:[{required:!0,message:"用户账号不能为空",trigger:"blur"}],nickname:[{required:!0,message:"用户昵称不能为空",trigger:"blur"}],password:[{required:!0,message:"用户密码不能为空",trigger:"blur"}]},buttonShow:{edit:!0,del:!0,changeStatus:!0},listLoading:!1}},created:function(){var t=this;t.getList(),t.hasRule("User/edit").then((function(e){t.buttonShow.edit=e})),t.hasRule("User/del").then((function(e){t.buttonShow.del=e})),t.hasRule("User/changeStatus").then((function(e){t.buttonShow.changeStatus=e}))},methods:{alertAdd:function(){var t=this;Object(s["g"])().then((function(e){t.groupList=e.data.data.list})),this.modalSetting.show=!0},submit:function(){var t=this;this.$refs["myForm"].validate((function(e){e&&(t.modalSetting.loading=!0,0===t.formItem.id?Object(o["a"])(t.formItem).then((function(e){t.$Message.success(e.data.msg),t.getList(),t.cancel()})).catch((function(){t.modalSetting.loading=!1})):Object(o["d"])(t.formItem).then((function(e){t.$Message.success(e.data.msg),t.getList(),t.cancel()})).catch((function(){t.modalSetting.loading=!1})))}))},cancel:function(){this.modalSetting.show=!1},doCancel:function(t){t||(this.formItem.id=0,this.$refs["myForm"].resetFields(),this.modalSetting.loading=!1,this.modalSetting.index=0)},changePage:function(t){this.tableShow.currentPage=t,this.getList()},changeSize:function(t){this.tableShow.pageSize=t,this.getList()},search:function(){this.tableShow.currentPage=1,this.getList()},getList:function(){var t=this,e={page:t.tableShow.currentPage,size:t.tableShow.pageSize,type:t.searchConf.type,keywords:t.searchConf.keywords,status:t.searchConf.status};t.listLoading=!0,Object(o["e"])(e).then((function(e){t.tableData=e.data.data.list,t.tableShow.listCount=e.data.data.count,t.listLoading=!1}))}}},c=u,d=(n("6b0c"),n("2877")),m=Object(d["a"])(c,a,r,!1,null,"0370838e",null);e["default"]=m.exports},3786:function(t,e,n){"use strict";n.d(e,"g",(function(){return r})),n.d(e,"h",(function(){return o})),n.d(e,"f",(function(){return s})),n.d(e,"i",(function(){return i})),n.d(e,"b",(function(){return l})),n.d(e,"a",(function(){return u})),n.d(e,"e",(function(){return c})),n.d(e,"c",(function(){return d})),n.d(e,"d",(function(){return m}));var a=n("66df"),r=function(){return a["b"].request({url:"Auth/getGroups",method:"get"})},o=function(t){return a["b"].request({url:"Auth/index",method:"get",params:t})},s=function(t){return a["b"].request({url:"Auth/editRule",method:"post",data:t})},i=function(t){return a["b"].request({url:"Auth/getRuleList",method:"get",params:t})},l=function(t,e){return a["b"].request({url:"Auth/changeStatus",method:"get",params:{status:t,id:e}})},u=function(t){return a["b"].request({url:"Auth/add",method:"post",data:t})},c=function(t){return a["b"].request({url:"Auth/edit",method:"post",data:t})},d=function(t){return a["b"].request({url:"Auth/del",method:"get",params:{id:t}})},m=function(t){return a["b"].request({url:"Auth/delMember",method:"get",params:t})}},"6b0c":function(t,e,n){"use strict";var a=n("b950"),r=n.n(a);r.a},b950:function(t,e,n){}}]);
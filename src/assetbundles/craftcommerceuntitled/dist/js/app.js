(window.webpackJsonp=window.webpackJsonp||[]).push([[0],{ZpdD:function(e,t,s){"use strict";s.r(t);var i=s("Kw5r"),a=s("L2JU");const n={"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"},r=(e="",t={})=>{const s=new URLSearchParams(t).toString();return`${Craft.getSiteUrl()+Craft.CommerceUntitled.pluginHandle}/api/${Craft.CommerceUntitled.apiVersion}/${e+(s.length>0?"?"+s:"")}`};var l=async(e={})=>await fetch(r("variant-configurations",e)),o=async(e={})=>await fetch(r("variant-configuration-types/fields",e)),c=async(e={})=>await fetch(r("variant-configurations"),{method:"POST",headers:n,body:JSON.stringify(e)}),d=async(e,t={})=>await fetch(r(`variant-configurations/${e}/variants/generate`),{method:"POST",headers:n,body:JSON.stringify(t)}),u=async(e,t={})=>await fetch(r(`products/${e}/types`),{method:"POST",headers:n,body:JSON.stringify(t)});const p=["price","stock","sku","minQty","maxQty"],v=["weight","length","width","height"],h=["all","skip","field"];var f=s("UGp+");const _={header:{title:"Variant Configurator",subtitle:"Select or create a variant configuration"},footer:{priBtnText:"Next Step",secBtnText:"Previous"},rules:Object(f.e)({})},m={component:"IndexStep",header:_.header,footer:{priBtnText:"Create New",secBtnText:"Cancel"},rules:_.rules},g={component:"NameStep",header:{..._.header,subtitle:"Step 1. Set the configuration name"},footer:_.footer,rules:Object(f.e)().shape({title:Object(f.f)().required("Please enter a configuration name")})},S={component:"FieldsStep",header:{..._.header,subtitle:"Step 2. Select the fields to generate variants from"},footer:_.footer,rules:Object(f.e)().shape({fields:Object(f.b)().of(Object(f.f)()).min(1,"Please select at least one field")})},y={component:"ValuesStep",header:{..._.header,subtitle:"Step 3. Select the values to generate variants from"},footer:_.footer,rules:Object(f.e)().shape({values:Object(f.b)().of(Object(f.f)()).min(1,"Please select at least one value")})},T=e=>{const t={},s=D.getters.settingsByType(e);if(null==s.field)return Object(f.e)(t);const i=D.getters.selectedOptionValuesByFieldHandle[s.field];return null==i||i.forEach(s=>{switch(e){case"price":case"stock":case"weight":case"height":case"length":case"width":case"minQty":case"maxQty":default:t[s]=Object(f.d)().typeError("Please enter a number").required("Please enter a value").min(0,"Please enter a value greater than 0");break;case"sku":t[s]=Object(f.f)().typeError("Please enter a value").required("Please enter a value").nullable()}}),Object(f.e)(t)},b=e=>{switch(e){case"price":case"stock":case"weight":case"height":case"length":case"width":case"minQty":case"maxQty":default:return Object(f.e)({value:Object(f.d)().typeError("Please enter a number").required("Please enter a value").min(0,"Please enter a value greater than 0")});case"sku":return Object(f.e)({value:Object(f.f)().typeError("Please enter a number").required("Please enter a value").min(0,"Please enter a value greater than 0").nullable()})}},C={component:"SettingsStep",header:{..._.header,subtitle:"Step 4. Set the configuration settings"},footer:{..._.footer,priBtnText:"Save Configuration"},rules:Object(f.e)().shape({settings:Object(f.c)(()=>(()=>{const e={};return D.getters.allowedTypes.forEach(t=>{e[t]=Object(f.e)({method:Object(f.f)().nullable().required("Please select an option").oneOf(h,"Please select an option"),field:Object(f.f)().when("method",{is:"field",then:Object(f.f)().nullable().required("Please select a field")}).nullable(),values:Object(f.e)().when("method",{is:"field",then:Object(f.c)(()=>T(t))}).when("method",{is:"all",then:Object(f.c)(()=>b(t))}).nullable()})}),Object(f.e)(e)})())})},I={component:"GenerateStep",header:{..._.header,subtitle:"Step 5. Generate your variants"},footer:{..._.footer,priBtnText:"Generate Variants"},rules:_.rules};var O={indexStep:m,nameStep:g,fieldsStep:S,valuesStep:y,settingsStep:C,generateStep:I};function E(){this.listeners={},this.oneTimeListeners={}}E.prototype.on=function(e,t,s=null){return A(e,t,s,this.listeners)},E.prototype.once=function(e,t,s=null){return A(e,t,s,this.oneTimeListeners)},E.prototype.emit=async function(e){let t;return t=N(this.listeners[e]),!1===t||(t=N(this.oneTimeListeners[e],!0)),t},E.prototype.off=function(e,t){w(this.listeners[e],t),w(this.oneTimeListeners[e],t)};const A=(e,t,s,i)=>{null===s&&"function"==typeof t&&(s=t,t=null),Array.isArray(i[e])||(i[e]=[]),s.bind(t),i[e].push(s)},N=async(e=[],t=!1)=>{let s=null;for(var i=0;i<e.length;i++)if("function"==typeof e[i]&&(s=await e[i](),t&&e.splice(i,1),!1===s))return s;return s},w=(e=[],t=(()=>{}))=>{for(var s=0;s<e.length;s++)e[s]===t&&e.splice(s,1)};var F=new E;i.a.use(a.a);const V={method:null,field:null,values:{}},x=()=>{const e={};return p.forEach(t=>{e[t]={...V}}),e},j=()=>({id:null,title:"",fields:[],values:[],settings:x()}),R=(e=[])=>{Array.isArray(e)||(e=[e]),e.forEach(e=>{let t=[];for(const[s,i]of Object.entries(e.values))t=t.concat(e.values[s]);e.values=t})};var D=new a.a.Store({state:{ui:{isLoading:!1,isSubmitting:!1,isCompleted:!1},step:0,totalSteps:5,formErrors:{},productId:null,productTypeId:null,typeId:null,variantConfiguration:j(),variantConfigurations:[],variantConfigurationTypeFields:[],hasDimensions:null},mutations:{SET_PRODUCT_ID(e,t){e.productId=t},SET_PRODUCT_TYPE_ID(e,t){e.productTypeId=t},SET_TYPE_ID(e,t){e.typeId=t},SET_VARIANT_CONFIGURATIONS(e,t){e.variantConfigurations=t},SET_HAS_DIMENSIONS(e,t){e.hasDimensions=t},UPDATE_VARIANT_CONFIGURATIONS(e,t){e.variantConfigurations.forEach((s,i)=>{s.id===t.id&&(e.variantConfigurations[i]={...s,...t})})},SET_VARIANT_CONFIGURATION_TYPE_FIELDS(e,t){e.variantConfigurationTypeFields=t},SET_IS_LOADING(e,t=!0){e.ui.isLoading=t},SET_IS_SUBMITTING(e,t=!0){e.ui.isSubmitting=t},SET_IS_COMPLETED(e,t=!0){e.ui.isCompleted=t},SET_VARIANT_CONFIGURATION(e,t){e.variantConfiguration={...t}},UPDATE_VARIANT_CONFIGURATION(e,t){e.variantConfiguration={...e.variantConfiguration,...t}},SET_VARIANT_CONFIGURATION_TITLE(e,t){e.variantConfiguration.title=t},SET_VARIANT_CONFIGURATION_FIELDS(e,t){e.variantConfiguration.fields=t},SET_VARIANT_CONFIGURATION_VALUES(e,t){e.variantConfiguration.values=t},SET_VARIANT_CONFIGURATION_SETTINGS(e,t){e.variantConfiguration.settings=t},SET_VARIANT_CONFIGURATION_SETTINGS_TYPE(e,{type:t,data:s}){e.variantConfiguration.settings[t]||(e.variantConfiguration.settings[t]={}),e.variantConfiguration.settings={...e.variantConfiguration.settings,[t]:{...e.variantConfiguration.settings[t],...V,...s}}},SET_STEP(e,t){e.step=t},SET_FORM_ERRORS(e,t){e.formErrors=t}},actions:{async resetForm({commit:e,dispatch:t}){t("createNewVariantConfiguration"),e("SET_IS_COMPLETED",!1),e("SET_STEP",0)},async saveVariantConfiguration({commit:e,state:t,getters:s,dispatch:i}){e("SET_IS_SUBMITTING",!0);const a={};Object.keys(s.fieldValuesByHandle).forEach(e=>{a[e]=s.selectedOptionValuesByFieldHandle[e]||[]});const n=await c({...t.variantConfiguration,values:a,productId:t.productId,typeId:t.typeId}),{data:r}=await n.json();R(r),e("SET_VARIANT_CONFIGURATION",r),e("UPDATE_VARIANT_CONFIGURATIONS",r),e("SET_IS_SUBMITTING",!1)},async generateVariants({commit:e},t){e("SET_IS_SUBMITTING",!0);await d(t);e("SET_IS_SUBMITTING",!1)},async setCurrentVariantConfigurationById({commit:e,state:t},s){const i=t.variantConfigurations.find(e=>e.id===s);if(null==i)throw new Error("Variant Configuration not found");e("SET_VARIANT_CONFIGURATION",i),e("SET_STEP",1)},async fetchVariantConfigurations({commit:e},t){const s=await l(t),{data:i}=await s.json();R(i),e("SET_VARIANT_CONFIGURATIONS",i)},async fetchVariantConfigurationTypeFields({commit:e},t){const s=await o(t),{data:i}=await s.json();e("SET_VARIANT_CONFIGURATION_TYPE_FIELDS",i)},async toggleAllSelectedFields({commit:e,getters:t,state:s}){if(t.isAllFieldsSelected)return e("SET_VARIANT_CONFIGURATION_FIELDS",[]);const i=[];s.variantConfigurationTypeFields.forEach(e=>{i.push(e.handle)}),e("SET_VARIANT_CONFIGURATION_FIELDS",i)},async toggleAllSelectedValues({commit:e,getters:t,state:s},i){const a=t.fieldValuesByHandle[i];if(!a.length)return;let n=[];const r=s.variantConfiguration.values;n=t.isAllValuesSelected(i)?r.filter(e=>-1===a.indexOf(e)):[...r,...a],e("SET_VARIANT_CONFIGURATION_VALUES",n)},async nextStep({commit:e,getters:t,state:s,dispatch:i}){const{rules:a}=t.currentStepState;if(!await i("validate",{schema:a,values:s.variantConfiguration}))return;!1!==await F.emit("form-submission")&&(s.step>=s.totalSteps||e("SET_STEP",s.step+1))},async prevStep({commit:e,state:t}){t.step<0||e("SET_STEP",t.step-1)},async menuStep({commit:e,dispatch:t}){t("resetForm")},async validate({commit:e,getters:t,state:s},{schema:i,values:a}){try{e("SET_FORM_ERRORS",{}),await i.validate(a,{abortEarly:!1})}catch(t){if(!(t instanceof f.a))throw t;return t.inner&&t.inner.forEach(t=>{e("SET_FORM_ERRORS",{...s.formErrors,[t.path]:t.message})}),!1}return!0},async createNewVariantConfiguration({commit:e,dispatch:t}){await t("clearFormErrors"),e("SET_VARIANT_CONFIGURATION",j())},async clearFormErrors({commit:e}){e("SET_FORM_ERRORS",{})}},getters:{allowedTypes:e=>p.concat(e.hasDimensions?v:[]),settingsByType:e=>t=>e.variantConfiguration.settings[t]||{},isAllFieldsSelected(e){const t=e.variantConfigurationTypeFields.map(e=>e.handle);return t.filter(t=>e.variantConfiguration.fields.includes(t)).length===t.length},isAllValuesSelected:(e,t)=>s=>{const i=t.fieldValuesByHandle[s];return i.filter(t=>e.variantConfiguration.values.includes(t)).length===i.length},selectedFields:e=>e.variantConfigurationTypeFields.filter(t=>e.variantConfiguration.fields.includes(t.handle)),selectedOptionsByFieldHandle(e,t){const s={};return t.selectedFields.forEach(t=>{s[t.handle]=[],t.values.forEach(i=>{e.variantConfiguration.values.includes(i.value)&&s[t.handle].push(i)})}),s},fieldValuesByHandle(e){const t={};return e.variantConfigurationTypeFields.forEach(e=>{t[e.handle]=e.values.map(({value:e})=>e)}),t},optionValuesById(e){const t={};return e.variantConfigurationTypeFields.forEach(e=>{e.values.forEach(e=>{t[e.value]=e.label})}),t},selectedOptionValuesByFieldHandle(e,t){const s={};return t.selectedFields.forEach(t=>{s[t.handle]=[],t.values.forEach(i=>{e.variantConfiguration.values.includes(i.value)&&s[t.handle].push(i.value)})}),s},currentStepState:(e,t)=>t.stepState(e.step),stepState:(e,t)=>e=>{switch(e){case 0:default:return O.indexStep;case 1:return O.nameStep;case 2:return O.fieldsStep;case 3:return O.valuesStep;case 4:return O.settingsStep;case 5:return O.generateStep}},defaultSettings:()=>V}}),P=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",[s("div",{staticClass:"body"},[s("div",{staticClass:"content-summary"},[s("AppHeader",{attrs:{title:e.state.header.title,subtitle:e.state.header.subtitle}})],1),e._v(" "),e.isLoading?s("AppLoading"):e._e(),e._v(" "),e.isLoading?e._e():s(e.state.component,e._b({tag:"component"},"component",{hasDimensions:e.hasDimensions},!1))],1),e._v(" "),s("AppFooter",{attrs:{isLoading:e.isLoading,isSubmitting:e.isSubmitting,isCompleted:e.isCompleted,priBtnText:e.state.footer.priBtnText,secBtnText:e.state.footer.secBtnText,step:e.step},on:{"close-modal":e.handleCloseModal}})],1)};P._withStripped=!0;var B=function(){var e=this.$createElement;this._self._c;return this._m(0)};B._withStripped=!0;var U={},k=s("KHd+"),G=Object(k.a)(U,B,[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticStyle:{display:"flex","align-items":"center"}},[this._v("\n  Loading...\n  "),t("span",{staticClass:"spinner"})])}],!1,null,null,null);G.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/AppLoading.vue";var L=G.exports,H=function(){var e=this.$createElement,t=this._self._c||e;return t("div",[t("h1",{staticStyle:{"font-size":"20px","font-weight":"bold"}},[this._v("\n    "+this._s(this.title)+"\n  ")]),this._v(" "),t("p",{staticStyle:{"margin-bottom":"2rem !important"}},[this._v("\n    "+this._s(this.subtitle)+"\n  ")])])};H._withStripped=!0;var M={props:{title:String,subtitle:String}},q=Object(k.a)(M,H,[],!1,null,null,null);q.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/AppHeader.vue";var Y=q.exports,Q=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{staticClass:"footer"},[e.step>0?s("div",{staticClass:"buttons left"},[s("button",{staticClass:"btn icon nav",on:{click:e.menuStep}},[e._v("Menu")])]):e._e(),e._v(" "),e.isCompleted?s("div",{staticClass:"buttons right"},[s("button",{staticClass:"btn submit",on:{click:function(t){return e.$emit("close-modal")}}},[e._v("Finish")])]):s("div",{staticClass:"buttons right"},[s("button",{staticClass:"btn",on:{click:e.handleSecBtnClick}},[e._v("\n      "+e._s(e.secBtnText)+"\n    ")]),e._v(" "),s("button",{staticClass:"btn submit",class:{disabled:e.isLoading,add:e.isSubmitting,icon:e.isSubmitting,loading:e.isSubmitting},attrs:{disabled:e.isLoading||e.isSubmitting},on:{click:e.nextStep}},[e._v("\n      "+e._s(e.priBtnText)+"\n    ")])])])};Q._withStripped=!0;var J={props:{isLoading:Boolean,isSubmitting:Boolean,isCompleted:Boolean,priBtnText:String,secBtnText:String,step:Number},methods:{...Object(a.b)(["nextStep","prevStep","menuStep"]),handleSecBtnClick(){0===this.step?this.$emit("close-modal"):this.prevStep()}}},K=Object(k.a)(J,Q,[],!1,null,null,null);K.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/AppFooter.vue";var z=K.exports,W=function(){var e=this.$createElement,t=this._self._c||e;return t("div",[t("table",{ref:"table",staticClass:"display data",attrs:{id:"variant_configurations"}},[this._m(0),this._v(" "),t("tbody")])])};W._withStripped=!0;var X={data:()=>({dt:null}),computed:Object(a.e)({variantConfigurations:e=>e.variantConfigurations,dtData(){const e=[];return this.variantConfigurations.forEach(t=>{const s=null==t.dateUpdated?null:new Date(t.dateUpdated).toLocaleDateString();e.push([t.id,t.title,t.numberOfVariants,s])}),e}}),created(){F.once("form-submission",this.createNewVariantConfiguration)},mounted(){const e=this;this.$nextTick(()=>{this.dt=$(this.$refs.table).DataTable({data:this.dtData,columnDefs:[{targets:[0],visible:!1,searchable:!1}]}),$(".dataTable").on("click","tbody tr",(function(){const t=e.dt.row(this).data();e.clearFormErrors(),e.setCurrentVariantConfigurationById(t[0])}))})},methods:{...Object(a.b)(["setCurrentVariantConfigurationById","createNewVariantConfiguration","clearFormErrors"])},beforeDestroy(){this.dt.destroy(),F.off("form-submission",this.createNewVariantConfiguration)}},Z=Object(k.a)(X,W,[function(){var e=this.$createElement,t=this._self._c||e;return t("thead",[t("tr",[t("th",[this._v("ID")]),this._v(" "),t("th",[this._v("Name")]),this._v(" "),t("th",[this._v("Number of Variants")]),this._v(" "),t("th",[this._v("Last Updated")])])])}],!1,null,null,null);Z.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/Steps/IndexStep.vue";var ee=Z.exports,te=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("form",{staticClass:"field width-100"},[e._m(0),e._v(" "),e._m(1),e._v(" "),s("div",{staticClass:"input ltr",class:{errors:e.errors.title}},[s("textarea",{staticClass:"nicetext text",staticStyle:{"min-height":"32px"},attrs:{id:"configuration-name",name:"title",rows:"1",cols:"50",placeholder:""},on:{input:function(t){return e.handleInput(t.target.value)},keydown:function(t){return e.handleEnterKey(t)}}},[e._v(e._s(e.title))]),e._v(" "),e.errors.title?s("ul",{staticClass:"errors"},[s("li",[e._v(e._s(e.errors.title))])]):e._e()])])};te._withStripped=!0;var se={computed:Object(a.e)({title:e=>e.variantConfiguration.title,errors:e=>e.formErrors}),methods:{...Object(a.d)({setTitle:"SET_VARIANT_CONFIGURATION_TITLE"}),...Object(a.b)(["validate","nextStep"]),async handleInput(e){const{rules:t}=g;await this.validate({values:{title:e},schema:t}),this.setTitle(e)},handleEnterKey(e){"Enter"===e.key&&(e.preventDefault(),this.nextStep())}}},ie=Object(k.a)(se,te,[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"heading"},[t("label",{attrs:{for:"configuration-name"}},[this._v("Name")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"instructions",attrs:{id:"configuration-name-instructions"}},[t("p",[this._v('Give your configuration a name e.g. "Accent Colours"')])])}],!1,null,null,null);ie.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/Steps/NameStep.vue";var ae=ie.exports,ne=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{staticClass:"field width-100"},[s("div",{staticClass:"heading"},[s("label",{attrs:{for:"configuration-fields"}},[e._v("Fields\n      "),s("a",{staticStyle:{"font-size":"11px","font-weight":"normal","margin-left":"2px"},attrs:{href:"#"},on:{click:e.toggleAllSelectedFields}},[e._v(e._s(e.isAllSelected?"Unselect all":"Select all"))])])]),e._v(" "),s("div",{staticClass:"input ltr",class:{errors:e.errors.fields}},[s("fieldset",{staticClass:"checkbox-group"},e._l(e.fields,(function(t){return s("div",[s("input",{directives:[{name:"model",rawName:"v-model",value:e.variantFields,expression:"variantFields"}],staticClass:"checkbox",attrs:{type:"checkbox",name:"fields[]",id:"fields-"+t.handle},domProps:{value:t.handle,checked:Array.isArray(e.variantFields)?e._i(e.variantFields,t.handle)>-1:e.variantFields},on:{change:function(s){var i=e.variantFields,a=s.target,n=!!a.checked;if(Array.isArray(i)){var r=t.handle,l=e._i(i,r);a.checked?l<0&&(e.variantFields=i.concat([r])):l>-1&&(e.variantFields=i.slice(0,l).concat(i.slice(l+1)))}else e.variantFields=n}}}),e._v(" "),s("label",{attrs:{for:"fields-"+t.handle}},[e._v("\n          "+e._s(t.name)+"\n        ")])])})),0),e._v(" "),e.errors.fields?s("ul",{staticClass:"errors"},[s("li",[e._v(e._s(e.errors.fields))])]):e._e()])])};ne._withStripped=!0;var re={computed:{...Object(a.e)({fields:e=>e.variantConfigurationTypeFields,errors:e=>e.formErrors}),...Object(a.c)({isAllSelected:"isAllFieldsSelected"}),variantFields:{get(){return this.$store.state.variantConfiguration.fields},async set(e){const{rules:t}=S;await this.validate({values:{fields:e},schema:t}),this.setFields(e)}}},methods:{...Object(a.d)({setFields:"SET_VARIANT_CONFIGURATION_FIELDS"}),...Object(a.b)(["toggleAllSelectedFields","validate"])}},le=Object(k.a)(re,ne,[],!1,null,null,null);le.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/Steps/FieldsStep.vue";var oe=le.exports,ce=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",e._l(e.selectedFields,(function(t){return s("div",{staticClass:"field width-100"},[s("div",{staticClass:"heading"},[s("label",{attrs:{for:"configuration-fields"}},[e._v(e._s(t.name)+"\n        "),s("a",{staticStyle:{"font-size":"11px","font-weight":"normal","margin-left":"2px"},attrs:{href:"#"},on:{click:function(s){return e.handleToggleValues(t.handle)}}},[e._v(e._s(e.isAllSelected(t.handle)?"Unselect all":"Select all"))])])]),e._v(" "),s("div",{staticClass:"input ltr",class:{errors:e.errors.values}},[s("fieldset",{staticClass:"checkbox-group",staticStyle:{display:"flex","flex-wrap":"wrap"}},e._l(t.values,(function(t){var i=t.label,a=t.value;return s("div",{staticStyle:{width:"25%"}},[s("input",{directives:[{name:"model",rawName:"v-model",value:e.values,expression:"values"}],staticClass:"checkbox",attrs:{type:"checkbox",id:"fields-"+a,name:"fields[]"},domProps:{value:a,checked:Array.isArray(e.values)?e._i(e.values,a)>-1:e.values},on:{change:function(t){var s=e.values,i=t.target,n=!!i.checked;if(Array.isArray(s)){var r=a,l=e._i(s,r);i.checked?l<0&&(e.values=s.concat([r])):l>-1&&(e.values=s.slice(0,l).concat(s.slice(l+1)))}else e.values=n}}}),e._v(" "),s("label",{attrs:{for:"fields-"+a}},[e._v("\n            "+e._s(i)+"\n          ")])])})),0),e._v(" "),e.errors.values?s("ul",{staticClass:"errors"},[s("li",[e._v(e._s(e.errors.values))])]):e._e()])])})),0)};ce._withStripped=!0;var de={computed:{...Object(a.e)({fields:e=>e.variantConfigurationTypeFields,errors:e=>e.formErrors,variantValues:e=>e.variantConfiguration.values,variantFields:e=>e.variantConfiguration.fields}),...Object(a.c)({selectedFields:"selectedFields",isAllSelected:"isAllValuesSelected"}),values:{get(){return this.variantValues},async set(e){await this.isValid(e),this.setValues(e)}}},methods:{...Object(a.b)(["toggleAllSelectedValues","validate"]),...Object(a.d)({setValues:"SET_VARIANT_CONFIGURATION_VALUES"}),handleToggleValues(e){this.toggleAllSelectedValues(e),this.isValid(this.variantValues)},async isValid(e){const{rules:t}=y;return await this.validate({values:{values:e},schema:t})}}},ue=Object(k.a)(de,ce,[],!1,null,null,null);ue.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/Steps/ValuesStep.vue";var pe=ue.exports,ve=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",e._l(e.allowedTypes,(function(t,i){return s("div",[s("AppSettings",{attrs:{type:t,hasDimensions:e.hasDimensions}}),e._v(" "),i<e.allowedTypes.length-1?s("hr"):e._e()],1)})),0)};ve._withStripped=!0;var he=function(){var e=this,t=e.$createElement,s=e._self._c||t;return"weight"!==e.type||e.hasDimensions?s("div",{staticClass:"field"},[s("div",{staticClass:"heading"},[s("label",[e._v(e._s(e.typeTitle))]),e._v(" "),s("div",{staticClass:"instructions"},[e._v("\n      "+e._s(e.typeInstructions)+"\n    ")])]),e._v(" "),s("div",{staticClass:"input ltr"},[s("fieldset",[s("div",[s("input",{directives:[{name:"model",rawName:"v-model",value:e.method,expression:"method"}],staticClass:"radio",attrs:{type:"radio",id:"settings-"+e.type+"-all",name:"settings["+e.type+"][method]",value:"all"},domProps:{checked:e._q(e.method,"all")},on:{change:function(t){e.method="all"}}}),e._v(" "),s("label",{attrs:{for:"settings-"+e.type+"-all"}},[e._v("Set "+e._s(e.typeTitle)+" for all variants")])]),e._v(" "),"all"===e.method?s("div",{staticClass:"field",staticStyle:{"margin-left":"32px"}},[s("div",{staticClass:"heading"},[s("label",[e._v(e._s(e.typeTitle))])]),e._v(" "),s("div",{staticClass:"input ltr"},[s("div",{staticClass:"flex"},["price"===e.type?s("div",[e._v("$")]):e._e(),e._v(" "),s("input",{staticClass:"nicetext text",attrs:{type:"text"},domProps:{value:e.values.value},on:{input:function(t){return e.setFieldValue("value",t.target.value)}}}),e._v(" "),"weight"===e.type?s("div",[e._v("g")]):e._e(),e._v(" "),e.hasLength?s("div",[e._v("mm")]):e._e()])]),e._v(" "),e.errors["settings."+e.type+".values.value"]?s("ul",{staticClass:"errors"},[s("li",[e._v(e._s(e.errors["settings."+e.type+".values.value"]))])]):e._e()]):e._e(),e._v(" "),s("div",[s("input",{directives:[{name:"model",rawName:"v-model",value:e.method,expression:"method"}],staticClass:"radio",attrs:{type:"radio",id:"settings-"+e.type+"-field",name:"settings["+e.type+"][method]",value:"field"},domProps:{checked:e._q(e.method,"field")},on:{change:function(t){e.method="field"}}}),e._v(" "),s("label",{attrs:{for:"settings-"+e.type+"-field"}},[e._v("Set "+e._s(e.typeTitle)+" per field")])]),e._v(" "),"field"===e.method?s("div",{staticClass:"field",staticStyle:{"margin-left":"32px"}},[e._m(0),e._v(" "),s("div",{staticClass:"input ltr"},[s("select",{directives:[{name:"model",rawName:"v-model",value:e.field,expression:"field"}],attrs:{name:"",id:""},on:{change:function(t){var s=Array.prototype.filter.call(t.target.options,(function(e){return e.selected})).map((function(e){return"_value"in e?e._value:e.value}));e.field=t.target.multiple?s:s[0]}}},[s("option",{domProps:{value:null}},[e._v("Select")]),e._v(" "),e._l(e.selectedFields,(function(t){return e.selectedValuesByHandle[t.handle].length?s("option",{domProps:{value:t.handle}},[e._v("\n              "+e._s(t.name)+"\n            ")]):e._e()}))],2)]),e._v(" "),e.errors["settings."+e.type+".field"]?s("ul",{staticClass:"errors"},[s("li",[e._v(e._s(e.errors["settings."+e.type+".field"]))])]):e._e(),e._v(" "),e._l(e.selectedValuesByHandle[e.field],(function(t){var i=t.label,a=t.value;return e.field?s("div",{staticClass:"field"},[s("div",{staticClass:"heading"},[s("label",[e._v(e._s(i))])]),e._v(" "),s("div",{staticClass:"input ltr"},[s("div",{staticClass:"flex"},["price"===e.type?s("div",[e._v("$")]):e._e(),e._v(" "),s("input",{staticClass:"nicetext text",attrs:{type:"text"},domProps:{value:e.values[a]},on:{input:function(t){return e.setFieldValue(a,t.target.value)}}}),e._v(" "),"weight"===e.type?s("div",[e._v("g")]):e._e(),e._v(" "),e.hasLength?s("div",[e._v("mm")]):e._e()])]),e._v(" "),e.errors["settings."+e.type+".values."+a]?s("ul",{staticClass:"errors"},[s("li",[e._v(e._s(e.errors["settings."+e.type+".values."+a]))])]):e._e()]):e._e()}))],2):e._e(),e._v(" "),s("div",[s("input",{directives:[{name:"model",rawName:"v-model",value:e.method,expression:"method"}],staticClass:"radio",attrs:{type:"radio",id:"settings-"+e.type+"-skip",name:"settings["+e.type+"][method]",value:"skip"},domProps:{checked:e._q(e.method,"skip")},on:{change:function(t){e.method="skip"}}}),e._v(" "),s("label",{attrs:{for:"settings-"+e.type+"-skip"}},[e._v("Skip "+e._s(e.typeTitle))])])]),e._v(" "),e.errors["settings."+e.type+".method"]?s("ul",{staticClass:"errors"},[s("li",[e._v(e._s(e.errors["settings."+e.type+".method"]))])]):e._e()])]):e._e()};he._withStripped=!0;var fe={props:{type:{type:String,required:!0},hasDimensions:{type:Boolean,required:!0}},computed:{hasLength(){return["length","height","width"].includes(this.type)},...Object(a.e)({variantSettings(e){return e.variantConfiguration.settings[this.type]||this.defaultSettings},errors:e=>e.formErrors}),...Object(a.c)({defaultSettings:"defaultSettings",selectedFields:"selectedFields",selectedValuesByHandle:"selectedOptionsByFieldHandle"}),typeTitle(){const e=Craft.t("craft-commerce-untitled",this.type);return e.charAt(0).toUpperCase()+e.slice(1)},typeInstructions(){switch(this.type){case"sku":return`Use ${this.templateVars} to dynamically set ${this.typeTitle} from field values`;default:return""}},templateVars(){return this.selectedFields.map(e=>`{${e.handle}}`).join(", ")},method:{get(){return this.variantSettings.method},async set(e){const t={...this.variantSettings,method:e};this.setSettingsType({type:this.type,data:t})}},field:{get(){return this.variantSettings.field},async set(e){const t={...this.variantSettings,field:e};this.setSettingsType({type:this.type,data:t})}},values:{get(){return this.variantSettings.values},async set(e){const t={...this.variantSettings,values:e};this.setSettingsType({type:this.type,data:t})}}},methods:{...Object(a.b)(["validate"]),...Object(a.d)({setSettingsType:"SET_VARIANT_CONFIGURATION_SETTINGS_TYPE"}),setFieldValue(e,t){const s={},i=this.variantSettings.values;null==t||""==t?delete i[e]:s[e]=t;const a={...this.variantSettings,values:{...i,...s}};this.setSettingsType({type:this.type,data:a})}}},_e=Object(k.a)(fe,he,[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"heading"},[t("label",[this._v("Choose a field")])])}],!1,null,null,null);_e.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/AppSettings.vue";var me=_e.exports,ge={props:{hasDimensions:{type:Boolean,required:!0}},created(){F.once("form-submission",this.saveVariantConfiguration)},computed:{...Object(a.c)(["allowedTypes"])},methods:{...Object(a.b)(["saveVariantConfiguration"])},beforeDestroy(){F.off("form-submission",this.saveVariantConfiguration)},components:{AppSettings:me}},Se=Object(k.a)(ge,ve,[],!1,null,null,null);Se.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/Steps/SettingsStep.vue";var ye=Se.exports,Te=function(){var e=this,t=e.$createElement,s=e._self._c||t;return e.isGenerating?s("div",[s("h2",[e._v("Hold Tight!")]),e._v(" "),e._m(2)]):s("div",[s("h2",{staticStyle:{"margin-bottom":"5px"}},[e._v("Config Summary")]),e._v(" "),s("div",{staticStyle:{"margin-bottom":"16px"}},[e._v("The following config will be saved:")]),e._v(" "),s("div",{staticClass:"field",staticStyle:{"margin-bottom":"16px 0"}},[e._m(0),e._v(" "),s("div",{staticStyle:{"margin-bottom":"16px"}},[e._v(e._s(e.variantConfiguration.title))]),e._v(" "),e._l(e.selectedFields,(function(t){return s("div",[s("div",{staticStyle:{"margin-bottom":"5px","font-weight":"bold",color:"#606d7b"}},[e._v("\n        "+e._s(t.name)+"\n      ")]),e._v(" "),s("ul",{staticStyle:{margin:"0 0 16px 16px","list-style-type":"disc"}},e._l(e.selectedOptionsByFieldHandle[t.handle],(function(t){var i=t.label;t.value;return s("li",[e._v("\n          "+e._s(i)+"\n        ")])})),0)])})),e._v(" "),e._m(1),e._v(" "),e._l(e.allowedTypes,(function(t){return s("ul",{staticStyle:{margin:"0 0 0 16px","list-style-type":"disc"}},["all"===e.variantConfiguration.settings[t].method?s("li",[e._v("\n        "+e._s(e.getTitle(t))+" - "+e._s("price"===t?"$":"")+e._s(e.variantConfiguration.settings[t].values.value)+"\n        for all variants\n      ")]):e._e(),e._v(" "),"field"===e.variantConfiguration.settings[t].method?[s("li",[e._v(e._s(e.getTitle(t))+" - Per field")]),e._v(" "),s("ul",{staticStyle:{"margin-left":"16px","list-style-type":"revert"}},e._l(e.variantConfiguration.settings[t].values,(function(i,a){return"value"!==a?s("li",[e._v("\n            "+e._s(e.optionValuesById[a])+" - "+e._s("price"===t?"$":"")+e._s(i)+"\n          ")]):e._e()})),0)]:e._e(),e._v(" "),"skip"===e.variantConfiguration.settings[t].method?s("li",[e._v("\n        "+e._s(e.getTitle(t))+" - Skipped\n      ")]):e._e()],2)}))],2)])};Te._withStripped=!0;var be={data:()=>({isGenerating:!1}),computed:{...Object(a.e)({variantConfiguration:e=>e.variantConfiguration}),...Object(a.c)(["selectedFields","selectedOptionsByFieldHandle","fieldValuesByHandle","optionValuesById","allowedTypes"])},created(){F.once("form-submission",this,this.handleGenerateVariants)},beforeDestroy(){F.off("form-submission",this.handleGenerateVariants)},methods:{...Object(a.b)(["generateVariants"]),...Object(a.d)({setIsCompleted:"SET_IS_COMPLETED"}),getTitle:(e="")=>e.charAt(0).toUpperCase()+e.slice(1),async handleGenerateVariants(){await this.generateVariants(this.variantConfiguration.id),this.setIsCompleted(),this.isGenerating=!0}}},Ce=Object(k.a)(be,Te,[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"heading"},[t("label",[this._v("Title")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"heading"},[t("label",[this._v("Settings")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("p",[this._v("\n    Please wait while your variants are generated."),t("br"),this._v("\n    This can take several minutes and you can monitor progress on the queue\n    jobs page.\n  ")])}],!1,null,null,null);Ce.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/Steps/GenerateStep.vue";var Ie=Ce.exports,Oe={props:{productId:Number,productTypeId:Number,variantConfigurationTypeId:Number,$modal:Object,hasDimensions:Boolean},computed:{...Object(a.e)({isLoading:e=>e.ui.isLoading,isSubmitting:e=>e.ui.isSubmitting,isCompleted:e=>e.ui.isCompleted,step:e=>e.step}),...Object(a.c)({state:"currentStepState"})},async created(){this.setIsLoading(!0),this.setProductId(this.productId),this.setProductTypeId(this.productTypeId),this.setTypeId(this.variantConfigurationTypeId),this.setHasDimensions(this.hasDimensions),await this.fetchVariantConfigurations({productId:this.productId}),await this.fetchVariantConfigurationTypeFields({productTypeId:this.productTypeId}),this.setIsLoading(!1)},methods:{...Object(a.b)(["fetchVariantConfigurations","fetchVariantConfigurationTypeFields"]),...Object(a.d)({setIsLoading:"SET_IS_LOADING",setProductId:"SET_PRODUCT_ID",setProductTypeId:"SET_PRODUCT_TYPE_ID",setTypeId:"SET_TYPE_ID",setHasDimensions:"SET_HAS_DIMENSIONS"}),handleCloseModal(){this.$modal.hide()}},components:{AppLoading:L,AppHeader:Y,AppFooter:z,IndexStep:ee,NameStep:ae,FieldsStep:oe,ValuesStep:pe,SettingsStep:ye,GenerateStep:Ie}},Ee=Object(k.a)(Oe,P,[],!1,null,null,null);Ee.options.__file="src/assetbundles/craftcommerceuntitled/src/js/components/App.vue";var Ae=Ee.exports;!function(e){e.fn.dataTable.ext.classes.sPageButton=e.fn.dataTable.ext.classes.sPageButton+" btn",e.fn.dataTable.ext.classes.sPageButtonActive="active",e.fn.dataTable.ext.classes.sFilterInput="text dataTables_filter__input",e.fn.dataTable.ext.classes.sSortAsc="ordered asc",e.fn.dataTable.ext.classes.sSortColumn="ordered",e.fn.dataTable.ext.classes.sSortDesc="ordered desc",Craft.CommerceUntitled=Garnish.Base.extend({settings:{productId:null,productTypeId:null,variantConfigurationTypeId:null,productVariantType:"standard",hasDimensions:!1},$dt:e(),init(t){this.settings=e.extend({},Craft.CommerceUntitled.defaults,t),"standard"!==this.settings.productVariantType&&(this.initEditBtn(),this.initDataTables()),this.handleVariantTypeChange()},initEditBtn(){const t=e(this.getEditBtnHtml());e("#header").find(".btngroup:first").before(t),t.on("click",e=>{e.preventDefault(),new Craft.VariantConfigurationModal({productId:this.settings.productId,productTypeId:this.settings.productTypeId,variantConfigurationTypeId:this.settings.variantConfigurationTypeId,hasDimensions:!!this.settings.hasDimensions,onClose:e=>{e.$destroy(),D.dispatch("resetForm"),this.$dt.ajax.reload()}})})},initDataTables(){const t=this.settings.productId;this.$dt=e("#configurable-variants").DataTable({autoWidth:!1,ajax:"/craft-commerce-untitled/api/v1/variants?productId="+t})},handleVariantTypeChange(){const t=this.settings.productId;e("#variant-type-field").find("select").on("change",e=>{const s=e.target.value;u(t,{variantType:s}).then(()=>{window.location.reload()})})},getEditBtnHtml:()=>'<button class="btn secondary">Edit Configurations</button>'}),Craft.VariantConfigurationModal=Garnish.Modal.extend({settings:{productId:null,productTypeId:null,variantConfigurationTypeId:null,hasDimensions:!1,onClose:()=>{}},closeOtherModals:!0,shadeClass:"modal-shade dark",init(t){this.setSettings(t,Craft.VariantConfigurationModal.defaults),this.$container=e('<div class="modal elementselectormodal">\n          <div id="variant-configuration-modal-container" style="height: 100%; overflow-y: auto; overscroll-behavior: contain;">\n            <div id="variant-configuration-app" />\n          </div>\n        </div>').appendTo(Garnish.$bod);const s=new i.a({el:"#variant-configuration-app",store:D,render:e=>e(Ae,{props:{productId:this.settings.productId,productTypeId:this.settings.productTypeId,variantConfigurationTypeId:this.settings.variantConfigurationTypeId,hasDimensions:!!this.settings.hasDimensions,$modal:this}})});this.on("hide",()=>{this.settings.onClose(s)}),this.base(this.$container,t)}})}(jQuery)}},[["ZpdD",1,2]]]);
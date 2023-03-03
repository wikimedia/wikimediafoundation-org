(()=>{var e={837:(e,t,r)=>{e.exports=r(48)},515:(e,t,r)=>{"use strict";r.r(t),r.d(t,{name:()=>l,settings:()=>u});const o=JSON.parse('{"apiVersion":2,"name":"simple-editorial-comments/editorial-comment","title":"Editorial Comment","description":"Leave comments on posts which will not render publicly","textdomain":"simple-editorial-comments","category":"widgets","icon":"edit-large","supports":{"inserter":true,"reusable":true},"attributes":{"comment":{"type":"string"}}}');function n(){return n=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},n.apply(this,arguments)}var i=r(610),c=(r(363),r(78)),a=r(3);const s=e=>{let{attributes:t,setAttributes:r}=e;const o=(0,c.useBlockProps)({className:"simple-editorial-comment"});return(0,i.createElement)("div",n({},o,{"data-note-text":(0,a.__)("Note","simple-editorial-comments")}),(0,i.createElement)(c.RichText,{className:"simple-editorial-comment__comment-text",tagName:"p",value:t.comment,placeholder:(0,a.__)("Leave an internal note about this article here...","simple-editorial-comments"),onChange:e=>r({comment:e})}),(0,i.createElement)("small",null,(0,a.__)("This comment will not render to users.","simple-editorial-comments")))},l=o.name,u={...o,edit:s,save:()=>null}},360:(e,t,r)=>{"use strict";r.r(t),r.d(t,{name:()=>l,settings:()=>u});var o=r(610),n=(r(363),r(78));const i=wp.blocks;var c=r(3);const a=JSON.parse('{"apiVersion":2,"name":"simple-editorial-comments/hidden-group","title":"Hidden Group","description":"Gather blocks in a hidden container which does not render on the frontend.","keywords":["container","wrapper","row","section"],"textdomain":"simple-editorial-comments","category":"design","icon":"hidden","supports":{"inserter":true,"reusable":false}}'),s=[["simple-editorial-comments/editorial-comment",{comment:(0,c.__)("Explain why this group is hidden.","simple-editorial-comments")}],["core/group",{}]],l=a.name,u={...a,edit:()=>{const e=(0,n.useBlockProps)({className:"simple-editorial-comments-hidden-group"});return(0,o.createElement)("div",e,(0,o.createElement)(n.InnerBlocks,{template:s,templateLock:"all"}))},save:()=>(0,o.createElement)(n.InnerBlocks.Content,null),transforms:{from:[{type:"block",blocks:["core/group"],transform:(e,t)=>(0,i.createBlock)(a.name,{},[(0,i.createBlock)("simple-editorial-comments/editorial-comment",{comment:(0,c.__)("Explain why this group is hidden.","simple-editorial-comments")}),(0,i.createBlock)("core/group",e,t)])},{type:"block",isMultiBlock:!0,blocks:["*"],__experimentalConvert:e=>{const t=e.map((e=>(0,i.createBlock)(e.name,e.attributes,e.innerBlocks)));return(0,i.createBlock)(a.name,{},[(0,i.createBlock)("simple-editorial-comments/editorial-comment",{comment:(0,c.__)("Explain why this group is hidden.","simple-editorial-comments")}),(0,i.createBlock)("core/group",{},t)])}}],to:[{type:"block",blocks:["core/group"],transform:(e,t)=>t.filter((e=>{let{name:t}=e;return"core/group"===t}))}]}}},748:(e,t,r)=>{"use strict";r.r(t),r.d(t,{name:()=>l,settings:()=>m});var o=r(610);r(363);const n=wp.components,i=wp.data,c=wp.editPost;var a=r(3),s=r(515);const l="simple-editorial-comments-list-sidebar",u=(e,t)=>t.name===s.name?(e.push(t),e):Array.isArray(t.innerBlocks)&&t.innerBlocks.length?t.innerBlocks.reduce(u,e):e,m={render:()=>{const{selectBlock:e}=(0,i.useDispatch)("core/editor"),t=(0,i.useSelect)((e=>e("core/editor").getBlocks().reduce(u,[])));return(0,o.createElement)(c.PluginSidebar,{name:l,icon:"edit-large",title:(0,a.__)("Simple Editorial Comments","simple-editorial-comments")},(0,o.createElement)("div",{className:"plugin-sidebar-content"},(0,o.createElement)(n.Panel,null,(0,o.createElement)(n.PanelBody,{title:(0,a.__)("Comment List","simple-editorial-comments")},t.map((t=>{const r=(t.attributes.comment||"").replace(/<[^>]+>/g,""),i=r.length>25?`${r.substr(0,25)}...`:r;return(0,o.createElement)(n.PanelRow,{key:`comment-link-${t.clientId}`},i||(0,a.__)("(no content)","simple-editorial-comments"),(0,o.createElement)("button",{onClick:()=>e(t.clientId)},(0,a.__)("Select","simple-editorial-comments")))}))))))}}},48:(e,t)=>{"use strict";function r(e){return r="function"===typeof Symbol&&"symbol"===typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"===typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}function o(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}function n(e,t){return n=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e},n(e,t)}function i(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,o=a(e);if(t){var n=a(this).constructor;r=Reflect.construct(o,arguments,n)}else r=o.apply(this,arguments);return c(this,r)}}function c(e,t){return!t||"object"!==r(t)&&"function"!==typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function a(e){return a=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)},a(e)}t.autoloadPlugins=t.autoloadBlocks=void 0;var s=window.wp,l=s.blocks,u=s.plugins,m=s.richText,d=s.hooks,p=s.data,f=function(){},g=function(e){var t=e.getContext,r=e.register,o=e.unregister,n=e.before,i=void 0===n?f:n,c=e.after,a=void 0===c?f:c,s=arguments.length>1&&void 0!==arguments[1]?arguments[1]:f,l={},u=function(){i();var e=t(),n=[];return e.keys().forEach((function(t){var i=e(t);if(i!==l[t]){var c=l[t];c&&console.groupCollapsed&&console.groupCollapsed("hot update: ".concat(t)),c&&o(l[t]),r(i),n.push(i),l[t]=i,c&&console.groupCollapsed&&console.groupEnd()}})),a(n),e},m=u();s(m,u)};var v=null,y=function(e){var t=e.name,r=e.settings,o=e.filters,n=e.styles;t&&r&&l.registerBlockType(t,r),o&&Array.isArray(o)&&o.forEach((function(e){var t=e.hook,r=e.namespace,o=e.callback;d.addFilter(t,r,o)})),n&&Array.isArray(n)&&n.forEach((function(e){return l.registerBlockStyle(t,e)}))};var b=function(e){var t=e.name,r=e.settings,o=e.filters,n=e.styles;t&&r&&l.unregisterBlockType(t),o&&Array.isArray(o)&&o.forEach((function(e){var t=e.hook,r=e.namespace;d.removeFilter(t,r)})),n&&Array.isArray(n)&&n.forEach((function(e){return l.unregisterBlockStyle(t,e.name)}))};var h=function(){v=p.select("core/block-editor").getSelectedBlockClientId(),p.dispatch("core/block-editor").clearSelectedBlock()};var k=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],t=e.map((function(e){return e.name}));t.length&&(p.select("core/block-editor").getBlocks().forEach((function(e){var r=e.name,o=e.clientId;t.includes(r)&&p.dispatch("core/block-editor").selectBlock(o)})),v?p.dispatch("core/block-editor").selectBlock(v):p.dispatch("core/block-editor").clearSelectedBlock(),v=null)};t.autoloadBlocks=function(e,t){var r=e.getContext,o=e.register,n=void 0===o?y:o,i=e.unregister,c=void 0===i?b:i,a=e.before,s=void 0===a?h:a,l=e.after;g({getContext:r,register:n,unregister:c,before:s,after:void 0===l?k:l},t)};var _=function(e){var t=e.name,r=e.settings,o=e.filters;t&&r&&u.registerPlugin(t,r),o&&Array.isArray(o)&&o.forEach((function(e){var t=e.hook,r=e.namespace,o=e.callback;d.addFilter(t,r,o)}))};var x=function(e){var t=e.name,r=e.settings,o=e.filters;t&&r&&u.unregisterPlugin(t),o&&Array.isArray(o)&&o.forEach((function(e){var t=e.hook,r=e.namespace;d.removeFilter(t,r)}))};t.autoloadPlugins=function(e,t){var r=e.getContext,o=e.register,n=void 0===o?_:o,i=e.unregister,c=void 0===i?x:i,a=e.before,s=e.after;g({getContext:r,register:n,unregister:c,before:a,after:s},t)};var B=function(e){var t=e.name,r=e.settings;t&&r&&m.registerFormatType(t,r)};var E=function(e){var t=e.name,r=e.settings;t&&r&&m.unregisterFormatType(t)}},447:(e,t,r)=>{var o={"./editorial-comment/index.js":515,"./hidden-group/index.js":360};function n(e){var t=i(e);return r(t)}function i(e){if(!r.o(o,e)){var t=new Error("Cannot find module '"+e+"'");throw t.code="MODULE_NOT_FOUND",t}return o[e]}n.keys=function(){return Object.keys(o)},n.resolve=i,e.exports=n,n.id=447},488:(e,t,r)=>{var o={"./editorial-comment-list-sidebar/index.js":748};function n(e){var t=i(e);return r(t)}function i(e){if(!r.o(o,e)){var t=new Error("Cannot find module '"+e+"'");throw t.code="MODULE_NOT_FOUND",t}return o[e]}n.keys=function(){return Object.keys(o)},n.resolve=i,e.exports=n,n.id=488},363:e=>{"use strict";e.exports=React},78:e=>{"use strict";e.exports=wp.blockEditor},610:e=>{"use strict";e.exports=wp.element},3:e=>{"use strict";e.exports=wp.i18n}},t={};function r(o){var n=t[o];if(void 0!==n)return n.exports;var i=t[o]={exports:{}};return e[o](i,i.exports,r),i.exports}r.d=(e,t)=>{for(var o in t)r.o(t,o)&&!r.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r.r=e=>{"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},(()=>{"use strict";var e=r(837);const t=(e,t)=>{0};(0,e.autoloadBlocks)({getContext:()=>r(447)},t),(0,e.autoloadPlugins)({getContext:()=>r(488)},t)})()})();
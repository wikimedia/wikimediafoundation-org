!function(e){var t={};function n(o){if(t[o])return t[o].exports;var i=t[o]={i:o,l:!1,exports:{}};return e[o].call(i.exports,i,i.exports,n),i.l=!0,i.exports}n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)n.d(o,i,function(t){return e[t]}.bind(null,i));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=260)}({0:function(e,t){e.exports=window.wp.element},1:function(e,t){e.exports=window.wp.i18n},10:function(e,t){e.exports=window.yoast.styledComponents},260:function(e,t,n){"use strict";n.r(t);var o=n(0),i=n(10),r=n.n(i),a=n(1);const c=i.createGlobalStyle`
	@media only screen and (min-width: 1024px) {
		.BeaconFabButtonFrame.BeaconFabButtonFrame {
			${e=>"1"===e.isRtl?"left":"right"}: 340px !important;
		}
	}
`;function l(e){const t=document.createElement("div");t.setAttribute("id","yoast-helpscout-beacon"),Object(o.render)(e,t),document.body.appendChild(t)}function s(){return!!document.getElementById("sidebar")}function d(e){""!==e&&(void 0!==(e=JSON.parse(e)).name&&void 0!==e.email&&(window.Beacon("prefill",{name:e.name,email:e.email}),delete e.name,delete e.email),window.Beacon("session-data",e))}function u(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"";!function(e,t){let n=e.Beacon||function(){};function o(){const e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,o){e.Beacon.readyQueue.push({method:t,options:n,data:o})},n.readyQueue=[],"complete"===t.readyState)return o();e.attachEvent?e.attachEvent("onload",o):e.addEventListener("load",o,!1)}(window,document,window.Beacon),window.Beacon("init",e),d(t),"1"===window.wpseoAdminGlobalL10n.isRtl&&window.Beacon("config",{display:{position:"left"}}),s()&&l(Object(o.createElement)(c,{isRtl:window.wpseoAdminGlobalL10n.isRtl}))}window.wpseoHelpScoutBeacon=u,window.wpseoHelpScoutBeaconConsent=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;const n=r.a.div`
		border-radius: 60px;
		height: 60px;
		position: fixed;
		transform: scale(1);
		width: 60px;
		z-index: 1049;
		bottom: 40px;
		box-shadow: rgba(0, 0, 0, 0.1) 0 4px 7px;
		${e=>"1"===e.isRtl?"left":"right"}: 40px;
		top: auto;
		border-width: initial;
		border-style: none;
		border-color: initial;
		border-image: initial;
		transition: box-shadow 250ms ease 0s, opacity 0.4s ease 0s, scale 1000ms ease-in-out 0s, transform 0.2s ease-in-out 0s;
	`,i=r.a.span`
		-webkit-box-align: center;
		align-items: center;
		color: white;
		cursor: pointer;
		display: flex;
		height: 100%;
		-webkit-box-pack: center;
		justify-content: center;
		left: 0;
		pointer-events: none;
		position: absolute;
		text-indent: -99999px;
		top: 0;
		width: 60px;
		will-change: opacity, transform;
		opacity: 1 !important;
		transform: rotate(0deg) scale(1) !important;
		transition: opacity 80ms linear 0s, transform 160ms linear 0s;
	`,d=()=>Object(o.createElement)(i,null,Object(o.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"52",height:"52"},Object(o.createElement)("path",{d:"M27.031 32h-2.488v-2.046c0-.635.077-1.21.232-1.72.154-.513.366-.972.639-1.381.272-.41.58-.779.923-1.109.345-.328.694-.652 1.049-.97l.995-.854a6.432 6.432 0 0 0 1.475-1.568c.39-.59.585-1.329.585-2.216 0-.635-.117-1.203-.355-1.703a3.7 3.7 0 0 0-.96-1.263 4.305 4.305 0 0 0-1.401-.783A5.324 5.324 0 0 0 26 16.114c-1.28 0-2.316.375-3.11 1.124-.795.75-1.286 1.705-1.475 2.865L19 19.693c.356-1.772 1.166-3.165 2.434-4.176C22.701 14.507 24.26 14 26.107 14c.947 0 1.842.131 2.682.392.84.262 1.57.648 2.185 1.16a5.652 5.652 0 0 1 1.475 1.892c.368.75.551 1.602.551 2.556 0 .728-.083 1.364-.248 1.909a5.315 5.315 0 0 1-.693 1.467 6.276 6.276 0 0 1-1.048 1.176c-.403.351-.83.71-1.28 1.073-.498.387-.918.738-1.26 1.057a4.698 4.698 0 0 0-.836 1.006 3.847 3.847 0 0 0-.462 1.176c-.095.432-.142.955-.142 1.568V32zM26 37a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z",fill:"#FFF"}))),p=r.a.button`
		-webkit-appearance: none;
		-webkit-box-align: center;
		align-items: center;
		bottom: 0;
		display: block;
		height: 60px;
		-webkit-box-pack: center;
		justify-content: center;
		line-height: 60px;
		position: relative;
		user-select: none;
		z-index: 899;
		background-color: rgb(164, 40, 106);
		color: white;
		cursor: pointer;
		min-width: 60px;
		-webkit-tap-highlight-color: transparent;
		border-radius: 200px;
		margin: 0;
		outline: none;
		padding: 0;
		border-width: initial;
		border-style: none;
		border-color: initial;
		border-image: initial;
		transition: background-color 200ms linear 0s, transform 200ms linear 0s;
	`,m=()=>{const[i,r]=Object(o.useState)(!0),l=s();return Object(o.createElement)(o.Fragment,null,l&&Object(o.createElement)(c,{isRtl:window.wpseoAdminGlobalL10n.isRtl}),i&&Object(o.createElement)(n,{className:l?"BeaconFabButtonFrame":"",isRtl:window.wpseoAdminGlobalL10n.isRtl},Object(o.createElement)(p,{onClick:function(){const n=Object(a.__)("When you click OK we will open our HelpScout beacon where you can find answers to your questions. This beacon will load our support data and also potentially set cookies.","wordpress-seo");window.confirm(n)&&(u(e,t),window.Beacon("open"),window.setTimeout(()=>{r(!1)},1e3))}},Object(o.createElement)(d,null))))};l(Object(o.createElement)(m,null))}}});
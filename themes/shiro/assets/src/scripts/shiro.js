// Modern JS

import './block-accordion';
import './block-collapsible-text';
import './block-hero-home';
import clockBlock from './clock-block';
import dimensionShim from './modules/dimension-shim';
import dropdown from './modules/dropdown';
import siteHeader from './modules/site-header';
import stickySiteHeader from './modules/sticky-site-header';
import tocNav from './modules/toc-nav';

clockBlock();
dropdown();
siteHeader();
dimensionShim();
stickySiteHeader();
tocNav();

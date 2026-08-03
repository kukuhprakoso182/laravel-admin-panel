import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import 'remixicon/fonts/remixicon.css';
import './utils/utils-register.js';
import './components/component-register.js';
import { bootAlpine } from './alpine-loader';

Alpine.plugin(collapse);
window.Alpine = Alpine;
bootAlpine(Alpine);

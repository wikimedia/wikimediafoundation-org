import { registerFormatType } from '@wordpress/rich-text';
import { name, settings } from './edit';
import './style.scss';

registerFormatType( name, settings );

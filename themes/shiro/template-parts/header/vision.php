<?php
/**
 * Setup Vision module
 *
 * @package shiro
 */

$visions = [
  get_theme_mod('wmf_vision_lang1'),
  get_theme_mod('wmf_vision_lang2'),
  get_theme_mod('wmf_vision_lang3'),
  get_theme_mod('wmf_vision_lang4'),
  get_theme_mod('wmf_vision_lang5')
];
$visions = array_filter($visions);

$visions_rtl = [
  get_theme_mod('wmf_vision_lang1_rtl'),
  get_theme_mod('wmf_vision_lang2_rtl'),
  get_theme_mod('wmf_vision_lang3_rtl'),
  get_theme_mod('wmf_vision_lang4_rtl'),
  get_theme_mod('wmf_vision_lang5_rtl')
];

if (empty($visions)) {
  $visions[] = '<span>Imagine a world</span> in which every single human being can freely share in the sum of all knowledge.';
  $visions_rtl[] = '';
}

$is_visible = 'is_visible';
foreach( array_combine($visions, $visions_rtl) as $vision => $vision_rtl ) {
  echo '<h1 class="vision '. esc_attr($is_visible), esc_attr($vision_rtl) .'">' . esc_html($vision) . '</h1>';
  $is_visible = '';
}
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

$visions_class = [
  get_theme_mod('wmf_vision_lang1_class'),
  get_theme_mod('wmf_vision_lang2_class'),
  get_theme_mod('wmf_vision_lang3_class'),
  get_theme_mod('wmf_vision_lang4_class'),
  get_theme_mod('wmf_vision_lang5_class')
];

$visions_langcode = [
  get_theme_mod('wmf_vision_lang1_langcode'),
  get_theme_mod('wmf_vision_lang2_langcode'),
  get_theme_mod('wmf_vision_lang3_langcode'),
  get_theme_mod('wmf_vision_lang4_langcode'),
  get_theme_mod('wmf_vision_lang5_langcode')
];

if (empty($visions)) {
  $visions[] = 'Imagine a world in which every single human being can freely share in the sum of all knowledge.';
  $visions_class[] = '';
  $visions_langcode[] = 'en-US';
}

$is_visible = 'is_visible';

foreach ($visions as $key => $vision) {

  $vision_output[$vision] = [
    'text' => $visions[$key],
    'rssclass' => $visions_class[$key],
    'langcode' => $visions_langcode[$key],
  ];

  echo '<h2 lang="'. esc_attr($vision_output[$vision]['langcode']) .'" class="hero-home__heading vision '. esc_attr($is_visible) .' '. esc_attr($vision_output[$vision]['rssclass']) .'">' . esc_html($vision_output[$vision]['text']) . '</h2>';
  $is_visible = '';
}

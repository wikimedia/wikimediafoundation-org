<?php
/**
 * Template Name: Stories Page
 *
 * Stories page gets the Stories picker from the Report pages, but renders them
 * out in a list instead of as cards. This works by filtering the query for the
 * normal Report Section page's "list" data, replacing that data with stories.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 */

require_once __DIR__ . '/page-report-section.php';

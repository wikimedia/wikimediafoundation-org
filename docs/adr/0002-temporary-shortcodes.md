# 1. Record architecture decisions

Date: 2020-06-19

## Status

Accepted

## Context

Currently, the production site uses Fieldmanager as a page builder UI. However, we want to switch to using Gutenberg blocks.
Fieldmanager field data isn't output as part as `the_content` but are stored in post meta.

The use of FM has resulted in a lot of complicated conditional rendering of template parts. ( see `template-parts/page` and `template-parts/modules`, and the function `wmf_get_template_part` and `wmf_get_template_data`).

We are unable to just turn on Gutenberg without refactoring the theme code.

MultilingualPress 2 is used on the site and is incompatible with Gutenberg. We have a separate plan to upgrade to v3 before the move to Gutenberg (prerequisite).
## Decision

At the time of writing, we have a tight timeframe to work on preparing for a Gutenberg migration. A theme rebuild would be the optimal solution, but is not being considered given the constraints. We have therefore decided to take an intermediary step, to make the Fieldmanager existing content displayable in the post content, by creating shortcodes which use the existing template parts to render the HTML. Where we can, we copy the code from the `modules` template parts which are the more atomic, thus bypassing a lot of rendering conditiontal logic.

We've decided to apply this to the homepage first, so when we enable Gutenberg, we switch the front page setting to a copy o the existing homepage with some tweaks for Gutenberg.

## Consequences

This gives us an upgrade path to using Gutenberg, by first using shortcodes in the content before migrating the templates to Gutenberg blocks.

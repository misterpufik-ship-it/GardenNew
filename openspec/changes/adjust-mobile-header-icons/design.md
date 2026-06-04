## Context

The mobile header currently renders the phone and Telegram quick links as a small flex group in `public_html/admiralteyskaya/couch/snippets/header.html`. The group is mobile-only and independent of the desktop navigation and full-screen mobile menu.

## Goals / Non-Goals

**Goals:**
- Move the mobile quick-link icon group slightly to the right.
- Make the two quick-link icons sit closer together.
- Preserve existing tap targets, links, icon styling, and desktop layout.

**Non-Goals:**
- Redesign the header.
- Change CMS fields, destinations, desktop navigation, or the mobile menu overlay.

## Decisions

- Use mobile-only CSS on `.mobile-quick-links` so the adjustment is isolated to the visible issue.
- Shift the group with a small transform instead of changing the header structure, keeping the centered branch selector and burger area intact.
- Reduce the link padding/gap modestly so the icons feel closer while remaining tappable.

## Risks / Trade-offs

- The group could become too close to the centered branch selector on very narrow screens -> keep the shift small and preserve fixed icon button dimensions.
- Smaller spacing could reduce perceived tap comfort -> avoid shrinking the icon buttons below their current practical touch area.

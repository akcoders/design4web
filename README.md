# Design4Web Studio

A custom, multipage, motion-first WordPress theme built for Design4web. Version 3.4 rebuilds the Clients archive as a reference-faithful, image-led portfolio with an equal two-column desktop feed, a single-column mobile feed and clean image-zoom interactions while retaining the logo-led header/footer and live Google review option.

## Install or update

The ready-to-upload release is `design4web-studio-3.4.0.zip`.

1. Download the ZIP without extracting it.
2. Open **WordPress Admin → Appearance → Themes → Add New → Upload Theme**.
3. Select the ZIP and choose **Install Now**.
4. If WordPress detects the older theme, choose **Replace current with uploaded**.
5. Activate it, then open **Settings → Permalinks** and click **Save Changes** once.
6. Visit the homepage or WordPress dashboard once so the safe content migration can complete.
7. Open **Appearance → Design4web Options** for the editing dashboard.

The theme safely creates Home, About, Services, Products, Work, Pricing, Journal and Contact destinations where needed. The v3 migration seeds missing service/product/pricing/social starter content, preserves existing posts and user-edited records, and adds the six product pages below Products. The primary navigation is maintained as Home, About, Services, Products, Clients, Testimonial and Contact Us; Products keeps its dynamic dropdown while Clients opens the Work archive and Testimonial jumps to the homepage review collection.

## Dynamic admin content

- **Services** — content, excerpt, icon, label, duration, qualifier, deliverables, image and display order.
- **Products** — title, copy, icon, kicker, benefits, workflow, accent, disclaimer and display order.
- **Pricing Plans** — monthly/project labels, supporting notes, badge, feature list, CTA and featured state.
- **Clients** — case-study copy, project types, client, period, industry, services, challenge, outcome, results, optional URL, image and display order.
- **Testimonials** — quote, client, company/role, rating, source, original-review URL, photo and order; these remain the fallback when Google is not configured or unavailable.
- **Testimonials → Google Reviews** — connect a Google Maps browser API key and Business Place ID to show current original Google reviews.
- **Social Feed** — Instagram/social URL, platform, handle, caption, thumbnail and order.
- **Process Steps** — title, description, number, icon and order.
- **FAQs** — question, answer, category and order.
- **Team** — name, role, biography, photo, email, LinkedIn and order.
- **Posts** — the Journal archive and article pages use normal WordPress posts.
- **Enquiries** — every valid form submission is saved privately for administrators before the email notification is attempted.

Global colors, section copy, statistics, Google review/profile URL, map embed URL, contact details, social links, section visibility and motion settings are under **Appearance → Customize → Design4web Theme Options**. Navigation is managed under **Appearance → Menus**. Logo and site icon use **Site Identity**.

## Pages and templates

- Homepage
- About
- Services archive and dynamic service detail pages
- Products archive and six dynamic product detail pages
- Creative pricing page with an editable plan-mode toggle
- Responsive, image-led Clients/Work archive with a two-column desktop feed, a single-column mobile feed and dynamic case studies
- Journal archive and article pages
- Contact and FAQ page
- Standard page, archive, search/404 and comment templates

## Motion system

The original motion layer includes a preloader, page transitions, split-word reveals, staggered card entrances, image curtains, portfolio image zooms, product orbits/pulses/tickers, an animated footer marquee, expandable service rows, parallax, counters, pointer spotlights, magnetic/ripple buttons, custom cursor, testimonial controls and rich hover/focus states. Same-page navigation uses native smooth scrolling, passive scroll handling and cached animation targets for a more consistent feel. The header and footer use the logo's lime/green family over a charcoal base, with matching navigation indicators, glass effects, CTA shines and ambient footer rings; the uploaded logo artwork itself is not recoloured.

Under **Brand & Motion**, administrators can independently control:

- Master advanced motion
- Animated preloader
- Page transitions
- Scroll parallax
- Decorative looping motion
- Desktop creative cursor

Touch devices receive a lighter motion profile. `prefers-reduced-motion`, keyboard operation, a testimonial pause control and JavaScript-failure fallbacks are included.

## Reviews, social feed and maps

- Review cards use a responsive horizontal rail, grow with the complete quote and never crop long review text.
- Locally managed cards are available in **Testimonials**. Set the source and original URL for every genuine review.
- Optional live reviews are configured under **Testimonials → Google Reviews**. The browser uses the official Places library and keeps the local cards as a fallback; the theme does not scrape or store Google review content.
- Google currently returns up to five reviews ordered by relevance. Live cards include the available author attribution, rating, relative date, original Google Maps review link and required Google Maps notice.
- In Google Cloud, enable billing, **Maps JavaScript API** and **Places API (New)**. Restrict the browser key to the live/staging HTTP referrers and to those APIs before saving it in WordPress.
- Social cards are managed in **Social Feed**. Upload the thumbnail and add the original Instagram/social URL; this avoids brittle unauthenticated scraping.
- The contact map works from the saved office address without a browser API key. A custom Google Maps embed URL can be supplied in the Customizer when required.

## Front-end stack

- Bootstrap 5.3.8, bundled locally
- Bootstrap Icons 1.13.1, bundled locally
- WordPress-bundled jQuery for plugin compatibility
- Original CSS and JavaScript interaction system

## Contact delivery

The AJAX form validates and sanitizes input, verifies a nonce, uses a honeypot and rate limit, saves the enquiry in WordPress, and then sends an email notification. Configure authenticated SMTP on the live server and test delivery after deployment.

## Content and assets

Legacy portfolio imagery in `assets/images/legacy` came from the client-owned Design4web website and is stored locally instead of hotlinked. Assigning a Featured Image to a project replaces its bundled fallback automatically. The Agenca and AiSensy references informed interaction pacing, information architecture and product grouping only; their proprietary code, copy and demo assets are not included.

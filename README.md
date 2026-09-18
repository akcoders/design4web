# Design4Web Studio

A custom, multipage, motion-first WordPress theme built for Design4web. Version 2.0 combines an original high-energy agency design with verified business content and portfolio material from the legacy Design4web website.

## Install or update

The ready-to-upload release is `design4web-studio-2.0.0.zip`.

1. Download the ZIP without extracting it.
2. Open **WordPress Admin → Appearance → Themes → Add New → Upload Theme**.
3. Select the ZIP and choose **Install Now**.
4. If WordPress detects the older theme, choose **Replace current with uploaded**.
5. Activate it, then open **Settings → Permalinks** and click **Save Changes** once.
6. Open **Appearance → Design4web Options** for the editing dashboard.

The theme safely creates Home, About, Contact and Journal pages, configures the front/blog pages when those locations are empty, and creates a proper multipage menu when the previous menu only contains homepage anchors.

## Dynamic admin content

- **Services** — content, excerpt, icon, label, duration, qualifier, deliverables, image and display order.
- **Projects** — case-study copy, project types, client, period, industry, services, challenge, outcome, results, optional URL, image and display order.
- **Testimonials** — quote, client, company/role, rating, photo and order.
- **Process Steps** — title, description, number, icon and order.
- **FAQs** — question, answer, category and order.
- **Team** — name, role, biography, photo, email, LinkedIn and order.
- **Posts** — the Journal archive and article pages use normal WordPress posts.
- **Enquiries** — every valid form submission is saved privately for administrators before the email notification is attempted.

Global colors, section copy, statistics, contact details, social links, section visibility and motion settings are under **Appearance → Customize → Design4web Theme Options**. Navigation is managed under **Appearance → Menus**. Logo and site icon use **Site Identity**.

## Pages and templates

- Homepage
- About
- Services archive and dynamic service detail pages
- Filterable Work archive and dynamic case studies
- Journal archive and article pages
- Contact and FAQ page
- Standard page, archive, search/404 and comment templates

## Motion system

The original motion layer includes a preloader, page transitions, split-word reveals, staggered card entrances, image curtains, expandable service rows, project filtering, parallax, counters, pointer spotlights, magnetic/ripple buttons, custom cursor, testimonial controls and rich hover/focus states.

Under **Brand & Motion**, administrators can independently control:

- Master advanced motion
- Animated preloader
- Page transitions
- Scroll parallax
- Decorative looping motion
- Desktop creative cursor

Touch devices receive a lighter motion profile. `prefers-reduced-motion`, keyboard operation, a testimonial pause control and JavaScript-failure fallbacks are included.

## Front-end stack

- Bootstrap 5.3.8, bundled locally
- Bootstrap Icons 1.13.1, bundled locally
- WordPress-bundled jQuery for plugin compatibility
- Original CSS and JavaScript interaction system

## Contact delivery

The AJAX form validates and sanitizes input, verifies a nonce, uses a honeypot and rate limit, saves the enquiry in WordPress, and then sends an email notification. Configure authenticated SMTP on the live server and test delivery after deployment.

## Content and assets

Legacy portfolio imagery in `assets/images/legacy` came from the client-owned Design4web website and is stored locally instead of hotlinked. Assigning a Featured Image to a project replaces its bundled fallback automatically. The Agenca reference informed interaction pacing and hierarchy only; its proprietary theme code and demo assets are not included.

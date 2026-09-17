# Design4Web Studio

A custom, motion-first classic WordPress theme created for Design4web.

## Install

The ready-to-upload archive is [`design4web-studio-1.1.0.zip`](./design4web-studio-1.1.0.zip).

1. Download the ZIP without extracting it.
2. In WordPress, open **Appearance → Themes → Add New → Upload Theme**.
3. Select the ZIP, click **Install Now**, and activate **Design4Web Studio**.
4. Open **Appearance → Design4web Options** to finish the setup.

For a manual install, copy this repository into `wp-content/themes/design4web-studio` and activate it in WordPress.

## Admin editing

- **Appearance → Design4web Options** is the theme dashboard.
- **Appearance → Customize → Design4web Theme Options** controls colors, animation options, hero/about/CTA copy, statistics, section visibility, phone, email, WhatsApp and social links.
- **Services**, **Projects** and **Testimonials** are dynamic content types in the WordPress sidebar.
- Featured images control service/project/client imagery. Project types are editable categories.
- **Appearance → Menus** controls both the main and footer navigation.
- **Appearance → Customize → Site Identity** controls the uploaded logo and site icon.

## Contact form

The AJAX form validates and sanitizes all fields, verifies a nonce, and sends to the email configured in Theme Options. Configure authenticated SMTP on the live server for reliable delivery.

## Front-end stack

- Bootstrap 5.3.8 (local production build)
- jQuery 4.0.0 (local production build)
- Bootstrap Icons 1.13.1
- Custom CSS/JavaScript animations with reduced-motion support

## Motion system

Version 1.1 adds staggered word and card reveals, image-mask entrances, section progress indicators, pointer spotlights, labeled project cursors, button ripples, page transitions and richer hover choreography. The complete motion layer can be switched off in **Appearance → Customize → Design4web Theme Options → Brand & Motion**. Touch devices and visitors using reduced-motion preferences receive optimized fallbacks.

All vendor assets are local except Google Fonts; the theme has system-font fallbacks.

## Production note

Configure authenticated SMTP for the enquiry form and confirm commercial usage rights for all demo imagery before making the website public.

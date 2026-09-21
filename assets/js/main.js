/* global d4wTheme */
(function ($) {
  'use strict';

  var $window = $(window);
  var $body = $('body');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var motionEnabled = $body.hasClass('d4w-motion-enabled') && !reducedMotion;
  var parallaxEnabled = motionEnabled && $body.hasClass('d4w-parallax-enabled');
  var pageTransitionsEnabled = motionEnabled && $body.hasClass('d4w-page-transition-enabled');
  var lastScroll = window.pageYOffset;
  var ticking = false;

  if (motionEnabled) $body.addClass('d4w-animate');

  function completePreloader() {
    $('.d4w-preloader').addClass('is-complete');
    $body.addClass('d4w-page-loaded');
    window.setTimeout(function () {
      $('.d4w-preloader').remove();
    }, 950);
  }

  function initPreloader() {
    var $preloader = $('.d4w-preloader');
    if (!$preloader.length || reducedMotion) {
      completePreloader();
      return;
    }

    var $count = $preloader.find('.d4w-preloader__count');
    var $line = $preloader.find('.d4w-preloader__line span');
    var start = performance.now();
    var duration = 900;

    function update(now) {
      var progress = Math.min((now - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var number = Math.round(eased * 100);
      $count.text(String(number).padStart(2, '0'));
      $line.css('width', number + '%');

      if (progress < 1) {
        window.requestAnimationFrame(update);
      } else {
        window.setTimeout(completePreloader, 180);
      }
    }

    window.requestAnimationFrame(update);
    window.setTimeout(completePreloader, 2200);
  }

  function updateScrollUI() {
    var current = window.pageYOffset;
    var max = document.documentElement.scrollHeight - window.innerHeight;
    var progress = max > 0 ? (current / max) * 100 : 0;
    var $header = $('.site-header');

    $('.d4w-scroll-progress').css('transform', 'scaleX(' + (progress / 100) + ')');
    $header.toggleClass('is-sticky', current > 24).removeClass('is-hidden');
    $('.d4w-back-top').toggleClass('is-visible', current > 650);

    if (motionEnabled) {
      if (parallaxEnabled) {
        $('.d4w-parallax').each(function () {
          var $element = $(this);
          var speed = parseFloat($element.data('speed')) || 0;
          var rect = this.getBoundingClientRect();
          if (rect.bottom > -150 && rect.top < window.innerHeight + 150) {
            var offset = (rect.top - window.innerHeight / 2) * speed;
            $element.css('--parallax-y', offset + 'px').css('translate', '0 var(--parallax-y)');
          }
        });
      }

      $('.d4w-motion-section').each(function () {
        var rect = this.getBoundingClientRect();
        var travel = rect.height + window.innerHeight;
        var sectionProgress = Math.max(0, Math.min(1, (window.innerHeight - rect.top) / travel));
        this.style.setProperty('--section-progress', sectionProgress.toFixed(4));
      });
    }

    lastScroll = current;
    ticking = false;
  }

  function requestScrollUpdate() {
    if (!ticking) {
      window.requestAnimationFrame(updateScrollUI);
      ticking = true;
    }
  }

  function initMobileMenu() {
    var $toggle = $('.d4w-menu-toggle');
    var $menu = $('#d4w-mobile-menu');
    var $inertRegions = $('#main-content, .site-footer, .site-branding');

    function setMenu(open) {
      $toggle.attr('aria-expanded', open ? 'true' : 'false');
      $toggle.attr('aria-label', open ? 'Close menu' : 'Open menu');
      $menu.attr('aria-hidden', open ? 'false' : 'true').toggleClass('is-open', open);
      $body.toggleClass('menu-open', open);
      $inertRegions.each(function () { this.inert = open; });
      $('.site-header').removeClass('is-hidden');
      if (open) {
        window.setTimeout(function () {
          $menu.find('a').first().trigger('focus');
        }, 400);
      }
    }

    $toggle.on('click', function () {
      setMenu($toggle.attr('aria-expanded') !== 'true');
    });

    $menu.on('click', 'a', function () {
      var $parent = $(this).parent();
      if ($parent.hasClass('menu-item-has-children') && !$parent.hasClass('is-submenu-open')) return;
      setMenu(false);
    });

    $(document).on('keydown', function (event) {
      if (event.key === 'Escape' && $body.hasClass('menu-open')) {
        setMenu(false);
        $toggle.trigger('focus');
      }

      if (event.key === 'Tab' && $body.hasClass('menu-open')) {
        var focusable = [$toggle[0]].concat($menu.find('a:visible, button:visible').get());
        if (!focusable.length) return;
        var current = focusable.indexOf(document.activeElement);
        var next = event.shiftKey ? current - 1 : current + 1;
        if (current === -1 || next < 0) next = focusable.length - 1;
        if (next >= focusable.length) next = 0;
        event.preventDefault();
        focusable[next].focus();
      }
    });

    $menu.find('.menu-item-has-children > a').on('click', function (event) {
      var $parent = $(this).parent();
      if (!$parent.hasClass('is-submenu-open')) {
        event.preventDefault();
        $parent.siblings('.menu-item-has-children').removeClass('is-submenu-open').children('a').attr('aria-expanded', 'false').next('.sub-menu').stop(true, true).slideUp(250);
        $parent.addClass('is-submenu-open');
        $(this).attr('aria-expanded', 'true').next('.sub-menu').stop(true, true).slideDown(250);
      }
    });
  }

  function initSmoothLinks() {
    $(document).on('click', 'a[href*="#"]', function (event) {
      var link = this;
      var url;

      try {
        url = new URL(link.href, window.location.href);
      } catch (error) {
        return;
      }

      if (!url.hash || url.pathname !== window.location.pathname || !$(url.hash).length) {
        return;
      }

      event.preventDefault();
      var $target = $(url.hash);
      var top = $target.offset().top - 74;
      if ($(link).hasClass('skip-link')) {
        $target[0].focus({ preventScroll: true });
      }
      $('html, body').stop().animate({ scrollTop: top }, reducedMotion || !motionEnabled ? 0 : 850, 'swing');
      window.history.replaceState(null, '', url.hash);
    });

    $('.d4w-back-top').on('click', function () {
      $('html, body').stop().animate({ scrollTop: 0 }, reducedMotion || !motionEnabled ? 0 : 800);
    });
  }

  function initWordReveals() {
    if (!motionEnabled) return;
    var selectors = [
      '.reveal-text',
      '.d4w-hero__title .line-inner',
      '.d4w-page-title',
      '.d4w-service-item h3 a',
      '.d4w-service-expander__heading strong',
      '.d4w-project-copy h3 a',
      '.d4w-portfolio-card h2 a',
      '.d4w-journal-card h2 a',
      '.d4w-related-card h3',
	  '.d4w-product-card h2',
	  '.d4w-home-product h3',
	  '.d4w-home-case h3',
	  '.d4w-work-card h2 a',
	  '.d4w-price-card h2',
	  '.d4w-product-benefit h3',
	  '.d4w-product-step h3',
      '.d4w-value-card h3',
      '.d4w-process-step h3',
      '.d4w-timeline__item h3',
      '.d4w-post-card h2 a',
      '.d4w-post-card h3 a',
      '.d4w-testimonial-slide blockquote'
    ];

    document.querySelectorAll(selectors.join(',')).forEach(function (element) {
      if (element.dataset.d4wSplit === 'true') return;
      var text = element.textContent.trim();
      if (!text) return;

      element.dataset.d4wSplit = 'true';
      element.textContent = '';

      var accessible = document.createElement('span');
      var visual = document.createElement('span');
      accessible.className = 'screen-reader-text';
      accessible.textContent = text;
      visual.className = 'd4w-split-visual';
      visual.setAttribute('aria-hidden', 'true');

      text.split(/\s+/).forEach(function (word, index, words) {
        var mask = document.createElement('span');
        var inner = document.createElement('span');
        mask.className = 'd4w-word';
        inner.className = 'd4w-word__inner';
        inner.style.setProperty('--word-index', index);
        inner.style.setProperty('--word-delay', Math.min(index * 42, 420) + 'ms');
        inner.textContent = word;
        mask.appendChild(inner);
        visual.appendChild(mask);
        if (index < words.length - 1) visual.appendChild(document.createTextNode(' '));
      });
      element.appendChild(accessible);
      element.appendChild(visual);
    });
  }

  function initChoreography() {
    if (!motionEnabled) return;
    var groups = [
      { selector: '.d4w-service-item', variant: 'motion-left' },
      { selector: '.d4w-service-expander', variant: 'motion-left' },
      { selector: '.d4w-project-card', variant: 'motion-mask' },
      { selector: '.d4w-portfolio-card', variant: 'motion-mask' },
	  { selector: '.d4w-product-card, .d4w-home-product, .d4w-price-card, .d4w-product-benefit', variant: 'motion-scale' },
	  { selector: '.d4w-home-case, .d4w-work-card, .d4w-social-card', variant: 'motion-mask' },
	  { selector: '.d4w-product-step, .d4w-pricing-includes__grid article', variant: 'motion-right' },
      { selector: '.d4w-process-step', variant: 'motion-right' },
      { selector: '.d4w-post-card', variant: 'motion-scale' },
      { selector: '.d4w-journal-card', variant: 'motion-scale' },
      { selector: '.d4w-related-card, .d4w-value-card, .d4w-contact-info-card, .d4w-team-card', variant: 'motion-scale' },
      { selector: '.d4w-faq-item, .d4w-timeline__item, .d4w-case-block, .d4w-case-metric', variant: 'motion-up' },
      { selector: '.site-footer .row > div', variant: 'motion-up' },
      { selector: '.footer-bottom', variant: 'motion-up' }
    ];

    groups.forEach(function (group) {
      document.querySelectorAll(group.selector).forEach(function (element, index) {
        element.classList.add('d4w-motion-item', group.variant);
        element.style.setProperty('--motion-index', index % 6);
        element.style.setProperty('--motion-delay', ((index % 6) * 85) + 'ms');
      });
    });

    $('.d4w-project-media').attr('data-cursor-label', 'VIEW');
    $('.d4w-portfolio-card__media, .d4w-case-visual').attr('data-cursor-label', 'VIEW');
    $('.d4w-post-card__media').attr('data-cursor-label', 'READ');
    $('.d4w-journal-card__media').attr('data-cursor-label', 'READ');
    $('.d4w-circle-link').attr('data-cursor-label', 'OPEN');
  }

  function initSectionMeters() {
    if (!motionEnabled) return;
    $('.site-main .section-space, .site-main .section-space-sm').each(function () {
      var $section = $(this);
      if ($section.children('.d4w-motion-meter').length) return;
      $section.addClass('d4w-motion-section').prepend('<span class="d4w-motion-meter" aria-hidden="true"></span>');
    });
  }

  function initSpotlights() {
    if (!motionEnabled || window.matchMedia('(pointer: coarse)').matches) return;
    var selector = '.d4w-service-item, .d4w-process-step, .d4w-post-card, .d4w-stats > div, .d4w-contact-form, .d4w-related-card, .d4w-value-card, .d4w-contact-info-card, .d4w-journal-card, .d4w-case-metric, .d4w-team-card, .d4w-product-card, .d4w-home-product, .d4w-price-card, .d4w-product-benefit, .d4w-product-step';

    $(selector).each(function () {
      if (!$(this).children('.d4w-card-glow').length) {
        $(this).prepend('<span class="d4w-card-glow" aria-hidden="true"></span>');
      }
    }).on('mousemove', function (event) {
      var rect = this.getBoundingClientRect();
      this.style.setProperty('--mouse-x', (event.clientX - rect.left) + 'px');
      this.style.setProperty('--mouse-y', (event.clientY - rect.top) + 'px');
      this.classList.add('is-pointing');
    }).on('mouseleave', function () {
      this.classList.remove('is-pointing');
    });
  }

  function initButtonRipples() {
    if (!motionEnabled) return;
    $(document).on('click', '.d4w-btn, .d4w-header-cta, .d4w-circle-link, .d4w-slider-controls button', function (event) {
      var rect = this.getBoundingClientRect();
      var size = Math.max(rect.width, rect.height) * 1.8;
      var $ripple = $('<span class="d4w-click-ripple" aria-hidden="true"></span>');
      $ripple.css({
        width: size,
        height: size,
        left: event.clientX - rect.left - size / 2,
        top: event.clientY - rect.top - size / 2
      });
      $(this).append($ripple);
      window.setTimeout(function () { $ripple.remove(); }, 700);
    });
  }

  function initPageTransitions() {
    var $transition = $('.d4w-page-transition');
    if (!$transition.length || !pageTransitionsEnabled) return;

    window.addEventListener('pageshow', function () {
      $body.removeClass('is-page-leaving');
    });

    $(document).on('click', 'a', function (event) {
      if (event.isDefaultPrevented() || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      if (this.target === '_blank' || this.hasAttribute('download')) return;

      var href = this.getAttribute('href');
      if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(href)) return;

      var destination;
      try {
        destination = new URL(this.href, window.location.href);
      } catch (error) {
        return;
      }

      if (destination.origin !== window.location.origin || destination.href === window.location.href || destination.hash) return;
      event.preventDefault();
      $body.addClass('is-page-leaving');
      window.setTimeout(function () { window.location.href = destination.href; }, 520);
    });
  }

  function initExpandableServices() {
    var $items = $('.d4w-service-expander');
    if (!$items.length) return;

    function openItem($item) {
      $items.removeClass('is-active').find('.d4w-service-expander__toggle').attr('aria-expanded', 'false');
      $items.find('.d4w-service-expander__panel').attr('aria-hidden', 'true').each(function () { this.inert = true; });
      $item.addClass('is-active').find('.d4w-service-expander__toggle').attr('aria-expanded', 'true');
      $item.find('.d4w-service-expander__panel').attr('aria-hidden', 'false').each(function () { this.inert = false; });
      window.setTimeout(function () {
        $item.find('.d4w-image-curtain').addClass('is-visible');
      }, 80);
    }

    $items.each(function () {
      var active = $(this).hasClass('is-active');
      $(this).find('.d4w-service-expander__panel').attr('aria-hidden', active ? 'false' : 'true').each(function () { this.inert = !active; });
    });

    $items.on('click', '.d4w-service-expander__toggle', function () {
      openItem($(this).closest('.d4w-service-expander'));
    });

    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
      $items.on('mouseenter', function () { openItem($(this)); });
    }
  }

  function initFaqs() {
    var $faqItems = $('.d4w-faq-item');
    $faqItems.each(function () {
      var open = $(this).hasClass('is-open');
      $(this).find('.d4w-faq-item__answer').attr('aria-hidden', open ? 'false' : 'true').each(function () { this.inert = !open; });
    });
    $faqItems.children('button').on('click', function () {
      var $item = $(this).closest('.d4w-faq-item');
      var willOpen = !$item.hasClass('is-open');
      $item.siblings().removeClass('is-open').find('> button').attr('aria-expanded', 'false');
      $item.siblings().find('.d4w-faq-item__answer').attr('aria-hidden', 'true').each(function () { this.inert = true; });
      $item.toggleClass('is-open', willOpen);
      $(this).attr('aria-expanded', willOpen ? 'true' : 'false');
      $item.find('.d4w-faq-item__answer').attr('aria-hidden', willOpen ? 'false' : 'true').each(function () { this.inert = !willOpen; });
    });
  }

  function initProjectFilters() {
    var $buttons = $('[data-project-filter]');
    var $items = $('.d4w-filter-item');
    if (!$buttons.length || !$items.length) return;

    $buttons.on('click', function () {
      var filter = $(this).data('project-filter');
      $buttons.removeClass('is-active').attr('aria-pressed', 'false');
      $(this).addClass('is-active').attr('aria-pressed', 'true');
      $items.each(function () {
        var types = String($(this).data('project-types') || '').split(/\s+/);
        var visible = filter === '*' || types.indexOf(filter) !== -1;
        $(this).toggleClass('is-filtered-out', !visible).attr('aria-hidden', visible ? 'false' : 'true');
      });
    });
  }

  function initHoverCards() {
    if (!motionEnabled || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    $('.d4w-hover-card').on('pointermove', function (event) {
      var rect = this.getBoundingClientRect();
      var x = (event.clientX - rect.left) / rect.width - 0.5;
      var y = (event.clientY - rect.top) / rect.height - 0.5;
      this.style.setProperty('--card-rotate-x', (-y * 3.5).toFixed(2) + 'deg');
      this.style.setProperty('--card-rotate-y', (x * 3.5).toFixed(2) + 'deg');
      this.classList.add('is-tilting');
    }).on('pointerleave', function () {
      this.classList.remove('is-tilting');
      this.style.removeProperty('--card-rotate-x');
      this.style.removeProperty('--card-rotate-y');
    });
  }

  function initMotionCleanup() {
    if (!motionEnabled) return;
    document.addEventListener('transitionend', function (event) {
      if (event.propertyName !== 'transform' && event.propertyName !== 'opacity' && event.propertyName !== 'clip-path') return;
      if (event.target.classList && (event.target.classList.contains('d4w-word__inner') || event.target.classList.contains('d4w-motion-item'))) {
        event.target.style.willChange = 'auto';
      }
    });
    window.setTimeout(function () {
      document.querySelectorAll('.d4w-page-loaded .d4w-word__inner, .d4w-page-loaded .d4w-motion-item').forEach(function (element) {
        element.style.willChange = 'auto';
      });
    }, 2200);
  }

  function initReveals() {
    var allElements = Array.from(document.querySelectorAll('.reveal-up, .reveal-text, .d4w-motion-item, .d4w-image-curtain'));
    var curtains = Array.from(document.querySelectorAll('.d4w-image-curtain'));
    var elements = allElements.filter(function (element) { return !element.classList.contains('d4w-image-curtain'); });

    if (!motionEnabled || reducedMotion || !('IntersectionObserver' in window)) {
      allElements.forEach(function (element) {
        element.classList.add('is-visible');
      });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting || entry.boundingClientRect.bottom < 0) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px 12% 0px', threshold: 0.02 });

    elements.forEach(function (element, index) {
      if (element.classList.contains('reveal-up') && !element.style.getPropertyValue('--motion-delay')) {
        element.style.setProperty('--motion-delay', Math.min((index % 4) * 70, 210) + 'ms');
      }
      observer.observe(element);
    });

    // A fully clipped element has a zero IntersectionObserver area in Chrome.
    // Read its layout box directly so curtain reveals still trigger reliably.
    var curtainFrame = 0;
    function scanCurtains() {
      curtainFrame = 0;
      var remaining = 0;
      curtains.forEach(function (curtain) {
        if (curtain.classList.contains('is-visible')) return;
        if (curtain.closest('[inert]')) {
          remaining += 1;
          return;
        }
        var rect = curtain.getBoundingClientRect();
        if (rect.top < window.innerHeight * 1.08) {
          curtain.classList.add('is-visible');
        } else {
          remaining += 1;
        }
      });
      if (!remaining) {
        window.removeEventListener('scroll', requestCurtainScan);
        window.removeEventListener('resize', requestCurtainScan);
      }
    }
    function requestCurtainScan() {
      if (!curtainFrame) curtainFrame = window.requestAnimationFrame(scanCurtains);
    }
    if (curtains.length) {
      window.addEventListener('scroll', requestCurtainScan, { passive: true });
      window.addEventListener('resize', requestCurtainScan);
      scanCurtains();
    }
  }

  function initCounters() {
    var counters = document.querySelectorAll('.counter');
    if (!counters.length) return;

    function runCounter(element) {
      if (element.dataset.animated === 'true') return;
      element.dataset.animated = 'true';
      var target = parseInt(element.dataset.count, 10) || 0;

      if (!motionEnabled) {
        element.textContent = target.toLocaleString();
        return;
      }

      var start = performance.now();
      var duration = 1400;
      function frame(now) {
        var progress = Math.min((now - start) / duration, 1);
        var value = Math.round(target * (1 - Math.pow(1 - progress, 3)));
        element.textContent = value.toLocaleString();
        if (progress < 1) window.requestAnimationFrame(frame);
      }
      window.requestAnimationFrame(frame);
    }

    if (!motionEnabled) {
      counters.forEach(runCounter);
      return;
    }

    if (!('IntersectionObserver' in window)) {
      counters.forEach(runCounter);
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          runCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.01 });
    counters.forEach(function (counter) { observer.observe(counter); });

    window.setTimeout(function () {
      counters.forEach(function (counter) {
        var rect = counter.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) runCounter(counter);
      });
    }, 1400);
  }

  function initPricingToggle() {
    var $buttons = $('[data-pricing-mode]');
    if (!$buttons.length) return;

    $buttons.on('click', function () {
      var mode = $(this).data('pricing-mode') === 'yearly' ? 'yearly' : 'monthly';
      $buttons.removeClass('is-active').attr('aria-pressed', 'false');
      $(this).addClass('is-active').attr('aria-pressed', 'true');
      $('.d4w-pricing-toggle').attr('data-active-mode', mode);
      $('.d4w-price-card__price').each(function () {
        var $price = $(this).find('strong');
        var $note = $(this).find('small');
        $(this).addClass('is-changing');
        window.setTimeout(function () {
          $price.text($price.data(mode));
          $note.text($note.data(mode));
          $price.closest('.d4w-price-card__price').removeClass('is-changing');
        }, motionEnabled ? 180 : 0);
      });
    });
  }

  function initCursor() {
    var $cursor = $('.d4w-cursor');
    var $dot = $('.d4w-cursor-dot');
    var $label = $cursor.find('.d4w-cursor-label');
    if (!$cursor.length || !motionEnabled || reducedMotion || window.matchMedia('(pointer: coarse)').matches) return;

    var mouseX = -100;
    var mouseY = -100;
    var cursorX = -100;
    var cursorY = -100;
    var cursorFrame = 0;
    var cursorRunning = false;

    function startCursor() {
      if (cursorRunning) return;
      cursorRunning = true;
      cursorFrame = window.requestAnimationFrame(render);
    }

    $(document).on('mousemove', function (event) {
      mouseX = event.clientX;
      mouseY = event.clientY;
      $dot.css('transform', 'translate3d(' + mouseX + 'px,' + mouseY + 'px,0)');
      $cursor.add($dot).addClass('is-visible');
      startCursor();
    }).on('mouseleave', function () {
      $cursor.add($dot).removeClass('is-visible');
      cursorRunning = false;
      window.cancelAnimationFrame(cursorFrame);
    }).on('mouseenter', 'a, button, input, textarea, select, .d4w-project-media', function () {
      $cursor.addClass('is-hovering');
    }).on('mouseleave', 'a, button, input, textarea, select, .d4w-project-media', function () {
      $cursor.removeClass('is-hovering');
    }).on('mouseenter', '[data-cursor-label]', function () {
      $label.text($(this).data('cursor-label'));
      $cursor.addClass('is-labeled');
    }).on('mouseleave', '[data-cursor-label]', function () {
      $cursor.removeClass('is-labeled');
      $label.text('');
    });

    function render() {
      if (!cursorRunning) return;
      if (document.hidden) { cursorRunning = false; return; }
      cursorX += (mouseX - cursorX) * 0.14;
      cursorY += (mouseY - cursorY) * 0.14;
      $cursor.css('transform', 'translate3d(' + cursorX + 'px,' + cursorY + 'px,0)');
      cursorFrame = window.requestAnimationFrame(render);
    }
  }

  function initMagnetic() {
    if (!motionEnabled || window.matchMedia('(pointer: coarse)').matches) return;
    $('.magnetic').each(function () {
      var element = this;
      $(element).on('mousemove', function (event) {
        var rect = element.getBoundingClientRect();
        var x = event.clientX - rect.left - rect.width / 2;
        var y = event.clientY - rect.top - rect.height / 2;
        element.style.transform = 'translate(' + (x * 0.16) + 'px,' + (y * 0.16) + 'px)';
      }).on('mouseleave', function () {
        element.style.transform = '';
      });
    });
  }

  function initServicePreview() {
    var $preview = $('.d4w-service-preview');
    if (!$preview.length || !motionEnabled || window.matchMedia('(pointer: coarse)').matches) return;

    $('.d4w-service-item').on('mouseenter', function () {
      $preview.find('img').attr('src', $(this).data('image'));
      $preview.addClass('is-visible');
    }).on('mousemove', function (event) {
      $preview.css({ left: event.clientX + 30, top: event.clientY - 10 });
    }).on('mouseleave', function () {
      $preview.removeClass('is-visible');
    });
  }

  function initProjectTilt() {
    if (!motionEnabled || window.matchMedia('(pointer: coarse)').matches) return;
    $('.d4w-project-media').on('mousemove', function (event) {
      var rect = this.getBoundingClientRect();
      var x = (event.clientX - rect.left) / rect.width - 0.5;
      var y = (event.clientY - rect.top) / rect.height - 0.5;
      $(this).css('transform', 'perspective(900px) rotateX(' + (-y * 2.2) + 'deg) rotateY(' + (x * 2.2) + 'deg)');
    }).on('mouseleave', function () {
      $(this).css('transform', '');
    });
  }

  function initTestimonials() {
    var $slides = $('.d4w-testimonial-slide');
    if (!$slides.length) return;
    var $track = $('.d4w-testimonial-track');
    var index = 0;
    var timer;
    var inView = false;
    var paused = false;
    var userPaused = false;
    var coarsePointer = window.matchMedia('(hover: none), (pointer: coarse)').matches;
    var $region = $track.closest('.d4w-testimonials');
    var $toggle = $('.d4w-testimonial-toggle');

    function updateHeight() {
      var height = $slides.eq(index).outerHeight(true);
      if (height) $track.css('height', height + 'px');
    }

    function show(next) {
      var oldIndex = index;
      index = (next + $slides.length) % $slides.length;
      if (oldIndex === index && $slides.eq(index).hasClass('is-active')) return;
      $slides.eq(oldIndex).addClass('is-leaving').removeClass('is-active');
      window.setTimeout(function () { $slides.eq(oldIndex).removeClass('is-leaving'); }, 550);
      $slides.eq(index).addClass('is-active');
      window.requestAnimationFrame(updateHeight);
    }

    function restart() {
      window.clearInterval(timer);
      if (motionEnabled && !coarsePointer && inView && !paused && !userPaused && !document.hidden && $slides.length > 1) {
        timer = window.setInterval(function () { show(index + 1); }, 6500);
      }
    }

    show(0);
    $('.d4w-testimonial-next').on('click', function () { show(index + 1); restart(); });
    $('.d4w-testimonial-prev').on('click', function () { show(index - 1); restart(); });
    $toggle.toggle(!coarsePointer && motionEnabled).on('click', function () {
      userPaused = !userPaused;
      $(this).attr('aria-pressed', userPaused ? 'true' : 'false')
        .attr('aria-label', userPaused ? 'Resume testimonial autoplay' : 'Pause testimonial autoplay')
        .find('i').toggleClass('bi-pause-fill', !userPaused).toggleClass('bi-play-fill', userPaused);
      restart();
    });
    $region.on('mouseenter focusin', function () { paused = true; restart(); });
    $region.on('mouseleave focusout', function () { paused = false; restart(); });
    $(document).on('visibilitychange', restart);
    $window.on('resize', updateHeight);

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        inView = entries[0].isIntersecting;
        restart();
      }, { threshold: 0.2 });
      observer.observe($track[0]);
    } else {
      inView = true;
      restart();
    }
  }

  function initContactForm() {
    var $forms = $('.d4w-ajax-contact');
    if (!$forms.length || typeof d4wTheme === 'undefined') return;

    $forms.each(function () {
      var $form = $(this);
      var $status = $form.find('.d4w-form-status');
      var $button = $form.find('button[type="submit"]');

      $form.on('submit', function (event) {
        event.preventDefault();
        var valid = true;
        $form.find('[required]').each(function () {
          var fieldValid = this.checkValidity();
          $(this).toggleClass('is-invalid', !fieldValid);
          valid = valid && fieldValid;
        });

        if (!valid) {
          $status.removeClass('is-success').addClass('is-error').text('Please complete the required fields.');
          return;
        }

        var original = $button.find('span').text();
        $button.prop('disabled', true).find('span').text('Sending…');
        $status.removeClass('is-success is-error').text('');

        var data = $form.serializeArray();
        data.push({ name: 'nonce', value: d4wTheme.nonce });

        $.ajax({
          url: d4wTheme.ajaxUrl,
          method: 'POST',
          data: $.param(data),
          dataType: 'json'
        }).done(function (response) {
          if (response.success) {
            $form[0].reset();
            $form.addClass('is-submitted');
            $status.addClass('is-success').text(response.data.message || d4wTheme.successText);
            window.setTimeout(function () { $form.removeClass('is-submitted'); }, 1800);
          } else {
            $status.addClass('is-error').text((response.data && response.data.message) || d4wTheme.errorText);
          }
        }).fail(function (xhr) {
          var message = d4wTheme.errorText;
          if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
            message = xhr.responseJSON.data.message;
          }
          $status.addClass('is-error').text(message);
        }).always(function () {
          $button.prop('disabled', false).find('span').text(original);
        });
      });

      $form.on('input change', '.is-invalid', function () {
        $(this).toggleClass('is-invalid', !this.checkValidity());
      });
    });
  }

  $(function () {
    initPreloader();
    initMobileMenu();
    initSmoothLinks();
    initWordReveals();
    initChoreography();
    initSectionMeters();
    initReveals();
    initCounters();
    initCursor();
    initMagnetic();
    initSpotlights();
    initButtonRipples();
    initPageTransitions();
    initExpandableServices();
    initFaqs();
    initProjectFilters();
    initHoverCards();
    initMotionCleanup();
    initServicePreview();
    initProjectTilt();
    initTestimonials();
	initPricingToggle();
    initContactForm();
    updateScrollUI();
  });

  $window.on('scroll resize', requestScrollUpdate);
  $window.on('load', function () {
    window.setTimeout(function () {
      $body.addClass('d4w-page-loaded');
    }, 60);
  });
})(jQuery);

/* global d4wTheme */
(function ($) {
  'use strict';

  var $window = $(window);
  var $body = $('body');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var motionEnabled = $body.hasClass('d4w-motion-enabled') && !reducedMotion;
  var parallaxEnabled = motionEnabled && $body.hasClass('d4w-parallax-enabled') && window.matchMedia('(min-width: 992px) and (pointer: fine)').matches;
  var pageTransitionsEnabled = motionEnabled && $body.hasClass('d4w-page-transition-enabled');
  var lastScroll = window.pageYOffset;
  var ticking = false;
  var scrollElements = {
    progress: null,
    header: null,
    backTop: null,
    parallax: [],
    sections: []
  };

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

  function cacheScrollElements() {
    scrollElements.progress = document.querySelector('.d4w-scroll-progress');
    scrollElements.header = document.querySelector('.site-header');
    scrollElements.backTop = document.querySelector('.d4w-back-top');
    scrollElements.parallax = parallaxEnabled ? Array.from(document.querySelectorAll('.d4w-parallax')) : [];
    scrollElements.sections = motionEnabled ? Array.from(document.querySelectorAll('.d4w-motion-section')) : [];
  }

  function updateScrollUI() {
    var current = window.pageYOffset;
    var max = document.documentElement.scrollHeight - window.innerHeight;
    var progress = max > 0 ? (current / max) * 100 : 0;

    if (scrollElements.progress) scrollElements.progress.style.transform = 'scaleX(' + (progress / 100) + ')';
    if (scrollElements.header) {
      scrollElements.header.classList.toggle('is-sticky', current > 24);
      scrollElements.header.classList.remove('is-hidden');
    }
    if (scrollElements.backTop) scrollElements.backTop.classList.toggle('is-visible', current > 650);

    if (motionEnabled) {
      if (parallaxEnabled) {
        scrollElements.parallax.forEach(function (element) {
          var speed = parseFloat(element.dataset.speed) || 0;
          var rect = element.getBoundingClientRect();
          if (rect.bottom > -150 && rect.top < window.innerHeight + 150) {
            var offset = (rect.top - window.innerHeight / 2) * speed;
            element.style.setProperty('--parallax-y', offset + 'px');
            element.style.translate = '0 var(--parallax-y)';
          }
        });
      }

      scrollElements.sections.forEach(function (section) {
        var rect = section.getBoundingClientRect();
        var travel = rect.height + window.innerHeight;
        var sectionProgress = Math.max(0, Math.min(1, (window.innerHeight - rect.top) / travel));
        section.style.setProperty('--section-progress', sectionProgress.toFixed(4));
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

      if (!url.hash || url.pathname !== window.location.pathname || url.search !== window.location.search) {
        return;
      }

      var targetId;
      try {
        targetId = decodeURIComponent(url.hash.slice(1));
      } catch (error) {
        return;
      }
      var target = document.getElementById(targetId);
      if (!target) return;

      event.preventDefault();
      var top = target.getBoundingClientRect().top + window.pageYOffset - 82;
      if ($(link).hasClass('skip-link')) {
        target.focus({ preventScroll: true });
      }
      window.scrollTo({ top: Math.max(0, top), behavior: reducedMotion || !motionEnabled || $(link).hasClass('skip-link') ? 'auto' : 'smooth' });
      window.history.replaceState(null, '', url.hash);
    });

    $('.d4w-back-top').on('click', function () {
      window.scrollTo({ top: 0, behavior: reducedMotion || !motionEnabled ? 'auto' : 'smooth' });
    });
  }

  function initWordReveals() {
    if (!motionEnabled) return;
    // Hero lines have their own slide reveal. Keeping their text intact also
    // prevents Chromium from losing an ancestor background-clipped gradient
    // when the temporary word layers are cleaned up after the intro.
    var selectors = [
      '.reveal-text',
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
	  '.d4w-footer-cta h2',
	  '.d4w-price-card h2',
	  '.d4w-product-benefit h3',
	  '.d4w-product-step h3',
      '.d4w-value-card h3',
      '.d4w-process-step h3',
      '.d4w-timeline__item h3',
      '.d4w-post-card h2 a',
      '.d4w-post-card h3 a'
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
	  { selector: '.d4w-footer-cta, .d4w-footer-grid > div', variant: 'motion-up' },
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
    var selector = '.d4w-service-item, .d4w-process-step, .d4w-post-card, .d4w-stats > div, .d4w-contact-form, .d4w-related-card, .d4w-value-card, .d4w-contact-info-card, .d4w-journal-card, .d4w-case-metric, .d4w-team-card, .d4w-product-card, .d4w-home-product, .d4w-price-card, .d4w-product-benefit, .d4w-product-step, .d4w-work-card';

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
    var $loadMore = $('[data-work-load-more]');
    if (!$items.length) return;

    var pageSize = window.matchMedia('(max-width: 767.98px)').matches ? 6 : 8;
    var visibleLimit = $loadMore.length ? pageSize : Number.POSITIVE_INFINITY;
    var activeFilter = '*';

    function renderProjects() {
      var matched = 0;
      var shown = 0;

      $items.each(function () {
        var $item = $(this);
        var types = String($item.data('project-types') || '').split(/\s+/);
        var matches = activeFilter === '*' || types.indexOf(activeFilter) !== -1;
        var withinLimit = matches && shown < visibleLimit;
        if (matches) {
          matched += 1;
          if (withinLimit) shown += 1;
        }
        $item.toggleClass('is-filtered-out', !matches);
        $item.toggleClass('is-load-hidden', matches && !withinLimit);
        $item.attr('aria-hidden', withinLimit ? 'false' : 'true');
      });

      if ($loadMore.length) {
        var remaining = Math.max(matched - shown, 0);
        $loadMore.toggleClass('is-complete', remaining === 0).prop('disabled', remaining === 0);
		$loadMore.closest('.d4w-work-more').toggleClass('is-complete', remaining === 0);
        $loadMore.find('small').text(remaining ? '+' + Math.min(pageSize, remaining) : 'All projects visible');
      }
    }

    $buttons.on('click', function () {
	  activeFilter = $(this).data('project-filter');
	  visibleLimit = $loadMore.length ? pageSize : Number.POSITIVE_INFINITY;
      $buttons.removeClass('is-active').attr('aria-pressed', 'false');
      $(this).addClass('is-active').attr('aria-pressed', 'true');
	  renderProjects();
    });

	$loadMore.on('click', function () {
	  visibleLimit += pageSize;
	  renderProjects();
	  var $firstNew = $items.filter(':not(.is-filtered-out):not(.is-load-hidden)').eq(Math.max(visibleLimit - pageSize, 0));
	  if ($firstNew.length) $firstNew.find('a').first().trigger('focus');
	});

	renderProjects();
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

  var googleMapsLoader;

  function safeExternalUrl(value) {
    if (!value) return '';
    try {
      var parsed = new URL(value, window.location.href);
      return parsed.protocol === 'https:' || parsed.protocol === 'http:' ? parsed.href : '';
    } catch (error) {
      return '';
    }
  }

  function loadGoogleMaps(config) {
    if (window.google && window.google.maps && window.google.maps.importLibrary) {
      return Promise.resolve(window.google.maps);
    }
    if (googleMapsLoader) return googleMapsLoader;

    googleMapsLoader = new Promise(function (resolve, reject) {
      var callbackName = 'd4wGoogleMapsReady';
      var script = document.createElement('script');
      var params = new URLSearchParams({
        key: config.apiKey,
        loading: 'async',
        libraries: 'places',
        callback: callbackName,
        v: 'weekly',
        language: config.language || 'en',
        region: config.region || 'IN',
        auth_referrer_policy: 'origin'
      });

      window[callbackName] = function () {
        delete window[callbackName];
        resolve(window.google.maps);
      };
      script.src = 'https://maps.googleapis.com/maps/api/js?' + params.toString();
      script.async = true;
      script.onerror = function () {
        delete window[callbackName];
        reject(new Error('Google Maps could not load.'));
      };
      document.head.appendChild(script);
    });

    return googleMapsLoader;
  }

  function createReviewStars(rating) {
    var stars = document.createElement('div');
    var rounded = Math.max(0, Math.min(5, Math.round(Number(rating) || 0)));
    stars.className = 'd4w-stars';
    stars.setAttribute('aria-label', rounded + ' out of 5 stars');
    for (var index = 0; index < 5; index += 1) {
      var star = document.createElement('i');
      star.className = 'bi ' + (index < rounded ? 'bi-star-fill' : 'bi-star');
      star.setAttribute('aria-hidden', 'true');
      stars.appendChild(star);
    }
    return stars;
  }

  function createGoogleReviewCard(review) {
    var card = document.createElement('article');
    var top = document.createElement('div');
    var quoteIcon = document.createElement('i');
    var quote = document.createElement('blockquote');
    var author = document.createElement('div');
    var authorLink = document.createElement('a');
    var authorDetails = document.createElement('div');
    var authorName = document.createElement('strong');
    var reviewTime = document.createElement('span');
    var sourceLink = document.createElement('a');
    var sourceIcon = document.createElement('i');
    var attribution = review.authorAttribution || {};
    var authorUrl = safeExternalUrl(attribution.uri);
    var photoUrl = safeExternalUrl(attribution.photoURI);
    var reviewUrl = safeExternalUrl(review.googleMapsURI);
    var displayedText = review.originalText || review.text || '';

    card.className = 'd4w-testimonial-slide d4w-review-card d4w-google-review-card';
    top.className = 'd4w-review-card__top';
    quoteIcon.className = 'bi bi-google';
    quoteIcon.setAttribute('aria-hidden', 'true');
    top.appendChild(createReviewStars(review.rating));
    top.appendChild(quoteIcon);

    quote.textContent = '“' + displayedText + '”';

    author.className = 'd4w-testimonial-author';
    if (authorUrl || reviewUrl) {
      authorLink.href = authorUrl || reviewUrl;
      authorLink.target = '_blank';
      authorLink.rel = 'noopener noreferrer';
    }
    authorLink.setAttribute('aria-label', (attribution.displayName || 'Google reviewer') + ' on Google Maps');

    if (photoUrl) {
      var avatar = document.createElement('img');
      avatar.src = photoUrl;
      avatar.alt = '';
      avatar.width = 54;
      avatar.height = 54;
      avatar.loading = 'lazy';
      avatar.addEventListener('error', function () {
        var fallbackAvatar = document.createElement('span');
        fallbackAvatar.className = 'd4w-testimonial-avatar';
        fallbackAvatar.setAttribute('aria-hidden', 'true');
        fallbackAvatar.textContent = (attribution.displayName || 'G').charAt(0).toUpperCase();
        avatar.replaceWith(fallbackAvatar);
      });
      authorLink.appendChild(avatar);
    } else {
      var initial = document.createElement('span');
      initial.className = 'd4w-testimonial-avatar';
      initial.setAttribute('aria-hidden', 'true');
      initial.textContent = (attribution.displayName || 'G').charAt(0).toUpperCase();
      authorLink.appendChild(initial);
    }

    authorName.textContent = attribution.displayName || 'Google reviewer';
    reviewTime.textContent = review.relativePublishTimeDescription || 'Google review';
    authorDetails.appendChild(authorName);
    authorDetails.appendChild(reviewTime);
    authorLink.appendChild(authorDetails);
    author.appendChild(authorLink);

    if (reviewUrl) {
      sourceLink.className = 'd4w-review-source-link';
      sourceLink.href = reviewUrl;
      sourceLink.target = '_blank';
      sourceLink.rel = 'noopener noreferrer';
      sourceLink.setAttribute('aria-label', 'View original review on Google Maps');
      sourceLink.textContent = 'Original';
      sourceIcon.className = 'bi bi-arrow-up-right';
      sourceIcon.setAttribute('aria-hidden', 'true');
      sourceLink.appendChild(sourceIcon);
      author.appendChild(sourceLink);
    }

    card.appendChild(top);
    card.appendChild(quote);
    card.appendChild(author);
    return card;
  }

  function initGoogleReviews() {
    var track = document.querySelector('[data-google-reviews-track]');
    var config = window.d4wTheme && window.d4wTheme.googleReviews;
    if (!track || !config || !config.enabled || !config.apiKey || !config.placeId) return;

    var status = document.querySelector('[data-google-review-status]');
    track.setAttribute('aria-busy', 'true');
    loadGoogleMaps(config).then(function () {
      return window.google.maps.importLibrary('places');
    }).then(function (placesLibrary) {
      var place = new placesLibrary.Place({ id: config.placeId });
      return place.fetchFields({
        fields: ['displayName', 'reviews', 'rating', 'userRatingCount', 'googleMapsURI']
      }).then(function () { return place; });
    }).then(function (place) {
      var reviews = (place.reviews || []).filter(function (review) {
        return Boolean(review && (review.originalText || review.text));
      });
      if (!reviews.length) throw new Error('No Google reviews were returned.');

      var fragment = document.createDocumentFragment();
      reviews.forEach(function (review) { fragment.appendChild(createGoogleReviewCard(review)); });
      track.replaceChildren(fragment);
      track.setAttribute('aria-busy', 'false');

      var summary = document.querySelector('[data-google-review-summary]');
      if (summary) {
        summary.hidden = false;
        var rating = summary.querySelector('[data-google-rating]');
        var count = summary.querySelector('[data-google-count]');
        if (rating) rating.textContent = Number(place.rating || 0).toFixed(1);
        if (count) count.textContent = Number(place.userRatingCount || reviews.length).toLocaleString();
      }

      var profileUrl = safeExternalUrl(place.googleMapsURI);
      var profileLink = document.querySelector('[data-google-profile-link]');
      if (profileLink && profileUrl) profileLink.href = profileUrl;

      var notice = document.querySelector('[data-google-review-notice]');
      if (notice) {
        notice.hidden = false;
        var extraAttributions = notice.querySelector('[data-google-attributions]');
        if (extraAttributions && place.attributions && place.attributions.length) {
          extraAttributions.textContent = place.attributions.map(function (item) {
            return item.provider || String(item);
          }).filter(function (provider) {
            return provider && String(provider).toLowerCase() !== 'google maps';
          }).join(', ');
        }
      }

      if (status) status.textContent = 'Showing current reviews supplied by Google Maps.';
      $(track).trigger('d4w:reviews-updated');
    }).catch(function () {
      track.setAttribute('aria-busy', 'false');
      if (status) status.textContent = 'Live Google reviews are unavailable. Showing locally managed client reviews.';
    });
  }

  function initWorkSlider() {
    $('[data-work-slider]').each(function () {
      var $slider = $(this);
      var $viewport = $slider.find('.d4w-home-work__viewport');
      var $track = $slider.find('.d4w-home-work__track');
      var $items = $track.children('.d4w-client-card');
      var $previous = $slider.find('.d4w-home-work__prev');
      var $next = $slider.find('.d4w-home-work__next');
      var $dots = $slider.find('.d4w-home-work__dots');
      var $current = $slider.find('[data-work-current]');
      var $total = $slider.find('[data-work-total]');
      var index = 0;
      var pages = 1;
      var visible = 2;
      var resizeFrame = 0;
      var touchStart = null;
      var suppressClick = false;

      if (!$viewport.length || !$track.length || !$items.length) return;

      function pad(value) {
        return String(value).padStart(2, '0');
      }

      function buildDots() {
        $dots.empty();
        for (var dotIndex = 0; dotIndex < pages; dotIndex += 1) {
          $('<button type="button"></button>')
            .attr('aria-label', 'Go to work slide ' + (dotIndex + 1))
            .attr('data-work-slide', dotIndex)
            .appendTo($dots);
        }
      }

      function update(animate) {
        var gap = parseFloat(window.getComputedStyle($track[0]).columnGap) || 0;
        var distance = index * ($viewport[0].clientWidth + gap);
        if (animate === false) $track.css('transition', 'none');
        $track.css('transform', 'translate3d(' + (-distance) + 'px, 0, 0)');
        if (animate === false) {
          $track[0].offsetHeight;
          $track.css('transition', '');
        }

        $current.text(pad(index + 1));
        $total.text(pad(pages));
        $dots.children().removeClass('is-active').attr('aria-current', 'false').eq(index).addClass('is-active').attr('aria-current', 'true');

        var firstVisible = index * visible;
        var lastVisible = firstVisible + visible;
        $items.each(function (itemIndex) {
          var active = itemIndex >= firstVisible && itemIndex < lastVisible;
          $(this).toggleClass('is-slide-active', active).attr('aria-hidden', active ? 'false' : 'true');
          $(this).find('a').attr('tabindex', active ? null : '-1');
        });
      }

      function measure(animate) {
        var nextVisible = window.matchMedia('(max-width: 767.98px)').matches ? 1 : 2;
        var nextPages = Math.max(1, Math.ceil($items.length / nextVisible));
        if (nextVisible !== visible || nextPages !== pages || !$dots.children().length) {
          visible = nextVisible;
          pages = nextPages;
          index = Math.min(index, pages - 1);
          buildDots();
        }
        update(animate);
      }

      function goTo(nextIndex) {
        index = (nextIndex + pages) % pages;
        update(true);
      }

      $previous.on('click', function () { goTo(index - 1); });
      $next.on('click', function () { goTo(index + 1); });
      $dots.on('click', 'button', function () { goTo(Number($(this).attr('data-work-slide')) || 0); });
      $viewport.on('keydown', function (event) {
        if (event.key === 'ArrowLeft') {
          event.preventDefault();
          goTo(index - 1);
        } else if (event.key === 'ArrowRight') {
          event.preventDefault();
          goTo(index + 1);
        }
      });

      $viewport.on('touchstart', function (event) {
        var touch = event.originalEvent.touches[0];
        touchStart = touch ? { x: touch.clientX, y: touch.clientY } : null;
      });
      $viewport.on('touchend', function (event) {
        if (!touchStart) return;
        var touch = event.originalEvent.changedTouches[0];
        if (!touch) return;
        var deltaX = touch.clientX - touchStart.x;
        var deltaY = touch.clientY - touchStart.y;
        touchStart = null;
        if (Math.abs(deltaX) > 45 && Math.abs(deltaX) > Math.abs(deltaY)) {
          suppressClick = true;
          goTo(index + (deltaX < 0 ? 1 : -1));
          window.setTimeout(function () { suppressClick = false; }, 350);
        }
      });
      $viewport.on('click', 'a', function (event) {
        if (suppressClick) event.preventDefault();
      });

      function requestMeasure() {
        if (resizeFrame) window.cancelAnimationFrame(resizeFrame);
        resizeFrame = window.requestAnimationFrame(function () {
          resizeFrame = 0;
          measure(false);
        });
      }

      window.addEventListener('resize', requestMeasure);
      if ('ResizeObserver' in window) {
        var observer = new ResizeObserver(requestMeasure);
        observer.observe($viewport[0]);
      }
      measure(false);
    });
  }

  function initTestimonials() {
    $('.d4w-testimonials').each(function () {
      var $region = $(this);
      var $track = $region.find('.d4w-testimonial-track');
      var $previous = $region.find('.d4w-testimonial-prev');
      var $next = $region.find('.d4w-testimonial-next');
      var $current = $region.find('.d4w-slider-count b');
      var $total = $region.find('.d4w-slider-count span');
      var scrollFrame;
      var index = 0;

      if (!$track.length) return;

      function slides() {
        return $track.find('.d4w-testimonial-slide');
      }

      function updateControls() {
        var $slides = slides();
        var scrollLeft = $track[0].scrollLeft;
        var firstOffset = $slides.length ? $slides.get(0).offsetLeft : 0;
        var closestDistance = Infinity;
        $slides.each(function (slideIndex) {
          var distance = Math.abs(this.offsetLeft - firstOffset - scrollLeft);
          if (distance < closestDistance) {
            closestDistance = distance;
            index = slideIndex;
          }
        });
        $slides.removeClass('is-active').eq(index).addClass('is-active');
        $current.text(String(index + 1).padStart(2, '0'));
        $total.text(String(Math.max(1, $slides.length)).padStart(2, '0'));
        $previous.prop('disabled', index <= 0 || $slides.length < 2);
        $next.prop('disabled', index >= $slides.length - 1 || $slides.length < 2);
      }

      function goTo(nextIndex) {
        var $slides = slides();
        if (!$slides.length) return;
        index = Math.max(0, Math.min(nextIndex, $slides.length - 1));
        var target = $slides.get(index);
        var firstOffset = $slides.get(0).offsetLeft;
        $track[0].scrollTo({
          left: target.offsetLeft - firstOffset,
          behavior: reducedMotion ? 'auto' : 'smooth'
        });
        updateControls();
      }

      $previous.on('click', function () { goTo(index - 1); });
      $next.on('click', function () { goTo(index + 1); });
      $track.on('scroll', function () {
        window.cancelAnimationFrame(scrollFrame);
        scrollFrame = window.requestAnimationFrame(updateControls);
      });
      $track.on('d4w:reviews-updated', function () {
        index = 0;
        $track[0].scrollLeft = 0;
        updateControls();
      });
      $window.on('resize', updateControls);
      updateControls();
    });
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
    initWorkSlider();
    cacheScrollElements();
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
	initGoogleReviews();
	initPricingToggle();
    initContactForm();
    updateScrollUI();
  });

  window.addEventListener('scroll', requestScrollUpdate, { passive: true });
  window.addEventListener('resize', requestScrollUpdate);
  $window.on('load', function () {
    window.setTimeout(function () {
      $body.addClass('d4w-page-loaded');
    }, 60);
  });
})(jQuery);

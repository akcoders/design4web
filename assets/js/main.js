/* global d4wTheme */
(function ($) {
  'use strict';

  var $window = $(window);
  var $body = $('body');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var motionEnabled = $body.hasClass('d4w-motion-enabled') && !reducedMotion;
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

    $('.d4w-scroll-progress').css('width', progress + '%');
    $header.toggleClass('is-sticky', current > 70);
    $('.d4w-back-top').toggleClass('is-visible', current > 650);

    if (current > 450 && current > lastScroll + 8 && !$body.hasClass('menu-open')) {
      $header.addClass('is-hidden');
    } else if (current < lastScroll - 8 || current < 100) {
      $header.removeClass('is-hidden');
    }

    if (!reducedMotion) {
      $('.d4w-parallax').each(function () {
        var $element = $(this);
        var speed = parseFloat($element.data('speed')) || 0;
        var rect = this.getBoundingClientRect();
        if (rect.bottom > -150 && rect.top < window.innerHeight + 150) {
          var offset = (rect.top - window.innerHeight / 2) * speed;
          $element.css('--parallax-y', offset + 'px').css('translate', '0 var(--parallax-y)');
        }
      });

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

    function setMenu(open) {
      $toggle.attr('aria-expanded', open ? 'true' : 'false');
      $toggle.attr('aria-label', open ? 'Close menu' : 'Open menu');
      $menu.attr('aria-hidden', open ? 'false' : 'true').toggleClass('is-open', open);
      $body.toggleClass('menu-open', open);
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
      setMenu(false);
    });

    $(document).on('keydown', function (event) {
      if (event.key === 'Escape' && $body.hasClass('menu-open')) {
        setMenu(false);
        $toggle.trigger('focus');
      }
    });

    $menu.find('.menu-item-has-children > a').on('click', function (event) {
      if (!$(this).attr('href') || $(this).attr('href') === '#') {
        event.preventDefault();
        $(this).next('.sub-menu').stop(true, true).slideToggle(250);
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
      var top = $(url.hash).offset().top - 74;
      $('html, body').stop().animate({ scrollTop: top }, reducedMotion ? 0 : 850, 'swing');
      window.history.replaceState(null, '', url.hash);
    });

    $('.d4w-back-top').on('click', function () {
      $('html, body').stop().animate({ scrollTop: 0 }, reducedMotion ? 0 : 800);
    });
  }

  function initWordReveals() {
    if (!motionEnabled) return;
    var selectors = [
      '.reveal-text',
      '.d4w-hero__title .line-inner',
      '.d4w-service-item h3 a',
      '.d4w-project-copy h3 a',
      '.d4w-process-step h3',
      '.d4w-post-card h2 a',
      '.d4w-post-card h3 a',
      '.d4w-testimonial-slide blockquote'
    ];

    document.querySelectorAll(selectors.join(',')).forEach(function (element) {
      if (element.dataset.d4wSplit === 'true') return;
      var text = element.textContent.trim();
      if (!text) return;

      element.dataset.d4wSplit = 'true';
      element.setAttribute('aria-label', text);
      element.textContent = '';

      text.split(/\s+/).forEach(function (word, index, words) {
        var mask = document.createElement('span');
        var inner = document.createElement('span');
        mask.className = 'd4w-word';
        mask.setAttribute('aria-hidden', 'true');
        inner.className = 'd4w-word__inner';
        inner.style.setProperty('--word-index', index);
        inner.style.setProperty('--word-delay', (index * 45) + 'ms');
        inner.textContent = word;
        mask.appendChild(inner);
        element.appendChild(mask);
        if (index < words.length - 1) element.appendChild(document.createTextNode(' '));
      });
    });
  }

  function initChoreography() {
    if (!motionEnabled) return;
    var groups = [
      { selector: '.d4w-service-item', variant: 'motion-left' },
      { selector: '.d4w-project-card', variant: 'motion-mask' },
      { selector: '.d4w-process-step', variant: 'motion-right' },
      { selector: '.d4w-post-card', variant: 'motion-scale' },
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
    $('.d4w-post-card__media').attr('data-cursor-label', 'READ');
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
    var selector = '.d4w-service-item, .d4w-process-step, .d4w-post-card, .d4w-stats > div, .d4w-contact-form';

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
    if (!$transition.length || !motionEnabled) return;

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

  function initReveals() {
    var elements = document.querySelectorAll('.reveal-up, .reveal-text, .d4w-motion-item');

    if (reducedMotion || !('IntersectionObserver' in window)) {
      elements.forEach(function (element) {
        element.classList.add('is-visible');
      });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px', threshold: 0.06 });

    elements.forEach(function (element, index) {
      if (element.classList.contains('reveal-up') && !element.style.getPropertyValue('--motion-delay')) {
        element.style.setProperty('--motion-delay', Math.min((index % 4) * 70, 210) + 'ms');
      }
      observer.observe(element);
    });
  }

  function initCounters() {
    var counters = document.querySelectorAll('.counter');
    if (!counters.length) return;

    function runCounter(element) {
      if (element.dataset.animated === 'true') return;
      element.dataset.animated = 'true';
      var target = parseInt(element.dataset.count, 10) || 0;

      if (reducedMotion) {
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
    }, { threshold: 0.55 });
    counters.forEach(function (counter) { observer.observe(counter); });
  }

  function initCursor() {
    var $cursor = $('.d4w-cursor');
    var $dot = $('.d4w-cursor-dot');
    var $label = $cursor.find('.d4w-cursor-label');
    if (!$cursor.length || reducedMotion || window.matchMedia('(pointer: coarse)').matches) return;

    var mouseX = -100;
    var mouseY = -100;
    var cursorX = -100;
    var cursorY = -100;

    $(document).on('mousemove', function (event) {
      mouseX = event.clientX;
      mouseY = event.clientY;
      $dot.css('transform', 'translate3d(' + mouseX + 'px,' + mouseY + 'px,0)');
      $cursor.add($dot).addClass('is-visible');
    }).on('mouseleave', function () {
      $cursor.add($dot).removeClass('is-visible');
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
      cursorX += (mouseX - cursorX) * 0.14;
      cursorY += (mouseY - cursorY) * 0.14;
      $cursor.css('transform', 'translate3d(' + cursorX + 'px,' + cursorY + 'px,0)');
      window.requestAnimationFrame(render);
    }
    window.requestAnimationFrame(render);
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
    var index = 0;
    var timer;

    function show(next) {
      var oldIndex = index;
      index = (next + $slides.length) % $slides.length;
      if (oldIndex === index && $slides.eq(index).hasClass('is-active')) return;
      $slides.eq(oldIndex).addClass('is-leaving').removeClass('is-active');
      window.setTimeout(function () { $slides.eq(oldIndex).removeClass('is-leaving'); }, 550);
      $slides.eq(index).addClass('is-active');
    }

    function restart() {
      window.clearInterval(timer);
      if (!reducedMotion && $slides.length > 1) {
        timer = window.setInterval(function () { show(index + 1); }, 6500);
      }
    }

    show(0);
    restart();
    $('.d4w-testimonial-next').on('click', function () { show(index + 1); restart(); });
    $('.d4w-testimonial-prev').on('click', function () { show(index - 1); restart(); });
  }

  function initContactForm() {
    var $form = $('#d4w-contact-form');
    if (!$form.length || typeof d4wTheme === 'undefined') return;
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
          $status.addClass('is-success').text(response.data.message || d4wTheme.successText);
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
    initServicePreview();
    initProjectTilt();
    initTestimonials();
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

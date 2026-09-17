/* global d4wTheme */
(function ($) {
  'use strict';

  var $window = $(window);
  var $body = $('body');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var lastScroll = window.pageYOffset;
  var ticking = false;

  $body.addClass('d4w-animate');

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

  function initReveals() {
    var elements = document.querySelectorAll('.reveal-up, .reveal-text');

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
    }, { rootMargin: '0px 0px -9% 0px', threshold: 0.08 });

    elements.forEach(function (element, index) {
      if (element.classList.contains('reveal-up')) {
        element.style.transitionDelay = Math.min((index % 4) * 0.07, 0.21) + 's';
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
    if (reducedMotion || window.matchMedia('(pointer: coarse)').matches) return;
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
    if (!$preview.length || window.matchMedia('(pointer: coarse)').matches) return;

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
    if (reducedMotion || window.matchMedia('(pointer: coarse)').matches) return;
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
    initReveals();
    initCounters();
    initCursor();
    initMagnetic();
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

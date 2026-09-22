document.addEventListener('DOMContentLoaded', () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.content;
  const libraryUrl = document.querySelector('meta[name="library-url"]')?.content;
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ---------------------------------------------------------------
  // Toast — small reusable feedback bubble. Anything can call
  // showToast('message', 'fa-solid fa-icon') and it slides in, sits
  // for a few seconds, then slides back out on its own.
  // ---------------------------------------------------------------
  function showToast(message, icon = 'fa-solid fa-circle-check', action = null) {
    document.querySelectorAll('.toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `<i class="${icon}"></i><span>${message}</span>`;

    if (action) {
      const link = document.createElement('a');
      link.href = action.url;
      link.className = 'toast-action';
      link.textContent = action.text;
      toast.appendChild(link);
    }

    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('toast-show'));

    setTimeout(() => {
      toast.classList.remove('toast-show');
      toast.addEventListener('transitionend', () => toast.remove(), { once: true });
      setTimeout(() => toast.remove(), 500); // fallback if transitionend doesn't fire
    }, 4200);
  }

  // A flashed session('status') message arrives as static HTML on page
  // load. Re-animate it the same way and let it auto-dismiss instead of
  // sitting there forever.
  const flashedToast = document.querySelector('.toast:not(.toast-show)');
  if (flashedToast) {
    const message = flashedToast.textContent.trim();
    flashedToast.remove();
    if (message) showToast(message);
  }

  // ---------------------------------------------------------------
  // Save / unsave — works for the compact bookmark button on cards
  // (.save-fab) and the full text button on the book detail page
  // (button-outline). Same endpoint, same CSRF token, different
  // rendering per element so each fits its context.
  // ---------------------------------------------------------------
  function paintSaveButton(btn, saved) {
    const isFab = btn.classList.contains('save-fab');
    btn.classList.toggle('is-saved', saved);
    btn.setAttribute('aria-pressed', saved ? 'true' : 'false');
    btn.title = saved ? 'Remove from your library' : 'Save to your library';

    if (isFab) {
      btn.innerHTML = `<i class="fa-${saved ? 'solid' : 'regular'} fa-bookmark"></i>`;
    } else {
      btn.innerHTML = saved
        ? '<i class="fa-solid fa-bookmark"></i> Saved'
        : '<i class="fa-regular fa-bookmark"></i> Save';
    }

    if (!prefersReducedMotion) {
      btn.classList.remove('save-pop');
      // force reflow so the animation can replay on repeated clicks
      void btn.offsetWidth;
      btn.classList.add('save-pop');

      // A brief glow specifically for the moment a book gets saved (not
      // on unsave) — the pop already signals "state changed", the glow
      // signals "and it was a good thing".
      if (saved) {
        btn.classList.remove('save-glow');
        void btn.offsetWidth;
        btn.classList.add('save-glow');
      }
    }

    // A small inline confirmation next to the button, when the page
    // provides one (the full-text Save button on a book's detail page
    // has a [data-save-confirm] sibling; the compact card bookmark
    // relies on the toast alone since there's no room for inline text).
    const confirm = btn.parentElement?.querySelector('[data-save-confirm]');
    if (confirm) {
      clearTimeout(confirm._hideTimer);
      if (saved) {
        confirm.classList.add('save-confirm-show');
        confirm._hideTimer = setTimeout(() => confirm.classList.remove('save-confirm-show'), 2200);
      } else {
        confirm.classList.remove('save-confirm-show');
      }
    }
  }

  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-save-book]');
    if (!btn || btn.disabled) return;

    const id = btn.dataset.saveBook;
    const wasSaved = btn.classList.contains('is-saved');
    btn.disabled = true;

    try {
      const res = await fetch(`/books/${id}/save`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
      });

      if (res.status === 401 || res.status === 419) {
        showToast('Sign in to save books to your library.', 'fa-solid fa-circle-info');
        return;
      }
      if (!res.ok) throw new Error(`Server returned ${res.status}`);

      const data = await res.json();
      paintSaveButton(btn, data.saved);

      const onLibraryPage = document.body.dataset.page === 'library.index';
      showToast(
        data.saved ? 'Added to your library.' : 'Removed from your library.',
        data.saved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark',
        // Only offer the "View library" link when it's actually useful —
        // not when the person saving is already standing on that page.
        (data.saved && libraryUrl && !onLibraryPage)
          ? { text: 'View library', url: libraryUrl }
          : null
      );

      // On the My Library page itself, an unsaved book no longer
      // belongs in the grid — fade the whole card out rather than
      // leaving a "saved-looking" page with an unsaved book sitting
      // in it.
      if (!data.saved && onLibraryPage) {
        const card = btn.closest('[data-book-card]');
        if (card) {
          if (prefersReducedMotion) {
            card.remove();
          } else {
            card.classList.add('card-leaving');
            card.addEventListener('transitionend', () => {
              card.remove();
              if (!document.querySelector('[data-book-card]')) location.reload();
            }, { once: true });
          }
        }
      }
    } catch (err) {
      console.error('Save toggle failed:', err);
      showToast("Couldn't reach the server — try again.", 'fa-solid fa-triangle-exclamation');
      paintSaveButton(btn, wasSaved); // restore whatever it was
    } finally {
      btn.disabled = false;
    }
  });

  // ---------------------------------------------------------------
  // Reviews (unchanged behaviour, kept as-is)
  // ---------------------------------------------------------------
  const form = document.querySelector('#review-form');
  if (form) {
    const buttons = [...form.querySelectorAll('[data-score]')], hidden = form.querySelector('[name=rating]');
    const paint = n => buttons.forEach(b => b.classList.toggle('active', +b.dataset.score <= n));
    paint(5);
    buttons.forEach(b => b.addEventListener('click', () => { hidden.value = b.dataset.score; paint(+b.dataset.score); }));
    form.addEventListener('submit', async e => {
      e.preventDefault();
      const r = await fetch(form.dataset.reviewUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ rating: +hidden.value, review: form.querySelector('[name=review]').value })
      });
      if (r.ok) { form.reset(); hidden.value = 5; paint(5); loadReviews(); showToast('Thanks for your review.'); }
    });
  }

  // ---------------------------------------------------------------
  // "Suggest a book" form on the browse/search page
  // ---------------------------------------------------------------
  const requestForm = document.querySelector('#book-request-form');
  if (requestForm) {
    const fileInput = requestForm.querySelector('#req-cover');
    const fileNameEl = requestForm.querySelector('[data-file-name]');
    if (fileInput && fileNameEl) {
      fileInput.addEventListener('change', () => {
        fileNameEl.textContent = fileInput.files[0]?.name || 'No file chosen';
      });
    }

    requestForm.addEventListener('submit', async e => {
      e.preventDefault();
      const btn = requestForm.querySelector('button[type=submit]');
      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Sending…';

      try {
        const res = await fetch(requestForm.dataset.requestUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
          body: new FormData(requestForm)
        });

        if (res.ok) {
          requestForm.reset();
          if (fileNameEl) fileNameEl.textContent = 'No file chosen';
          showToast("Thanks — we'll take a look.", 'fa-solid fa-paper-plane');
        } else if (res.status === 422) {
          const data = await res.json().catch(() => null);
          const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
          showToast(firstError || 'Please check the form and try again.', 'fa-solid fa-triangle-exclamation');
        } else {
          showToast("Couldn't send that — try again.", 'fa-solid fa-triangle-exclamation');
        }
      } catch (err) {
        showToast("Couldn't reach the server — try again.", 'fa-solid fa-triangle-exclamation');
      } finally {
        btn.disabled = false;
        btn.textContent = originalText;
      }
    });
  }

  async function loadReviews() {
    if (!window.LIBTUNE_REVIEWS_URL) return;
    const box = document.querySelector('#reviews');
    if (!box) return;
    try {
      const r = await fetch(window.LIBTUNE_REVIEWS_URL);
      const d = await r.json();
      box.innerHTML = d.reviews.length
        ? d.reviews.map(x => `<article class="review"><div class="review-top"><strong>${escapeHtml(x.user?.username || 'Reader')}</strong><span class="stars">${'★'.repeat(x.score)}${'☆'.repeat(5 - x.score)}</span></div>${x.review ? `<p>${escapeHtml(x.review)}</p>` : ''}</article>`).join('')
        : '<p class="muted">Be the first reader to leave a thought.</p>';
    } catch (e) {
      box.innerHTML = '<p class="muted">Reviews are temporarily unavailable.</p>';
    }
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[c]));
  }
  loadReviews();

  document.addEventListener('keydown', e => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      document.querySelector('.global-search input')?.focus();
    }
  });

  // ---------------------------------------------------------------
  // Scroll-reveal — book grids and section headers fade/slide in as
  // they enter the viewport, staggered slightly per card so a whole
  // shelf doesn't just snap into place at once. Skipped entirely for
  // anyone who's asked their OS for reduced motion.
  // ---------------------------------------------------------------
  if (!prefersReducedMotion && 'IntersectionObserver' in window) {
    const revealTargets = document.querySelectorAll(
      '.book-grid > *, .section-head, .empty-state, .suggestions-head, .suggestion'
    );

    revealTargets.forEach((el, i) => {
      el.classList.add('reveal');
      el.style.setProperty('--reveal-delay', `${Math.min(i % 10, 10) * 45}ms`);
    });

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal-in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    revealTargets.forEach(el => observer.observe(el));
  }
});

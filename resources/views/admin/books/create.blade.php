@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-lg-9 col-xl-8 mx-auto">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Add New Book</h4>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                <div class="border rounded p-3 mb-4 bg-light-subtle">
                    <label for="ol-query" class="form-label mb-1">
                        Pre-fill from Open Library <span class="text-muted fs-12">(optional)</span>
                    </label>
                    <p class="text-muted fs-12 mb-2">
                        Search for a book, pick a result, and the fields below are filled in for you.
                        Nothing is saved until you press <strong>Add Book</strong>.
                    </p>
                    <div class="input-group">
                        <input type="text"
                               id="ol-query"
                               class="form-control"
                               placeholder="Search by title or author, e.g. Dune">
                        <button type="button" class="btn btn-outline-primary" id="ol-search-btn">
                            Search
                        </button>
                    </div>
                    <div id="ol-status" class="small mt-2"></div>
                    <div id="ol-results" class="list-group mt-2"></div>
                </div>

                <form action="{{ route('admin.books.store') }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf

                    <input type="hidden" name="cover_image_url" id="cover_image_url" value="{{ old('cover_image_url') }}">
                    <input type="hidden" name="open_library_key" id="open_library_key" value="{{ old('open_library_key') }}">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Book Title</label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}"
                                       placeholder="Enter book title"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="published_year" class="form-label">Published Year</label>
                                <input type="number"
                                       name="published_year"
                                       id="published_year"
                                       class="form-control @error('published_year') is-invalid @enderror"
                                       value="{{ old('published_year') }}"
                                       placeholder="e.g. 2024">
                                @error('published_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="authors" class="form-label">Author(s)</label>
                                <select name="authors[]"
                                        id="authors"
                                        class="form-select @error('authors') is-invalid @enderror"
                                        multiple
                                        required>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->auth_id }}"
                                            {{ collect(old('authors'))->contains($author->auth_id) ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple.</small>
                                @error('authors')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categories" class="form-label">Categories</label>
                                <select name="categories[]"
                                        id="categories"
                                        class="form-select @error('categories') is-invalid @enderror"
                                        multiple
                                        required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->cate_id }}"
                                            {{ collect(old('categories'))->contains($category->cate_id) ? 'selected' : '' }}>
                                            {{ $category->cate_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple.</small>
                                @error('categories')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="copyright_status" class="form-label">Copyright Status</label>
                                <select name="copyright_status" id="copyright_status" class="form-select">
                                    <option value="copyrighted" {{ old('copyright_status', 'copyrighted') == 'copyrighted' ? 'selected' : '' }}>
                                        Copyrighted
                                    </option>
                                    <option value="public_domain" {{ old('copyright_status') == 'public_domain' ? 'selected' : '' }}>
                                        Public Domain
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">Cover Image</label>
                                <div class="input-group @error('cover_image') is-invalid @enderror">
                                    <label class="input-group-text" for="cover_image">Upload</label>
                                    <input type="file"
                                           name="cover_image"
                                           id="cover_image"
                                           accept="image/*"
                                           class="form-control @error('cover_image') is-invalid @enderror">
                                </div>
                                @error('cover_image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div id="ol-cover-preview" class="mt-2 d-none">
                                    <img src="" alt="Cover preview" style="height:90px" class="rounded border">
                                    <div class="small text-muted mt-1">
                                        Cover from Open Library.
                                        <a href="#" id="ol-cover-clear">Remove</a>
                                        — or upload a file above to use that instead.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="reading_url" class="form-label">
                                    Reading URL <span class="text-muted fs-12">(optional external link)</span>
                                </label>
                                <input type="url"
                                       name="reading_url"
                                       id="reading_url"
                                       class="form-control @error('reading_url') is-invalid @enderror"
                                       value="{{ old('reading_url') }}"
                                       placeholder="https://example.com/read">
                                @error('reading_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description"
                                          id="description"
                                          class="form-control"
                                          rows="5"
                                          placeholder="Enter a short description">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       name="is_archived" id="is_archived" value="1"
                                       {{ old('is_archived') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_archived">
                                    Archive this book
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-light px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Add Book
                            </button>
                        </div>
                    </div>

                </form>

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    const lookupUrl  = @json(route('admin.books.lookup'));
    const queryInput = document.getElementById('ol-query');
    const searchBtn  = document.getElementById('ol-search-btn');
    const statusBox  = document.getElementById('ol-results');
    const statusMsg  = document.getElementById('ol-status');

    const titleEl    = document.getElementById('title');
    const yearEl     = document.getElementById('published_year');
    const descEl     = document.getElementById('description');
    const authorsEl  = document.getElementById('authors');
    const catsEl     = document.getElementById('categories');
    const coverUrlEl = document.getElementById('cover_image_url');
    const olKeyEl    = document.getElementById('open_library_key');
    const previewBox = document.getElementById('ol-cover-preview');
    const copyrightEl = document.getElementById('copyright_status');
    const readingUrlEl = document.getElementById('reading_url');

    function setStatus(text, cls) {
        statusMsg.className = 'small mt-2 ' + (cls || 'text-muted');
        statusMsg.textContent = text || '';
    }

    // Select an <option> whose text matches the given name (case-insensitive).
    // Returns true if a match was found, so we can warn about missing ones.
    function selectByName(selectEl, name) {
        if (!selectEl || !name) return false;
        const target = name.trim().toLowerCase();
        for (const opt of selectEl.options) {
            if (opt.text.trim().toLowerCase() === target) {
                opt.selected = true;
                return true;
            }
        }
        return false;
    }

    let searchGeneration = 0;

    async function runSearch() {
        const q = queryInput.value.trim();
        if (!q) return;

        // Guard against overlapping requests (e.g. the page auto-running a
        // search from URL params while the admin also clicks Search): only
        // the most recently started request is allowed to touch the UI.
        const myGeneration = ++searchGeneration;

        statusBox.innerHTML = '';
        setStatus('Searching Open Library…');
        searchBtn.disabled = true;

        try {
            // Open Library can be slow on a cold request — give it real time
            // before giving up, and let the request be aborted cleanly if
            // it does run out the clock.
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000);

            const res = await fetch(`${lookupUrl}?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json' },
                signal: controller.signal
            });
            clearTimeout(timeoutId);

            if (myGeneration !== searchGeneration) return; // a newer search superseded this one

            if (res.status === 401 || res.status === 419) {
                setStatus('Your admin session expired — refresh the page and log in again.', 'text-danger');
                return;
            }
            if (!res.ok) {
                console.error('Open Library lookup: server returned', res.status, await res.text());
                setStatus(`Lookup failed (server returned ${res.status}). You can still fill the form in manually.`, 'text-danger');
                return;
            }

            const data = await res.json();
            if (myGeneration !== searchGeneration) return;

            const results = data.results || [];

            if (!results.length) {
                setStatus('No matches found. You can still fill the form in manually.', 'text-muted');
                return;
            }

            setStatus(`${results.length} result(s) — click one to fill the form.`);

            results.forEach(function (r) {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action d-flex align-items-center gap-2';
                item.dataset.key = r.key;
                item.innerHTML = `
                    ${r.cover ? `<img src="${r.cover}" style="height:48px" class="rounded">` : ''}
                    <span class="text-start">
                        <strong>${r.title}</strong><br>
                        <span class="text-muted small">
                            ${(r.authors || []).join(', ') || 'Unknown author'}${r.year ? ' · ' + r.year : ''}
                        </span>
                        ${r.already_added ? '<br><span class="badge bg-warning-subtle text-warning">Already in catalogue</span>' : ''}
                        ${r.is_public_domain ? '<br><span class="badge bg-success-subtle text-success">Public domain · reading link available</span>' : ''}
                    </span>`;
                item.addEventListener('click', () => fillForm(r));
                statusBox.appendChild(item);
            });
        } catch (e) {
            if (myGeneration !== searchGeneration) return; // superseded — ignore its error too

            console.error('Open Library lookup failed:', e);

            const timedOut = e.name === 'AbortError';
            statusMsg.className = 'small mt-2 text-danger';
            statusMsg.innerHTML = timedOut
                ? 'Open Library took too long to respond. This is usually a one-off, not a real outage — '
                : 'Could not reach the lookup service. Check your connection, or — ';
            const retryLink = document.createElement('a');
            retryLink.href = '#';
            retryLink.textContent = 'try again';
            retryLink.addEventListener('click', function (ev) {
                ev.preventDefault();
                runSearch();
            });
            statusMsg.appendChild(retryLink);
            statusMsg.append(', or fill the form in manually.');
        } finally {
            if (myGeneration === searchGeneration) {
                searchBtn.disabled = false;
            }
        }
    }

    async function fillForm(r) {
        titleEl.value = r.title || '';
        yearEl.value  = r.year || '';
        olKeyEl.value = r.key || '';

        if (r.cover) {
            coverUrlEl.value = r.cover;
            previewBox.querySelector('img').src = r.cover;
            previewBox.classList.remove('d-none');
        }

        // Only touch these when Open Library actually gives us a real,
        // working reading link — never guess "public domain" from
        // publish year alone, and never blank out a value the admin
        // might have already typed if this result turns out not to
        // qualify.
        if (r.is_public_domain && r.reading_url) {
            copyrightEl.value = 'public_domain';
            readingUrlEl.value = r.reading_url;
        }

        const missing = [];

        const authorName = (r.authors || [])[0];
        if (authorName) {
            Array.from(authorsEl.options).forEach(o => o.selected = false);
            if (!selectByName(authorsEl, authorName)) {
                missing.push(`author “${authorName}”`);
            }
        }

        setStatus('Fetching description…');

        try {
            const res = await fetch(`${lookupUrl}?key=${encodeURIComponent(r.key)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const details = await res.json();

            if (details.description) {
                descEl.value = details.description;
            }

            if (details.suggested_category) {
                Array.from(catsEl.options).forEach(o => o.selected = false);
                if (!selectByName(catsEl, details.suggested_category)) {
                    missing.push(`category “${details.suggested_category}”`);
                }
            }
        } catch (e) {
            // description is optional — the rest of the form is still filled
        }

        const pdNote = (r.is_public_domain && r.reading_url)
            ? ' Marked public domain with a reading link filled in — double-check it opens correctly before saving.'
            : '';

        if (missing.length) {
            setStatus(
                `Filled in. Note: ${missing.join(' and ')} doesn't exist yet — create it first, or pick another from the lists below.${pdNote}`,
                'text-warning'
            );
        } else {
            setStatus(`Filled in. Review the fields, then press Add Book.${pdNote}`, 'text-success');
        }

        statusBox.innerHTML = '';
        titleEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    searchBtn.addEventListener('click', runSearch);
    queryInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();   // don't submit the book form
            runSearch();
        }
    });

    document.getElementById('ol-cover-clear').addEventListener('click', function (e) {
        e.preventDefault();
        coverUrlEl.value = '';
        previewBox.classList.add('d-none');
    });

    // Arriving from an "Add to library" suggestion on the public search
    // page: pre-fill the lookup box and run it straight away.
    const params = new URLSearchParams(window.location.search);
    const presetTitle = params.get('title');
    const presetKey = params.get('ol');

    if (presetTitle) {
        queryInput.value = presetTitle;
        runSearch().then(function () {
            if (!presetKey) return;
            // auto-pick the exact work that was clicked, if it came back
            const match = Array.from(statusBox.children).find(
                el => el.dataset.key === presetKey
            );
            if (match) match.click();
        });
    }
})();
</script>
@endsection

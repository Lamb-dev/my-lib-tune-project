@php $member ??= null; @endphp

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $member->name ?? '') }}"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <input type="text"
                   name="role"
                   id="role"
                   class="form-control @error('role') is-invalid @enderror"
                   value="{{ old('role', $member->role ?? '') }}"
                   placeholder="e.g. Founder, Front-End Developer"
                   required>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <div class="input-group @error('photo') is-invalid @enderror">
                <label class="input-group-text" for="photo">Upload</label>
                <input type="file"
                       name="photo"
                       id="photo"
                       accept="image/*"
                       class="form-control @error('photo') is-invalid @enderror">
            </div>
            <div class="form-text">Square photo works best. Leave blank to keep the current one, or show initials if none has been uploaded yet.</div>
            @error('photo')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            @if(($member->photo_path ?? null))
                <div class="form-check mt-2">
                    <input type="checkbox"
                           name="remove_photo"
                           id="remove_photo"
                           value="1"
                           class="form-check-input">
                    <label for="remove_photo" class="form-check-label text-danger">
                        Remove current photo
                    </label>
                </div>
                <div class="form-text">Falls back to initials. Ignored if you upload a new photo above.</div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="sort_order" class="form-label">Display Order</label>
            <input type="number"
                   name="sort_order"
                   id="sort_order"
                   min="0"
                   class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $member->sort_order ?? 0) }}">
            <div class="form-text">Lower numbers show first.</div>
            @error('sort_order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea name="bio"
                      id="bio"
                      class="form-control @error('bio') is-invalid @enderror"
                      rows="4"
                      maxlength="1000">{{ old('bio', $member->bio ?? '') }}</textarea>
            <div class="form-text">Shown on the card — keep it to a sentence or two.</div>
            @error('bio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-muted">(optional)</span></label>
            <input type="email"
                   name="email"
                   id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $member->email ?? '') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="linkedin_url" class="form-label">LinkedIn URL <span class="text-muted">(optional)</span></label>
            <input type="url"
                   name="linkedin_url"
                   id="linkedin_url"
                   class="form-control @error('linkedin_url') is-invalid @enderror"
                   value="{{ old('linkedin_url', $member->linkedin_url ?? '') }}"
                   placeholder="https://linkedin.com/in/...">
            @error('linkedin_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="twitter_url" class="form-label">Twitter / X URL <span class="text-muted">(optional)</span></label>
            <input type="url"
                   name="twitter_url"
                   id="twitter_url"
                   class="form-control @error('twitter_url') is-invalid @enderror"
                   value="{{ old('twitter_url', $member->twitter_url ?? '') }}"
                   placeholder="https://x.com/...">
            @error('twitter_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="website_url" class="form-label">Website URL <span class="text-muted">(optional)</span></label>
            <input type="url"
                   name="website_url"
                   id="website_url"
                   class="form-control @error('website_url') is-invalid @enderror"
                   value="{{ old('website_url', $member->website_url ?? '') }}">
            @error('website_url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<!--end row-->

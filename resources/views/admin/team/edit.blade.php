@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-lg-8 col-xl-6 mx-auto">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Edit Team Member</h4>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                @if($teamMember->photo_path)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $teamMember->photo_path) }}"
                             alt="{{ $teamMember->name }}"
                             class="rounded-circle"
                             width="70" height="70"
                             style="object-fit: cover;">
                    </div>
                @endif

                <form action="{{ route('admin.team.update', $teamMember->team_member_id) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')
                    @include('admin.team.form', ['member' => $teamMember])

                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <a href="{{ route('admin.team.index') }}" class="btn btn-light px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Save Changes
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

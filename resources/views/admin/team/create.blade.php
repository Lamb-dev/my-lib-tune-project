@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-lg-8 col-xl-6 mx-auto">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Add Team Member</h4>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                <form action="{{ route('admin.team.store') }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @include('admin.team.form')

                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <a href="{{ route('admin.team.index') }}" class="btn btn-light px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Add Team Member
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

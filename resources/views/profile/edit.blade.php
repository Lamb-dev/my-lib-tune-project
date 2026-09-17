@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">Edit Profile</h2>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input id="name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input id="email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea id="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" name="bio">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Profile Picture -->
                    <div class="md:col-span-2">
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                        <div class="flex items-center space-x-4">
                            @if($user->profile_picture_path)
                                <img src="{{ Storage::disk('public')->url($user->profile_picture_path) }}" alt="Profile Picture" class="w-20 h-20 object-cover rounded-full border-2 border-gray-200">
                            @else
                                <img src="https://via.placeholder.com/150" alt="Placeholder" class="w-20 h-20 object-cover rounded-full border-2 border-gray-200">
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-700">Current Picture</p>
                                <input id="profile_picture" type="file" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" name="profile_picture" accept="image/*">
                                @error('profile_picture')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Book Catalog -->
        <div class="space-y-8">
            <!-- Saved Books -->
            <div>
                <h2 class="text-xl font-bold mb-4">Saved Books</h2>
                @if($savedBooks->isEmpty())
                    <p class="text-gray-500">You haven't saved any books yet.</p>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($savedBooks as $book)
                            <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-48 object-cover rounded-md mb-3">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-md mb-3">
                                        <span class="text-gray-500">No Cover</span>
                                    </div>
                                @endif
                                <h3 class="text-lg font-semibold mb-1 line-clamp-2">{{ $book->title }}</h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    @foreach($book->authors as $author)
                                        {{ $author->name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                @if($book->category)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $book->category->cate_name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Want to Read -->
            <div>
                <h2 class="text-xl font-bold mb-4">Want to Read</h2>
                @if($wantToRead->isEmpty())
                    <p class="text-gray-500">You haven't marked any books as want to read.</p>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($wantToRead as $book)
                            <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-48 object-cover rounded-md mb-3">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-md mb-3">
                                        <span class="text-gray-500">No Cover</span>
                                    </div>
                                @endif
                                <h3 class="text-lg font-semibold mb-1 line-clamp-2">{{ $book->title }}</h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    @foreach($book->authors as $author)
                                        {{ $author->name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                @if($book->category)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $book->category->cate_name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Currently Reading -->
            <div>
                <h2 class="text-xl font-bold mb-4">Currently Reading</h2>
                @if($currentlyReading->isEmpty())
                    <p class="text-gray-500">You aren't currently reading any books.</p>
                @else
                    <div class="gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($currentlyReading as $book)
                            <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-48 object-cover rounded-md mb-3">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-md mb-3">
                                        <span class="text-gray-500">No Cover</span>
                                    </div>
                                @endif
                                <h3 class="text-lg font-semibold mb-1 line-clamp-2">{{ $book->title }}</h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    @foreach($book->authors as $author)
                                        {{ $author->name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                @if($book->category)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $book->category->cate_name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
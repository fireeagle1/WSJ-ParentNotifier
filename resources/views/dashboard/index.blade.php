@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Message -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                Welcome, {{ Auth::user()->display_name }}!
            </h1>
            <p class="mt-1 text-gray-600">Manage your expedition updates from the dashboard.</p>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-brand-600">
                <div class="text-3xl font-bold text-brand-600">{{ $postCount }}</div>
                <div class="text-sm text-gray-500 mt-1">Total Posts</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-brand-400">
                <div class="text-3xl font-bold text-brand-600">{{ $parentCount }}</div>
                <div class="text-sm text-gray-500 mt-1">Active Parents</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-brand-300">
                <div class="text-3xl font-bold text-brand-600">{{ $leaderCount }}</div>
                <div class="text-sm text-gray-500 mt-1">Leaders</div>
            </div>
        </div>

        <!-- Quick Links -->
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Posts -->
            <a href="{{ route('posts.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow border-t-2 border-brand-600">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <div class="ml-4">
                        <div class="font-semibold text-gray-900">Posts</div>
                        <div class="text-sm text-gray-500">Create and manage updates</div>
                    </div>
                </div>
            </a>

            <!-- Parents -->
            <a href="{{ route('parents.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow border-t-2 border-brand-500">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div class="ml-4">
                        <div class="font-semibold text-gray-900">Parents</div>
                        <div class="text-sm text-gray-500">Manage parent access</div>
                    </div>
                </div>
            </a>

            <!-- Leaders (Admin Only) -->
            @if(Auth::user()->is_admin)
            <a href="{{ route('leaders.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow border-t-2 border-brand-400">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <div class="ml-4">
                        <div class="font-semibold text-gray-900">Leaders</div>
                        <div class="text-sm text-gray-500">Manage leader accounts</div>
                    </div>
                </div>
            </a>
            @endif

            <!-- Settings -->
            <a href="{{ route('leaders.settings') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow border-t-2 border-brand-300">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div class="ml-4">
                        <div class="font-semibold text-gray-900">Settings</div>
                        <div class="text-sm text-gray-500">Update your profile</div>
                    </div>
                </div>
            </a>

            <!-- Home Contacts -->
            <a href="{{ route('contacts.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow border-t-2 border-brand-300">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <div class="ml-4">
                        <div class="font-semibold text-gray-900">Home Contacts</div>
                        <div class="text-sm text-gray-500">Manage contact schedule</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

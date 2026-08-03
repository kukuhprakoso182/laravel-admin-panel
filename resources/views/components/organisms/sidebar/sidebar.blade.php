@props([
    'brand' => config('app.name'),
    'author' => config('app.author', "Kukuh Prakoso"),
])

<aside {{ $attributes->merge(['class' => 'w-full h-full bg-gray-100 rounded-2xl flex flex-col overflow-hidden']) }}>

    <x-organisms.sidebar.sidebar-brand :brand="$brand"/>

    <x-organisms.sidebar.sidebar-menu/>

    <x-organisms.sidebar.sidebar-footer :brand="$brand" :author="$author"/>
</aside>

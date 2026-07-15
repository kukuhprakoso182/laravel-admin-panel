{{--
    Pengganti inline <svg> Solar icon di template asli.
    Pakai: <x-ui.icon name="dashboard-line" />
    Daftar nama icon: https://remixicon.com/ (tanpa prefix "ri-", cukup "dashboard-line")
--}}
@props(['name', 'class' => ''])

<i class="ri-{{ $name }} {{ $class }}" aria-hidden="true"></i>

<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class SessionHelper
{
    /**
     * Simpan data ke session dalam bentuk terenkripsi.
     */
    public static function put(string $key, mixed $value): void
    {
        Session::put($key, Crypt::encrypt($value));
    }

    /**
     * Ambil data dari session, otomatis di-decrypt.
     * Jika data korup/invalid, otomatis dihapus dan mengembalikan $default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Session::get($key);

        if ($value === null) {
            return $default;
        }

        try {
            return Crypt::decrypt($value);
        } catch (DecryptException) {
            Session::forget($key);
            return $default;
        }
    }

    public static function has(string $key): bool
    {
        return Session::has($key);
    }

    public static function forget(string $key): void
    {
        Session::forget($key);
    }

    /**
     * Simpan data terenkripsi hanya untuk 1 request berikutnya (flash).
     */
    public static function flash(string $key, mixed $value): void
    {
        Session::flash($key, Crypt::encrypt($value));
    }

    /**
     * Simpan seluruh array data sekaligus (masing-masing key dienkripsi terpisah).
     */
    public static function putMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::put($key, $value);
        }
    }
}

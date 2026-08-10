<?php

namespace App\Services\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

trait HandlesForeignKeyViolation
{
    protected function isForeignKeyViolation(QueryException $e): bool
    {
        // errorInfo[0] = SQLSTATE, errorInfo[1] = driver-specific error code
        // 1451 = MySQL: Cannot delete or update a parent row: a foreign key constraint fails
        return ($e->errorInfo[1] ?? null) === 1451
            || (string) $e->getCode() === '23000';
    }

    /**
     * Jalankan operasi delete (atau operasi apa pun yang bisa kena FK constraint),
     * dan otomatis ubah QueryException akibat FK violation jadi ValidationException
     * yang rapi. Exception lain (bukan FK) tetap dilempar apa adanya.
     *
     * @param  callable(): bool  $callback   Operasi yang mau dijalankan, mis. fn () => $this->repo->delete($id)
     * @param  string            $field      Nama field untuk key pesan validasi
     * @param  string            $message    Pesan yang ditampilkan ke user
     */
    protected function deleteOrFailOnForeignKey(callable $callback, string $field, string $message = "Data ini tidak bisa dihapus karena masih terpakai di data lain."): bool
    {
        try {
            return (bool) $callback();
        } catch (QueryException $e) {
            if ($this->isForeignKeyViolation($e)) {
                throw ValidationException::withMessages([$field => $message]);
            }

            throw $e;
        }
    }
}

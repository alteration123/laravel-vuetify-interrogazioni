<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Log;

/*
 * EXAMPLE::PROGETTODATABASE_SEEDER
 */
class DatabaseSeeder extends Seeder
{
    const N_STUDENT = 100;

    /**
     * @var StudentSeeder
     */
    protected $studentSeeder;

    /**
     * DatabaseSeeder constructor.
     * @param StudentSeeder $studentSeeder
     */
    public function __construct(
        StudentSeeder $studentSeeder
    ) {
        $this->studentSeeder = $studentSeeder;
    }

    /**
     * @throws \Throwable
     */
    public function run(): void
    {
        $success = $this->studentSeeder->execute(self::N_STUDENT);
        Log::info("Generati $success studenti su " . self::N_STUDENT);
    }
}

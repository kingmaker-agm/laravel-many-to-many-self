<?php

namespace Kingmaker\Illuminate\Eloquent\Relations\Tests\Contracts;

interface DatabaseSchemaRefreshable
{
    /**
     * Refresh the Database Schema
     *
     * Existing Database Tables will be dropped, if they already exists.
     * Create the Database Tables.
     * @return void
     */
    public static function refreshDatabaseSchema(): void;
}

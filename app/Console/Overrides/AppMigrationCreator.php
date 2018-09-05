<?php

namespace App\Console\Overrides;


use Illuminate\Database\Migrations\MigrationCreator;

class AppMigrationCreator extends MigrationCreator
{
    public $table = null;
    public $create = null;

    public function getStub($table, $create)
    {
        return $this->files->get(__DIR__ . '/../Commands/stubs/psmigrationcreate.stub');
    }

    public function create($name, $path, $table = null, $create = false)
    {

        $this->ensureMigrationDoesntAlreadyExist($name);

        // First we will get the stub file for the migration, which serves as a type
        // of template for the migration. Once we have those we will populate the
        // various place-holders, save the file, and run the post create event.
        $stub = $this->getStub($table, $create);

        $this->files->put(
            $path = $this->getPath($name, $path),
            $this->psPopulateStub($name, $stub, $table)
        );

        // Next, we will fire any hooks that are supposed to fire after a migration is
        // created. Once that is done we'll be ready to return the full path to the
        // migration file so it can be used however it's needed by the developer.
        $this->firePostCreateHooks($table);

        return $path;
    }

    public function psPopulateStub($name, $stub, $table)
    {
        $stub = str_replace('DummyClass', $this->getClassName($name), $stub);

        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
        if (! is_null($table)) {
            $stub = str_replace('DummyTable', $table, $stub);
        }

        return $stub;
    }
}
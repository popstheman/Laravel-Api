<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class PsApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:psapi {--m|model= : The Model name for the seeder} {--a|api= : The api route name for the route files} {--c|columns= : The column type and name for the model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createModel();
        $this->createMigration();
        $this->createObserver();
        $this->createSeeder();
        $this->createFeatureTest();
        $this->createController();
        $this->createResource();
        $this->createRequest();
        $this->composer->dumpAutoloads();
    }

    protected function getModelName()
    {
        return $this->option('model');
    }

    protected function getApiName()
    {
        return $this->option('api');
    }

    protected function getColumns()
    {
        return $this->option('columns');
    }

    protected function createModel()
    {
        $this->info("Creating Model for " . $this->getModelName() . " .");
        $this->call('make:model', ['name' => $this->getModelName()]
        );
    }

    protected function createMigration()
    {
        $this->info("Creating Migration for the model " . $this->getModelName() . " .");
        $this->call('make:psmigration', ['name' => 'create_' . $this->getApiName() . '_table', '-c' => $this->getColumns()]);
    }

    protected function createObserver()
    {
        $name = $this->getModelName() . "Observer";
        $this->call('make:observer', ['name' => $name, '-m' => $this->getModelName()]);
    }

    protected function createSeeder()
    {
        $name = $this->getModelName() . "Seeder";
        $data = $this->getModelName() . "Data";
        $this->call('make:psseeder', ['name' => $name, '-m' => $this->getModelName(), '-d' => $data, '-c' => $this->getColumns()]);
    }

    protected function createFeatureTest()
    {
        $name = $this->getModelName() . "FeatureTest";
        $this->call('make:pstest', ['name' => $name, '-m' => $this->getModelName(), '-a' => $this->getApiName(), '-c' => $this->getColumns()]);

    }

    protected function createController()
    {
        $name = $this->getModelName() . "Controller";
        $this->call('make:controller', ['name' => $name, '-m' => $this->getModelName(), '--api' => true]);
    }

    protected function createResource()
    {
        $name = $this->getModelName() . "Resource";
        $this->call('make:resource', ['name' => $name]);

    }

    protected function createRequest()
    {
        $name = "Store" . $this->getModelName() . "Request";
        $this->call('make:request', ['name' => $name]);

    }


}

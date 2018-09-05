<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;

class PsSeederMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:psseeder {name} {--m|model= : The Model name for the seeder} {--d|data= : The Data file name for the seeder} {--c|columns= : The column names which you want to populate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new PS seeder class';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct($files);

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $model = $this->getModelName();
        $data = $this->getDataName();

        $pathData = $this->getDataPath($data);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');
            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);
        $this->makeDirectory($pathData);

        $this->files->put($pathData, $this->columnNamesForDataPopulation());

        if($model && $data)
            $this->files->put($path, $this->PsbuildClass($name,$model,$data));
        else
            $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
//        $this->info('Running composer dump-autoload command ...');
//        $this->composer->dumpAutoloads();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/psseederplain.stub';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getCustomStub()
    {
        return __DIR__.'/stubs/psseeder.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel->databasePath().'/seeds/'.$name.'.php';
    }

    protected function getDataPath($name)
    {
        return $this->laravel->databasePath(). '/data/'. $name. '.json';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return $name;
    }

    protected function getModelName()
    {
        return $this->option('model');
    }

    protected function getDataName()
    {
        return $this->option('data');
    }

    protected function PsbuildClass($name, $model, $data)
    {
        $stub = $this->files->get($this->getCustomStub());

        $stub = $this->replaceModel($stub,$model);
        $stub = $this->replaceData($stub,$data);

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceModel($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('DummyModel', $class, $stub);
    }

    protected function replaceData($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('DummyData', $class, $stub);
    }

    public function columnNamesForDataPopulation()
    {
        $columns = explode(':',$this->option('columns'));
        $dataColumns = [];
        foreach($columns as $column)
        {
            $column = explode(".", $column);
            $dataColumns []= [$column[1] => ""];
        }
        $json = json_encode(array_collapse($dataColumns),true);
        return "[".$json."]";
    }
}

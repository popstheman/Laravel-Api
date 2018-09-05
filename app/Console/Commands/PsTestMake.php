<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class PsTestMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pstest {name} {--m|model= : Model Name for the Test Class} {--a|api= : Api end point}, {--c|columns : Columns For DataObject and DataModelObject}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new PS test class';

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
    protected $type = 'Test';

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  \Illuminate\Support\Composer $composer
     * @return void
     */
    public function __construct(FileSystem $files, Composer $composer)
    {
        parent::__construct($files);

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $model = $this->getModelName();
        $api = $this->getApiName();

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->getNameInput())
        ) {
            $this->error($this->type . ' already exists!');
            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        if ($model && $api)
            $this->files->put($path, $this->PsbuildClass($name, $model, $api));
        else
            $this->files->put($path, $this->buildClass($name));

        $this->info($this->type . ' created successfully.');
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
        return __DIR__ . '/stubs/psfeaturetestplain.stub';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getCustomStub()
    {
        return __DIR__ . '/stubs/psfeaturetest.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        return base_path('tests') . str_replace('\\', '/', $name) . '.php';
    }

    protected function getModelName()
    {
        return $this->option('model');
    }

    protected function getApiName()
    {
        return $this->option('api');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Feature';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return 'Tests';
    }

    protected function PsbuildClass($name, $model, $api)
    {
        $columns = $this->explodeColumns();
        $stub = $this->files->get($this->getCustomStub());
        $stub = $this->replaceModel($stub, $model);
        $stub = $this->replaceApi($stub, $api);
        $stub = $this->replaceDataObject($stub, $columns);
        $stub = $this->replaceDataModelObject($stub, $columns);
        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceModel($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('DummyModel', $class, $stub);
    }

    protected function replaceApi($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('DummyApi', $class, $stub);
    }

    protected function replaceDataObject($stub, $columns){
        $dataObject = $this->columnNamesForDataObjects($columns);
        return str_replace('dummyDataObject', $dataObject, $stub);
    }

    protected function replaceDataModelObject($stub, $columns){
        $dataObject = $this->columnNamesForDataModelObjects($columns);
        return str_replace('dummyModelObject', $dataObject, $stub);
    }

    public function explodeColumns()
    {
        $columns = explode(':',$this->option('columns'));
        return $columns;
    }
    public function columnNamesForDataObjects($columns)
    {
        $dataColumns = "";
        foreach($columns as $column)
        {
            $column = explode(".", $column);
            $dataColumns  .= ",'".$column[1]."'";
        }
        return $dataColumns;
    }

    public function columnNamesForDataModelObjects($columns)
    {
        $dataColumns = "";
        $i = 0;
        foreach($columns as $column)
        {
            $column = explode(".", $column);
            if ($i + 1 < sizeof($columns))
                $dataColumns .= "'".$column[1]."' => '', " ;
            else
                $dataColumns .= "'".$column[1]."' => ''" ;
            $i++;
        }
        return $dataColumns;
    }
}

<?php

namespace Terranet\Administrator\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Traits\SessionGuardHelper;

class AdministratorCreateCommand extends Command
{
    use SessionGuardHelper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrator:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator user.';

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * Create a new command instance.
     *
     * @param Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        parent::__construct();

        $this->hasher = $hasher;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Name', 'Administrator');
        $email = $this->ask('Email', 'admin@example.com');
        $password = $this->ask('Password', 'secret');

        $data = $this->prepareData($name, $email, $password);

        $this->tryUpdatingRole($this->createUserInstance($data));
    }

    /**
     * Prepare administrator's data.
     *
     * @param $name
     * @param $email
     * @param $password
     * @return array
     */
    protected function prepareData($name, $email, $password)
    {
        $data = compact('name', 'email', 'password');

        if (app('scaffold.config')->get('manage_passwords')) {
            $data['password'] = $this->hasher->make($data['password']);
        }

        return $data;
    }

    /**
     * Try to update user's role to admin if column 'role' exists.
     *
     * @param $instance
     * @return bool
     */
    protected function tryUpdatingRole($instance)
    {
        try {
            return $instance->forceFill(['role' => 'admin'])->save();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Insert new user into database.
     *
     * @param $data
     * @return Model
     * @throws Exception
     */
    protected function createUserInstance($data)
    {
        $config = app('scaffold.config');

        if (! $model = $this->fetchModel($config)) {
            throw new Exception("Could not find a model to create user.");
        }

        try {
            $model::unguard();

            return (new $model)->create($this->dataWithId($data));
        } catch (Exception $e) {
            return (new $model)->create($data);
        }
    }

    /**
     * Merge Administrator's data with an ID.
     *
     * @param $data
     * @return array
     */
    protected function dataWithId($data)
    {
        return array_merge(['id' => 1], $data);
    }
}

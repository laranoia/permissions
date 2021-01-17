<?php

namespace Laranoia\Permissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Laranoia\Permissions\Contracts\Ability;
use Laranoia\Permissions\Contracts\Role;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{
    protected $signature = 'permissions:import';

    protected $description = 'Sync all abilities and roles with the database';

    protected $abilities = [];

    protected $roles = [];

    public function handle()
    {
        $this->roles = $this->loadRoles();
        $this->abilities = $this->loadAbilities();

        $this->prepare();
        $this->syncWithDatabase();
    }

    /** Load the list of abilities
     *
     * Override method to import from file
     *
     * @return array
     */
    protected function loadAbilities(): array
    {
        return config('permissions.abilities');
    }

    /**
     * @return array
     */
    protected function loadRoles(): array
    {
        return config('permissions.roles');
    }

    protected function prepare()
    {
        foreach ($this->abilities as $ability => &$details) {
            foreach ($details['roles'] as $key => $role) {
                if (!array_key_exists($role, $this->roles)) {
                    $this->warn('Ability ' . $ability . ': The role ' . $role . ' is not defined.');
                    unset($details['roles'][$key]);
                } else {
                    $this->info('Adding ability ' . $ability . ' to role ' . $role, OutputInterface::VERBOSITY_DEBUG);
                    if (!array_key_exists('abilities', $this->roles[$role])) {
                        $this->roles[$role]['abilities'] = [];
                    }
                    $this->roles[$role]['abilities'][] = $ability;
                }
            }
            if (empty($details['roles'])) {
                $this->warn('Ability ' . $ability . ' has no roles and will be ignored.');
                unset($this->abilities[$ability]);
                continue;
            }
            $details['name'] = $ability;
            if (!array_key_exists('display_name', $details) || empty($details['display_name'])) {
                $this->info('Ability ' . $ability . ' has no display name. Using name as display name');
                $details['display_name'] = $details['name'];
            }
        }

        foreach ($this->roles as $role => &$details) {
            if (!array_key_exists('abilities', $details) || count($details['abilities']) === 0) {
                $this->warn('Role ' . $role . ' has no abilities and will be ignored.');
                unset($this->roles[$role]);
                continue;
            }
            $details['name'] = $role;
            if (!array_key_exists('display_name', $details) || empty($details['display_name'])) {
                $this->info('Role ' . $role . ' has no display name. Using name as display name');
                $details['display_name'] = $details['name'];
            }
        }
    }

    protected function syncWithDatabase()
    {
        // laravel currently has no way of getting the concrete class for the interface without instancing -> fallback
        // to config
        /** @var Role $roleModel */
        $roleModel = config('permissions.models.role');
        /** @var Model $abilityModel */
        $abilityModel = config('permissions.models.ability');

        $abilities = collect($this->abilities);
        $storedAbilities = $abilityModel::all()->keyBy('name');

        $abilitiesToDelete = $storedAbilities->diffKeys($abilities);
        $abilitiesToDelete->each(function ($ability) use ($abilityModel) {
            $this->info('Removing ability '.$ability->name);
            $ability->delete();
        });

        $abilities->each(function($ability) use ($abilityModel){
            $this->info('Adding/updating ability '.$ability['name']);
            $abilityModel::updateOrCreate(['name' => $ability['name']], $ability);
        });
        
        $roles = collect($this->roles);
        $storedRoles = $roleModel::all()->keyBy('name');

        $rolesToDelete = $storedRoles->diffKeys($roles);
        $rolesToDelete->each(function ($role) use ($roleModel) {
            $this->info('Removing role '.$role->name);
            $role->delete();
        });

        $roles->each(function($role) use ($roleModel, $abilityModel){
            $this->info('Adding/updating role '.$role['name']);
            $roleInstance = $roleModel::updateOrCreate(['name' => $role['name']], $role);
            $roleAbilities = $abilityModel::where('name', $role['abilities'])->get('id');
            $roleInstance->abilities()->sync($roleAbilities);
        });
    }
}

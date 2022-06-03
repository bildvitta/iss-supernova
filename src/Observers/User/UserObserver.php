<?php

namespace Bildvitta\IssSupernova\Observers\User;

use App\Models\User;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created($user)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        if(strpos($user->name, 'supernova') === false) {
            return;
        }

        $user->loadMissing(
            'company',
            'groups',
        );
        $data = $user->toArray();
        $data['sync_to'] = 'sys';
        $data['permissions'] = $user->getAllPermissions();

        //Supervisor
        $data['supervisor_uuid'] = $this->getUserUuidByPermission('supervisor.brokers.' . $user->uuid);

        //Gerente
        $data['manager_uuid'] = $data['supervisor_uuid'] ? $this->getUserUuidByPermission('manager.supervisors.' . $data['supervisor_uuid']) : null;
        if (!$data['manager_uuid']) {
            $data['manager_uuid'] = $this->getUserUuidByPermission('manager.supervisors.' . $user->uuid);
        }

        try {
            $issSupernova = new IssSupernova('no-token');
            $response = $issSupernova->users()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($user)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        if(strpos($user->name, 'supernova') === false) {
            return;
        }

        $user->loadMissing(
            'company',
            'groups',
        );
        $data = $user->toArray();
        $data['sync_to'] = 'sys';
        $data['permissions'] = $user->getAllPermissions();

        //Supervisor
        $data['supervisor_uuid'] = $this->getUserUuidByPermission('supervisor.brokers.' . $user->uuid);

        //Gerente
        $data['manager_uuid'] = $data['supervisor_uuid'] ? $this->getUserUuidByPermission('manager.supervisors.' . $data['supervisor_uuid']) : null;
        if (!$data['manager_uuid']) {
            $data['manager_uuid'] = $this->getUserUuidByPermission('manager.supervisors.' . $user->uuid);
        }

        try {
            $issSupernova = new IssSupernova('no-token');
            $response = $issSupernova->users()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($user)
    {
        //
    }

    protected function getUserUuidByPermission($permission, $projectSlug='modular') {
        $permission = is_array($permission) ? $permission : [$permission];
        $user = User::where(function ($query) use ($permission, $projectSlug) {
            $query->whereHas('groups', function ($query) use ($permission, $projectSlug) {
                $query->whereHas('permissions', function ($query) use ($permission, $projectSlug) {
                    $query->whereIn('name', $permission);

                    $query->whereHas('project', function ($query) use ($projectSlug) {
                        $query->where('slug', $projectSlug);
                    });
                });
            })->orWhereHas('roles', function ($query) use ($permission, $projectSlug) {
                $query->whereHas('permissions', function ($query) use ($permission, $projectSlug) {
                    $query->whereIn('name', $permission);

                    $query->whereHas('project', function ($query) use ($projectSlug) {
                        $query->where('slug', $projectSlug);
                    });
                });
            })->orWhereHas('permissions', function ($query) use ($permission, $projectSlug) {
                $query->whereIn('name', $permission);

                $query->whereHas('project', function ($query) use ($projectSlug) {
                    $query->where('slug', $projectSlug);
                });
            });
        })->first('uuid');
        return $user ? $user->uuid : null;
    }
}

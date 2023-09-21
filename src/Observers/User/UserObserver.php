<?php

namespace Bildvitta\IssSupernova\Observers\User;

use App\Models\User;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created($user)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $user->loadMissing(
            'company',
            'groups',
        );
        $data = $user->toArray();
        $data['sync_to'] = 'sys';
        $data['permissions'] = $user->getAllPermissions();

        if (!in_array($data['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        //Supervisor
        $data['supervisor_uuid'] = config('hub.model_user')::query()->whereHas('user_companies', function ($query) use ($user) {
            $query->where('company_id', $user->company_id);

            //Usuário que tenha cargo de Supervisor
            $query->whereIn('position_id', self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id'));

            $query->where(function ($query) use ($user) {
                //Que o user passado seja corretor abaixo desse supervisor
                $query->whereHas('children_positions', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getRealEstateBrokerPositionsFromCompany($user->company_id)->pluck('id'));
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    });
                });
                //OU que o user passado seja gerente acima desse supervisor
                $query->orWhereHas('parent_position', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getManagerPositionsFromCompany($user->company_id)->pluck('id'));
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    });
                });
            });
        })
        ->first(['uuid'])?->uuid;
    
        //Gerente
        $data['manager_uuid'] = config('hub.model_user')::query()->whereHas('user_companies', function ($query) use ($user) {
            $query->where('company_id', $user->company_id);
            
            //Usuário que tenha cargo de Gerente
            $query->whereIn('position_id', self::getManagerPositionsFromCompany($user->company_id)->pluck('id'));

            $query->where(function ($query) use ($user) {
                //Que o user passado seja supervisor abaixo desse gerente
                $query->whereHas('children_positions', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id'));
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    });
                });
                //OU que o user passado seja corretor abaixo de algum supervisor abaixo desse gerente
                $query->orWhereHas('children_positions', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id'));

                    $query->whereHas('children_positions', function ($query) use ($user) {
                        $query->whereIn('position_id', self::getRealEstateBrokerPositionsFromCompany($user->company_id)->pluck('id'));
                        $query->whereHas('user', function ($query) use ($user) {
                            $query->where('id', $user->id);
                        });
                    });
                });
            });
        })
        ->first(['uuid'])?->uuid;

        $data['is_real_estate_broker'] = $user->user_companies->contains(function ($userCompany) use ($user) {
            return self::getRealEstateBrokerPositionsFromCompany($user->company_id)->pluck('id')->contains($userCompany->position_id);
        });

        $data['is_supervisor'] = $user->user_companies->contains(function ($userCompany) use ($user) {
            return self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id')->contains($userCompany->position_id);
        });

        $data['is_manager'] = $user->user_companies->contains(function ($userCompany) use ($user) {
            return self::getManagerPositionsFromCompany($user->company_id)->pluck('id')->contains($userCompany->position_id);
        });

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

        $user->refresh();

        $user->loadMissing(
            'company',
            'groups',
        );
        $data = $user->toArray();
        $data['sync_to'] = 'sys';
        $data['permissions'] = $user->getAllPermissions();

        if (!in_array($data['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        //Supervisor
        $data['supervisor_uuid'] = config('hub.model_user')::query()->whereHas('user_companies', function ($query) use ($user) {
            $query->where('company_id', $user->company_id);

            //Usuário que tenha cargo de Supervisor
            $query->whereIn('position_id', self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id'));

            $query->where(function ($query) use ($user) {
                //Que o user passado seja corretor abaixo desse supervisor
                $query->whereHas('children_positions', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getRealEstateBrokerPositionsFromCompany($user->company_id)->pluck('id'));
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    });
                });
                //OU que o user passado seja gerente acima desse supervisor
                $query->orWhereHas('parent_position', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getManagerPositionsFromCompany($user->company_id)->pluck('id'));
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    });
                });
            });
        })
        ->first(['uuid'])?->uuid;
    
        //Gerente
        $data['manager_uuid'] = config('hub.model_user')::query()->whereHas('user_companies', function ($query) use ($user) {
            $query->where('company_id', $user->company_id);

            //Usuário que tenha cargo de Gerente
            $query->whereIn('position_id', self::getManagerPositionsFromCompany($user->company_id)->pluck('id'));

            $query->where(function ($query) use ($user) {
                //Que o user passado seja supervisor abaixo desse gerente
                $query->whereHas('children_positions', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id'));
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    });
                });
                //OU que o user passado seja corretor abaixo de algum supervisor abaixo desse gerente
                $query->orWhereHas('children_positions', function ($query) use ($user) {
                    $query->whereIn('position_id', self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id'));

                    $query->whereHas('children_positions', function ($query) use ($user) {
                        $query->whereIn('position_id', self::getRealEstateBrokerPositionsFromCompany($user->company_id)->pluck('id'));
                        $query->whereHas('user', function ($query) use ($user) {
                            $query->where('id', $user->id);
                        });
                    });
                });
            });
        })
        ->first(['uuid'])?->uuid;

        $data['is_real_estate_broker'] = $user->user_companies->contains(function ($userCompany) use ($user) {
            return self::getRealEstateBrokerPositionsFromCompany($user->company_id)->pluck('id')->contains($userCompany->position_id);
        });

        $data['is_supervisor'] = $user->user_companies->contains(function ($userCompany) use ($user) {
            return self::getSupervisorPositionsFromCompany($user->company_id)->pluck('id')->contains($userCompany->position_id);
        });

        $data['is_manager'] = $user->user_companies->contains(function ($userCompany) use ($user) {
            return self::getManagerPositionsFromCompany($user->company_id)->pluck('id')->contains($userCompany->position_id);
        });

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
        $this->updated($user);
    }

    protected static function getManagerPositionsFromCompany(int $companyId): Collection
    {
        if (!$mainCompany = config('hub.model_company')::find($companyId)) {
            return collect();
        }

        while($mainCompany->main_company_id) {
            $mainCompany = $mainCompany->main_company;
        }

        return config('hub.model_position')::where('company_id', $mainCompany->id)
            ->whereNull('parent_position_id')
            ->get();
    }

    protected static function getSupervisorPositionsFromCompany(int $companyId): Collection
    {
        if (!$mainCompany = config('hub.model_company')::find($companyId)) {
            return collect();
        }

        while($mainCompany->main_company_id) {
            $mainCompany = $mainCompany->main_company;
        }

        return config('hub.model_position')::where('company_id', $mainCompany->id)
            ->whereIn('parent_position_id', self::getManagerPositionsFromCompany($companyId)->pluck('id'))
            ->get();
    }

    protected static function getRealEstateBrokerPositionsFromCompany(int $companyId)
    {
        if (!$mainCompany = config('hub.model_company')::find($companyId)) {
            return collect();
        }

        while($mainCompany->main_company_id) {
            $mainCompany = $mainCompany->main_company;
        }

        return config('hub.model_position')::where('company_id', $mainCompany->id)
            ->whereIn('parent_position_id', self::getSupervisorPositionsFromCompany($companyId)->pluck('id'))
            ->get();
    }
}

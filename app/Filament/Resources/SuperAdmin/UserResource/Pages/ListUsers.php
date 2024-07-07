<?php

namespace App\Filament\Resources\SuperAdmin\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use Spatie\Permission\Models\Role;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\SuperAdmin\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListUsers extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getHeaderWidgets(): array
    {
        return static::$resource::getWidgets();
    }

    // public function getTabs(): array
    // {
    //     // Ambil semua peran dari database
    //     $roles = Role::all();

    //     // Inisialisasi array tab
    //     $tabs = [
    //         null => Tab::make('All'),
    //     ];

    //     // Iterasi melalui setiap peran dan buat tab dinamis
    //     foreach ($roles as $role) {
    //         $tabs[$role->name] = Tab::make()->query(fn ($query) => $query->with('roles')->whereRelation('roles', 'name', '=', $role->name));
    //     }

    //     return $tabs;
    // }

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();
        $model = (new (static::$resource::getModel())())
            ->with('roles')
            ->where('id', '!=', auth()->user()->id);

        if (!$user->isSuperAdmin()) {
            $model = $model->whereDoesntHave('roles', function ($query) {
                $query->where('name', '=', config('filament-shield.super_admin.name'));
            });
        }

        return $model;
    }
}

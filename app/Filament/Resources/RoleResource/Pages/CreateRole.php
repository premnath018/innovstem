<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Spatie\Permission\Models\Role as ModelsRole;
use Throwable;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;


    public function create(bool $another = false): void
    {
        $this->authorizeAccess();
    
        try {
            $this->beginDatabaseTransaction();
    
            $this->callHook('beforeValidate');
    
            $data = $this->form->getState();
    
            $this->callHook('afterValidate');
    
            $data = $this->mutateFormDataBeforeCreate($data);
    
            $this->callHook('beforeCreate');
    
            // Extract permissions from the data
            $permissions = collect($data)
                ->filter(fn ($value, $key) => str_starts_with($key, 'permissions') && $value === true)
                ->keys()
                ->map(fn ($key) => (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT)) // Extracting the ID
                ->toArray();
    
            // Creating the role
            $role = ModelsRole::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'],
            ]);
    
            // Assign each permission to the role
            if (!empty($permissions)) {
                $role->permissions()->attach($permissions);
            }
    
            $this->record = $role; // Assign the created role to the record
    
            $this->callHook('afterCreate');
    
            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();
    
            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();
            throw $exception;
        }
    
        $this->rememberData();
    
        $this->getCreatedNotification()?->send();
    
        if ($another) {
            // Ensure that the record is properly set before calling model()
            if ($this->record) {
                $this->form->model($this->record::class);
            }
            $this->record = null;
            $this->fillForm();
            return;
        }
    
        $redirectUrl = $this->getRedirectUrl();
        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
    }
    
    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return config('filament-spatie-roles-permissions.should_redirect_to_index.roles.after_create', false)
            ? $resource::getUrl('index')
            : parent::getRedirectUrl();
    }
}

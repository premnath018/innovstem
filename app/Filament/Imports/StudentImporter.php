<?php

namespace App\Filament\Imports;

use App\Models\Student;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['email', 'max:255']), // No unique rule here

            ImportColumn::make('mobile')
                ->requiredMapping()
                ->rules(['required', 'max:255']), // No unique rule here

            ImportColumn::make('standard')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('ambition')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('parent_no')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('age')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),

            ImportColumn::make('gender')
                ->requiredMapping()
                ->rules(['required', 'in:male,female,other']),

            ImportColumn::make('district')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('state')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Student
    {
        return DB::transaction(function () {
            // ✅ First, find or create the user
            $user = User::firstOrCreate(
                ['email' => $this->data['email']], // Lookup by email
                [
                    'name' => $this->data['name'],
                    'password' => Hash::make($this->data['mobile']), // Default password is mobile
                ]
            );

            // ✅ Now, create or update the student and link the user_id
            return Student::updateOrCreate(
                ['user_id' => $user->id], // Match by user_id
                [
                    'name' => $this->data['name'],
                    'mobile' => $this->data['mobile'],
                    'standard' => $this->data['standard'],
                    'ambition' => $this->data['ambition'] ?? null,
                    'parent_no' => $this->data['parent_no'] ?? null,
                    'age' => $this->data['age'],
                    'gender' => $this->data['gender'],
                    'district' => $this->data['district'],
                    'address' => $this->data['address'],
                    'state' => $this->data['state'],
                ]
            );
        });
    }

    public function saveRecord(): void
    {
    //    dd($this->record);
        $this->record->save();
    }


    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed. ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}

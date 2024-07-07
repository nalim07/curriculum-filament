<?php

namespace App\Filament\Resources\SuperAdmin;

use Exception;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Settings\MailSettings;
use Filament\Facades\Filament;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Resources\SuperAdmin\UserResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\SuperAdmin\UserResource\RelationManagers\StudentsRelationManager;
use App\Filament\Resources\SuperAdmin\UserResource\RelationManagers\EmployeesRelationManager;

// function generateUniqueEmployeeCode(): string
// {
//     $code = Str::random(6); // Menghasilkan kode acak
//     while (User::where('employee_code', $code)->exists()) {
//         $code = Str::random(6); // Ulangi jika kode sudah ada di database
//     }
//     return strtoupper($code);
// }


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static int $globalSearchResultsLimit = 20;

    protected static ?int $navigationSort = -1;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->hiddenLabel()
                                    ->avatar()
                                    ->collection('avatars')
                                    ->alignCenter()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('username')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Toggle::make('status')
                            ->required(),
                    ])
                    ->columnSpan([
                        'sm' => 1,
                        'lg' => 2
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Role')
                            ->schema([
                                Select::make('roles')->label('Role')
                                    ->hiddenLabel()
                                    ->relationship('roles', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => Str::headline($record->name))
                                    ->multiple()
                                    ->preload()
                                    ->native(false),
                            ])
                            ->compact(),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->required(),
                                Forms\Components\TextInput::make('passwordConfirmation')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->same('password')
                                    ->required(),
                            ])
                            ->compact()
                            ->hidden(fn (string $operation): bool => $operation === 'edit'),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('email_verified_at')
                                    ->label(__('resource.general.email_verified_at'))
                                    ->content(fn (User $record): ?string => $record->email_verified_at),
                                Forms\Components\Actions::make([
                                    Action::make('resend_verification')
                                        ->label(__('resource.user.actions.resend_verification'))
                                        ->color('secondary')
                                        ->action(fn (MailSettings $settings, Model $record) => static::doResendEmailVerification($settings, $record)),
                                ])
                                    ->hidden(fn (User $user) => $user->email_verified_at != null)
                                    ->fullWidth(),
                                Forms\Components\Placeholder::make('created_at')
                                    ->label(__('resource.general.created_at'))
                                    ->content(fn (User $record): ?string => $record->created_at?->diffForHumans()),
                                Forms\Components\Placeholder::make('updated_at')
                                    ->label(__('resource.general.updated_at'))
                                    ->content(fn (User $record): ?string => $record->updated_at?->diffForHumans()),
                            ])
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columnSpan(1),
            ])->columns(3);
    }

    public static function getForm(string $operation): array
    {
        return [
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('media')
                                ->hiddenLabel()
                                ->avatar()
                                ->collection('avatars')
                                ->alignCenter()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('username')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),
                    Forms\Components\Toggle::make('status')
                        ->required(),
                ])
                ->columnSpan([
                    'sm' => 1,
                    'lg' => 2
                ]),
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Role')
                        ->schema([
                            Select::make('roles')->label('Role')
                                ->hiddenLabel()
                                ->relationship('roles', 'name')
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => Str::headline($record->name))
                                ->multiple()
                                ->preload()
                                ->native(false),
                        ])
                        ->compact(),
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->revealable()
                                ->required(),
                            Forms\Components\TextInput::make('passwordConfirmation')
                                ->password()
                                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->revealable()
                                ->same('password')
                                ->required(),
                        ])
                        ->compact()
                        ->hidden(fn () => $operation === 'edit'),
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('email_verified_at')
                                ->label(__('resource.general.email_verified_at'))
                                ->content(fn (?User $record): ?string => $record?->email_verified_at),
                            Forms\Components\Actions::make([
                                Action::make('resend_verification')
                                    ->label(__('resource.user.actions.resend_verification'))
                                    ->color('secondary')
                                    ->action(fn (MailSettings $settings, Model $record) => static::doResendEmailVerification($settings, $record)),
                            ])
                                ->hidden(fn (?User $user) => $user?->email_verified_at != null)
                                ->fullWidth(),
                            Forms\Components\Placeholder::make('created_at')
                                ->label(__('resource.general.created_at'))
                                ->content(fn (?User $record): ?string => $record?->created_at?->diffForHumans()),
                            Forms\Components\Placeholder::make('updated_at')
                                ->label(__('resource.general.updated_at'))
                                ->content(fn (?User $record): ?string => $record?->updated_at?->diffForHumans()),
                        ])
                        ->hidden(fn () => $operation === 'create'),
                ])
                ->columnSpan(1)
        ];
    }


    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->fileName(fn (Export $export): string => "user-export-{$export->getKey()}")
                    ->columnMapping(false),
                ImportAction::make()
                    ->importer(UserImporter::class),
            ])
            ->columns([
                // SpatieMediaLibraryImageColumn::make('media')->label('Avatar')
                //     ->collection('avatars')
                //     ->circular()
                //     ->wrap(),
                TextColumn::make('student.fullname')
                    ->label('Student Full Name')
                    ->searchable()
                    ->getStateUsing(function (?Model $record) {
                        return $record->student->fullname ?? '-';
                    })
                    ->extraAttributes(function (?Model $record) {
                        return [
                            'style' => $record && $record->student ? '' : 'display: flex; align-items: center; justify-content: center;'
                        ];
                    })
                    ->hidden(fn (?Model $record) => $record && $record->employee !== null),

                TextColumn::make('employee.fullname')
                    ->label('Employee Full Name')
                    ->searchable()
                    ->getStateUsing(function (?Model $record) {
                        return $record->employee->fullname ?? '-';
                    })
                    ->extraAttributes(function (?Model $record) {
                        return [
                            'style' => $record && $record->employee ? '' : 'display: flex; align-items: center; justify-content: center;'
                        ];
                    })
                    ->hidden(fn (?Model $record) => $record && $record->student !== null),
                Tables\Columns\TextColumn::make('username')->label('Username')
                    ->copyable()
                    ->copyableState(fn (string $state): string => "Color: {$state}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Role')
                    // ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['info'])
                    ->badge(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->copyableState(fn (string $state): string => "Color: {$state}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')->label('Verified at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function resolveRecord($record)
    {
        return $record instanceof User ? $record : User::findOrFail($record);
    }

    public static function getRelations(): array
    {
        return [
            StudentsRelationManager::class,
            EmployeesRelationManager::class,
        ];
    }

    public static function relations($record): array
    {
        $relations = [];

        if ($record->student) {
            $relations[] = StudentsRelationManager::class;
        } else if ($record->employee) {
            $relations[] = EmployeesRelationManager::class;
        }

        return $relations;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->email;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'username'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'name' => $record->fullname ?? $record->fullname,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.user_management");
    }

    public static function doResendEmailVerification($settings = null, $user): void
    {
        if (!method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = new VerifyEmail();
        $notification->url = Filament::getVerifyEmailUrl($user);

        $settings->loadMailSettingsToConfig();

        $user->notify($notification);

        Notification::make()
            ->title(__('resource.user.notifications.notification_resent.title'))
            ->success()
            ->send();
    }
}

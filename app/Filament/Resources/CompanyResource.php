<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlternativeResource\RelationManagers\ResourcesRelationManager;
use App\Filament\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Resources\CompanyResource\Pages\ListCompanies;
use App\Filament\Resources\CompanyResource\RelationManagers\AlternativesRelationManager;
use App\Filament\Resources\CompanyResource\RelationManagers\OfficeLocationsRelationManager;
use App\Models\Company;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('slug')->required(),
                TextInput::make('url')->required(),
                DateTimePicker::make('approved_at'),
                Textarea::make('description')->columnSpanFull(),
                Textarea::make('notes')->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('logo'),

                Select::make('tags')->relationship('tagsRelation', 'name')
                    ->multiple()->searchable()->preload()->native(false)
                    ->createOptionForm([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('slug')
                                ->required(),
                        ]),
                    ]),
                TextInput::make('headquarter'),
                TextInput::make('valuation')->nullable()->numeric(),
                TextInput::make('exit_valuation')->nullable(),
                Select::make('category_id')
                    ->relationship('category', 'title'),
                Select::make('exit_strategy_id')
                    ->relationship('exitStrategy', 'title'),
                Select::make('funding_level_id')
                    ->relationship('fundingLevel', 'title'),
                Select::make('company_size_id')
                    ->relationship('companySize', 'title'),
                TextInput::make('stock_symbol'),
                TextInput::make('total_funding')->nullable(),
                DatePicker::make('last_funding_date'),
                DatePicker::make('founded_at'),
                TextInput::make('employee_count')->numeric(),
                TextInput::make('stock_quote'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')->size(70),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('tagsRelation.name')->label('Tags')->badge()->searchable(),
                TextColumn::make('officeLocations.name')->badge()->color('info')->searchable(),
                TextColumn::make('url')
                    ->url(fn (Company $record) => $record->url)
                    ->color('info')
                    ->openUrlInNewTab()->searchable()->limit(50),
                TextColumn::make('resources.url')
                    ->label('Resources')
                    ->formatStateUsing(function ($record) {
                        return $record->resources->map(function ($resource): string {
                            return "<a href='{$resource->url}' target='_blank'>{$resource->url}</a>";
                        })->implode('<br>');
                    })
                    ->html()
                    ->disabledClick()
                    ->color('info'),
                IconColumn::make('approved_at')->label('Approved')
                    ->boolean(fn (Company $record): bool => $record->approved_at !== null),
                TextColumn::make('valuation')->numeric()->sortable(),
                TextColumn::make('category.title')->numeric()->sortable(),
                TextColumn::make('exit_valuation')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('exitStrategy.title')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fundingLevel.title')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('companySize.title')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_symbol')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_funding')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_funding_date')->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('headquarter')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('founded_at')->date()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('employee_count')->numeric()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock_quote')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ResourcesRelationManager::class,
            OfficeLocationsRelationManager::class,
            AlternativesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}

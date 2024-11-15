<?php

namespace App\Filament\Resources\FundingLevelResource\Pages;

use App\Filament\Resources\FundingLevelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFundingLevel extends CreateRecord
{
    protected static string $resource = FundingLevelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

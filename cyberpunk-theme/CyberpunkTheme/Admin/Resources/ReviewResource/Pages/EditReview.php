<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\ReviewResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\ReviewResource;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->after(fn () => Reviews::flush()),
        ];
    }

    protected function afterSave(): void
    {
        // La nota media cambia al editar las estrellas.
        Reviews::flush();
    }
}

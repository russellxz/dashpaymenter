<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\CommentResource\Pages\EditComment;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\CommentResource\Pages\ListComments;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Support\UserColumn;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;

/**
 * Comentarios de la comunidad y respuestas.
 *
 * Las reseñas con estrellas tienen su propio apartado (Reseñas), así que aquí
 * sólo aparece lo que NO es una reseña: comentarios de las publicaciones y
 * respuestas a otras personas.
 */
class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?string $navigationLabel = 'Comunidad · Comentarios';

    protected static ?string $modelLabel = 'comentario';

    protected static ?string $pluralModelLabel = 'comentarios';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-chat-3-line';

    protected static ?int $navigationSort = 3;

    /**
     * Todo lo que no lleva estrellas: comentarios y respuestas.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('rating');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('content')->label('Contenido')->rows(5)->required()->columnSpanFull(),
            Toggle::make('approved')->label('Aprobado'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                UserColumn::make(),
                TextColumn::make('content')
                    ->label('Comentario')
                    ->limit(70)
                    ->tooltip(fn (Comment $record) => $record->content)
                    ->searchable(),
                TextColumn::make('parent_id')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Respuesta' : 'Comentario')
                    ->color(fn ($state) => $state ? 'gray' : 'info'),
                TextColumn::make('likes_count')->label('Útil')->sortable()->toggleable(),
                IconColumn::make('approved')->label('Aprobado')->boolean(),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved')->label('Aprobado'),
                TernaryFilter::make('parent_id')
                    ->label('Sólo respuestas')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('parent_id'),
                        false: fn (Builder $query) => $query->whereNull('parent_id'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                Action::make('toggleApproved')
                    ->label(fn (Comment $record) => $record->approved ? 'Ocultar' : 'Aprobar')
                    ->icon(fn (Comment $record) => $record->approved ? 'ri-eye-off-line' : 'ri-check-line')
                    ->color(fn (Comment $record) => $record->approved ? 'warning' : 'success')
                    ->action(fn (Comment $record) => $record->update(['approved' => !$record->approved])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComments::route('/'),
            'edit' => EditComment::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && ($user->hasPermission('admin.settings.view') || $user->hasPermission('admin.cyberpunk.moderate'));
    }
}

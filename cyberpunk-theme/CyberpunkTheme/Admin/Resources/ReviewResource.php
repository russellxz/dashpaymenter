<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\ReviewResource\Pages\EditReview;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Resources\ReviewResource\Pages\ListReviews;
use Paymenter\Extensions\Others\CyberpunkTheme\Admin\Support\UserColumn;
use Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews;

/**
 * Reseñas con estrellas: las de los planes y las del servicio en general.
 *
 * Los comentarios de la comunidad tienen su propio apartado.
 */
class ReviewResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?string $navigationLabel = 'Reseñas';

    protected static ?string $modelLabel = 'reseña';

    protected static ?string $pluralModelLabel = 'reseñas';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-star-smile-line';

    protected static ?int $navigationSort = 2;

    /**
     * Sólo lo que lleva estrellas y no es una respuesta.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNull('parent_id')
            ->whereNotNull('rating');
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $pendientes = static::getEloquentQuery()->where('approved', false)->count();

            return $pendientes > 0 ? (string) $pendientes : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('rating')
                ->label('Estrellas')
                ->options([
                    5 => '★★★★★  Excelente',
                    4 => '★★★★☆  Bueno',
                    3 => '★★★☆☆  Normal',
                    2 => '★★☆☆☆  Malo',
                    1 => '★☆☆☆☆  Muy malo',
                ])
                ->required(),
            Textarea::make('content')->label('Reseña')->rows(5)->required()->columnSpanFull(),
            Toggle::make('approved')
                ->label('Publicada')
                ->helperText('Si se desactiva, deja de verse en la web y no cuenta para la nota media.'),
            Toggle::make('featured')
                ->label('Destacar en el inicio')
                ->helperText('Aparece en la página principal, después de los planes.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                UserColumn::make(),
                TextColumn::make('rating')
                    ->label('Estrellas')
                    ->sortable()
                    ->formatStateUsing(fn (?int $state) => $state
                        ? str_repeat('★', $state) . str_repeat('☆', 5 - $state)
                        : '—')
                    ->color(fn (?int $state) => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('commentable_type')
                    ->label('Sobre')
                    ->badge()
                    ->color(fn (?string $state) => $state === Comment::GENERAL ? 'info' : 'gray')
                    ->formatStateUsing(fn (?string $state) => $state === Comment::GENERAL
                        ? 'El servicio'
                        : 'Un plan')
                    ->description(fn (Comment $record) => $record->targetLabel()),
                TextColumn::make('content')
                    ->label('Reseña')
                    ->limit(70)
                    ->tooltip(fn (Comment $record) => $record->content)
                    ->searchable(),
                TextColumn::make('replies_count')
                    ->label('Respuestas')
                    ->counts('replies')
                    ->toggleable(),
                IconColumn::make('featured')
                    ->label('En el inicio')
                    ->boolean()
                    ->trueIcon('ri-award-fill')
                    ->falseIcon('ri-subtract-line')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                IconColumn::make('approved')->label('Publicada')->boolean(),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->label('Estrellas')
                    ->options([
                        5 => '★★★★★',
                        4 => '★★★★☆',
                        3 => '★★★☆☆',
                        2 => '★★☆☆☆',
                        1 => '★☆☆☆☆',
                    ]),
                SelectFilter::make('commentable_type')
                    ->label('Sobre')
                    ->options([
                        Product::class => 'Un plan',
                        Comment::GENERAL => 'El servicio en general',
                    ]),
                TernaryFilter::make('featured')->label('Destacada en el inicio'),
                TernaryFilter::make('approved')->label('Publicada'),
            ])
            ->recordActions([
                Action::make('toggleFeatured')
                    ->label(fn (Comment $record) => $record->featured ? 'Quitar del inicio' : 'Destacar')
                    ->icon('ri-award-fill')
                    ->color(fn (Comment $record) => $record->featured ? 'gray' : 'warning')
                    ->action(function (Comment $record) {
                        $record->update(['featured' => !$record->featured]);
                        Reviews::flush();
                    }),
                Action::make('toggleApproved')
                    ->label(fn (Comment $record) => $record->approved ? 'Ocultar' : 'Publicar')
                    ->icon(fn (Comment $record) => $record->approved ? 'ri-eye-off-line' : 'ri-check-line')
                    ->color(fn (Comment $record) => $record->approved ? 'warning' : 'success')
                    ->action(function (Comment $record) {
                        $record->update(['approved' => !$record->approved]);
                        Reviews::flush();
                    }),
                EditAction::make(),
                DeleteAction::make()->after(fn () => Reviews::flush()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('destacar')
                        ->label('Destacar en el inicio')
                        ->icon('ri-award-fill')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            Comment::whereIn('id', $records->pluck('id'))->update(['featured' => true]);
                            Reviews::flush();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('quitarDestacado')
                        ->label('Quitar del inicio')
                        ->icon('ri-subtract-line')
                        ->color('gray')
                        ->action(function (Collection $records) {
                            Comment::whereIn('id', $records->pluck('id'))->update(['featured' => false]);
                            Reviews::flush();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()->after(fn () => Reviews::flush()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && ($user->hasPermission('admin.settings.view') || $user->hasPermission('admin.cyberpunk.moderate'));
    }
}

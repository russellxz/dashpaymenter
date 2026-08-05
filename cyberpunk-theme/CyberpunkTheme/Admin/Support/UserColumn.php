<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Admin\Support;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

/**
 * Columna del usuario para las tablas del panel.
 *
 * Paymenter no guarda un campo `name`: el nombre se compone de `first_name`
 * y `last_name`. Si se deja que Filament busque por "user.name" la consulta
 * sale con `WHERE users.name LIKE ...` y la base de datos responde
 * "Unknown column 'name'". Por eso aquí definimos a mano cómo buscar y
 * ordenar, e incluimos también el correo.
 */
class UserColumn
{
    public static function make(string $name = 'user.name', string $label = 'Usuario'): TextColumn
    {
        return TextColumn::make($name)
            ->label($label)
            ->description(fn ($record) => $record->user?->email)
            ->searchable(query: function (Builder $query, string $search): Builder {
                $texto = trim($search);

                return $query->whereHas('user', function (Builder $q) use ($texto) {
                    $q->where('first_name', 'like', "%{$texto}%")
                        ->orWhere('last_name', 'like', "%{$texto}%")
                        ->orWhere('email', 'like', "%{$texto}%")
                        // Nombre y apellido juntos: "Ana Torres"
                        ->orWhereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$texto}%"]);
                });
            })
            ->sortable(query: function (Builder $query, string $direction): Builder {
                return $query
                    ->orderBy(
                        \App\Models\User::select('first_name')->whereColumn('users.id', 'user_id'),
                        $direction
                    )
                    ->orderBy(
                        \App\Models\User::select('last_name')->whereColumn('users.id', 'user_id'),
                        $direction
                    );
            });
    }
}

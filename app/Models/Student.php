<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
 * EXAMPLE::PROGETTO model studente
 */

/**
 * Class Student
 * @property User user
 * @package App\Models
 */
class Student extends Model
{
    use HasFactory;

    /**
     * Variabili che possono essere manipolate nel frontend (per questo non c'è ID)
     *
     * @var string[]
     */
    protected $fillable = ['first_name', 'last_name', 'age', 'user_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'first_name' => 'string',
        'last_name' => 'string',
        'age' => 'int',
    ];

    /**
     * @return string
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    /**
     * Filtri presenti sulla pagina
     *
     * @return string[]
     */
    public static function getFilters(): array
    {
        return ['search', 'sort_order', 'sort_field', 'items_per_page', 'current_page'];
    }

    /**
     * funzione per filtrare
     *
     * @param Builder $query
     * @param array $filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        \Log::info(print_r($filters, true));
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function (Builder $query) use ($search) {
                        $query->where('email', 'like', '%' . $search . '%');
                    });
            });
        });

        $query->when($filters['sort_field'] ?? null, function (Builder $query, string $sortField) use ($filters) {
            if (strpos($sortField, 'email') !== false) {
                $query->join('users', 'users.id', '=', 'students.user_id', 'students')
                    ->orderBy('email', $filters['sort_order']);
            } else {
                $query->orderBy($filters['sort_field'], $filters['sort_order']);
            }
        });
    }

    // SPECIFICA LA RELAZIONE 1-1 con user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // SPECIFIFCA LA RELAZIONE students <--->> votes (uno studente ha + [has many] voti)
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}

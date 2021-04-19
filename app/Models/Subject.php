<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * EXAMPLE::PROGETTO model materia
 */
class Subject extends Model
{
    use HasFactory;

    /**
     * Variabili che possono essere manipolate nel frontend (per questo non c'è ID)
     *
     * @var string[]
     */
    protected $fillable = ['name', 'description'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
    ];

    public static function getFilters()
    {
        return ['search', 'sort_order', 'sort_field'];
    }

    /**
     * funzione per filtrare
     *
     * @param $query
     * @param array $filters
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        })->when($filters['sort_field'] ?? null, function ($query, $sortField) use ($filters) {
            $query->orderBy($sortField, $filters['sort_order']);
        });
    }

    // SPECIFIFCA LA RELAZIONE subjects <--->> votes (una materia ha + [has many] voti)
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}

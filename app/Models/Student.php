<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * EXAMPLE::PROGETTO model studente
 */
class Student extends Model
{
    use HasFactory;

    /**
     * Variabili che possono essere manipolate nel frontend (per questo non c'è ID)
     *
     * @var string[]
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'age', 'user_id'];

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
     * Filtri presenti sulla pagina
     *
     * @return string[]
     */
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
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        })->when($filters['sort_field'] ?? null, function ($query, $sortField) use ($filters) {
            $query->orderBy($sortField, $filters['sort_order']);
        });
    }

    // SPECIFICA LA RELAZIONE 1-1 con user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // SPECIFIFCA LA RELAZIONE students <--->> votes (uno studente ha + [has many] voti)
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}

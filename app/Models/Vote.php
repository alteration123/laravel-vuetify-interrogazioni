<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * EXAMPLE::PROGETTO model voto
 */
class Vote extends Model
{
    use HasFactory;

    //in questo caso la tabella si chiama student_subject, mentre la model Vote
    //quindi bisogna specificare che a questa model corrisponde quella tabella
    protected $table = 'student_subject';

    protected $fillable = [
        'student_id',
        'subject_id',
        'score',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        //specifico il formato della datetime
        'date' => 'datetime:Y-m-d H:i:s',
        'score' => 'integer',
        'student_id' => 'integer',
        'subject_id' => 'integer',
    ];

    //METODO FILTRO
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('subject', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('student', function ($query) use ($search) {
                        $query->where('email', 'like', '%'.$search.'%')
                            ->orWhere('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                });
            });
        })->when($filters['sort_field'] ?? null, function ($query, $sortField) use ($filters) {
            if ($sortField == 'student') {
                $query->join('students', 'students.id', '=', 'student_subject.student_id')
                    ->orderBy('students.email', $filters['sort_order']);
            } else if ($sortField == 'subject') {
                $query->join('subjects', 'subjects.id', '=', 'student_subject.subject_id')
                    ->orderBy('subjects.name', $filters['sort_order']);
            } else {
                $query->orderBy($sortField, $filters['sort_order']);
            }
        });
    }

    public static function getFilters()
    {
        return ['search', 'sort_order', 'sort_field'];
    }

    //SPECIFIFCA LA RELAZIONE votes <<---> students (un voto appartiene (belongsTo) ad un solo studente)
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    //SPECIFIFCA LA RELAZIONE votes <<---> subjects (un voto appartiene (belongsTo) ad una sola materia)
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

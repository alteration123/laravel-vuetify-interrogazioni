<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Validators\Controller\StudentUserValidator;
use DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/*
 * EXAMPLE::PROGETTO Controller Studente
 */
class StudentsController extends Controller
{
    /**
     * @var StudentUserValidator
     */
    protected $studentUserValidator;

    /**
     * StudentsController constructor.
     * @param StudentUserValidator $studentUserValidator
     */
    public function __construct(
        StudentUserValidator $studentUserValidator
    ) {
        $this->studentUserValidator = $studentUserValidator;
    }

    /**
     * Controller richiamato per visualizzare la lista degli studenti
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Student::class);

        //per vedere quali filtri vengono passati
//        dd(\Illuminate\Support\Facades\Request::only(Student::getFilters()));

        return Inertia::render('Students/Index', [
            'students' => Student::query()
                ->filter(\Illuminate\Support\Facades\Request::only(Student::getFilters()))
                ->with('user')
                ->paginate()
                ->withQueryString()
                ->through(function ($student) {
                    return [
                        'id' => $student->id,
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                        'email' => $student->email,
                        'age' => $student->age,
                        'user_id' => $student->user_id,
                    ];
                }),
        ]);
    }

    /**
     * @return Response
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Student::class);

        return Inertia::render('Students/Create', [
            'users' => User::query()
                ->orderBy('name')
                ->get()
                ->map
                ->only(['id', 'name']),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(Request $request)
    {
        $this->authorize('create', Student::class);

        $this->studentUserValidator->validateData($request->toArray());

        //TODO::NOTE in realtà il modo corretto di fare questa cosa è tramite repository + management
        DB::beginTransaction();

        //crea solo studente nella variabile
        $student = Student::factory()->newModel($request->toArray());

        //crea un utente e lo inserisce nel DB
        $newUser = User::factory()->create([
            'name' => $student->first_name . " " . $student->last_name,
            'email' => $student->email,
            'password' => bcrypt($request->get('password'))
        ]);

        $student->user_id = $newUser->id;
        $student->save();

        DB::commit();

        return Inertia::location('/studenti');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * @param Student $student
     * @return Response
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function edit(Student $student)
    {
        $this->authorize('update', [Student::class, $student]);

        $this->studentUserValidator->validateData($student->toArray(), ['student' => $student]);

        $student->saveOrFail();

        return Inertia::render('Students/Edit', [
            'student' => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->last_name,
                'age' => $student->last_name,
                'user_id' => $student->last_name,
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param Student $student
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Student $student)
    {
        $this->authorize('update', [Student::class, $student]);

        $student->update(
            Request::validate([
                'first_name' => ['required', 'max:255'],
                'last_name' => ['required', 'max:255'],
                'email' => ['required', 'max:255'],
                'age' => ['required', 'min:10', 'max:20'],
                'user_id' => ['required'],
            ])
        );

        return Redirect::route('students.edit', $student->id);
    }

    /**
     * @param Student $student
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete', [Student::class, $student]);

        return Inertia::location('/students');
    }
}

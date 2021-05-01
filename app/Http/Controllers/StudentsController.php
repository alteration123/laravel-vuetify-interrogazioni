<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Repositories\StudentRepository;
use App\Validators\Controller\StudentUserValidator;
use Database\Factories\StudentFactory;
use DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/*
 * EXAMPLE::PROGETTO Controller Studente
 */
class StudentsController extends Controller
{
    protected StudentUserValidator $studentUserValidator;
    protected StudentRepository $studentRepository;

    /**
     * StudentsController constructor.
     * @param StudentUserValidator $studentUserValidator
     * @param StudentRepository $studentRepository
     */
    public function __construct(
        StudentUserValidator $studentUserValidator,
        StudentRepository $studentRepository
    ) {
        $this->studentUserValidator = $studentUserValidator;
        $this->studentRepository = $studentRepository;
    }

    /**
     * Controller richiamato per visualizzare la lista degli studenti
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Student::class);

        //per vedere quali filtri vengono passati
//        dd(\Illuminate\Support\Facades\Request::only(Student::getFilters()));
        //bisognerebbe aggiungere un validatore dei filtri

        return Inertia::render('Students/Index', [
            'students' => Student::query()
                ->filter(\Illuminate\Support\Facades\Request::only(Student::getFilters()))
                ->with('user')
                ->paginate()
                ->withQueryString()
                ->through(function ($student) {
                    return $student->toArray();
                }),
        ]);
    }

    /**
     * @return Response
     * @throws AuthorizationException
     */
    public function create(): Response
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
    public function store(Request $request): \Illuminate\Http\Response
    {
        $this->authorize('create', Student::class);

        $this->studentUserValidator->validateData($request->toArray());

        $this->studentRepository->saveOrCreate(
            StudentFactory::new()->make($request->all()),
            $request->get('email'),
            $request->get('password')
        );

        return Inertia::location('/studenti');
    }

    /**
     * Display the specified resource.
     *
     * @param Student $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student): \Illuminate\Http\Response
    {
        //
    }

    /**
     * @param Student $student
     * @return Response
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function edit(Student $student): Response
    {
        $this->authorize('update', [Student::class, $student]);

        return Inertia::render('Students/Edit', $student->toArray());
    }

    /**
     * @param Request $request
     * @param Student $student
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('update', [Student::class, $student]);

        $this->studentUserValidator->validateData($request->toArray(), ['student' => $student]);

        $this->studentRepository->save($student, $request->get('email'), $request->get('password'));

        return Redirect::route('students.edit', $student->id);
    }

    /**
     * @param Student $student
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(Student $student): \Illuminate\Http\Response
    {
        $this->authorize('delete', [Student::class, $student]);

        //feature implementata solo in backend
//        $this->studentRepository->delete($student);

        return Inertia::location('/studenti');
    }
}

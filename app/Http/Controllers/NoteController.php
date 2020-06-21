<?php


namespace App\Http\Controllers;

use App\Http\Requests\NoteRequest;
use App\Note;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JWTAuth;

class NoteController extends Controller
{
    public const PER_PAGE = 3;

    /**
     * NoteController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return Note::whereUserId(auth()->user()->id)->paginate(self::PER_PAGE);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, note with id ' . $id . ' cannot be found'
            ], 400);
        }

        return $note;
    }

    /**
     * @param NoteRequest $request
     * @return JsonResponse
     */
    public function create(NoteRequest $request): ?JsonResponse
    {
        $note = new Note();
        $note->title = $request->get('title');
        $note->content = $request->get('content');
        $note->user_id = auth()->user()->id;

        if ($note->save()) {
            return response()->json([
                'success' => true,
                'note' => $note
            ]);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, note could not be added'
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function addFile(Request $request, $id): ?JsonResponse
    {
        if ($request->hasFile('file'))
        {
            $path = $request->file->store('storage/notes');
            Note::whereId($id)->update(['file' => $path]);
            return response()->json([
                'success' => true,
                'path' => $path,
                'extension' => $request->file->getClientOriginalExtension(),
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Please, attach file.'
            ], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id): ?JsonResponse
    {
        $note = Note::whereId($id);

        if (!$note instanceof Note) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, note with id ' . $id . ' cannot be found'
            ], 400);
        }

        if ($note->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Note could not be deleted'
            ], 500);
        }
    }
}

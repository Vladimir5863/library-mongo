<?php
namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render("News/Index", [
            "news" => News::orderBy("startDate", "desc")->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render("News/Create");
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            "type" => "required|in:announcement,event,promotion",
            "title" => "required|string|max:255",
            "text" => "required|string",
            "startDate" => "required|date",
            "endDate" => "required|date|after:startDate",
            // Frontend prikazuje `data:image/png;base64,...`, pa ograničavamo na PNG.
            "multimedia" => "nullable|image|mimes:png|max:2048",
            "logo" => "required|image|mimes:png|max:2048",
        ]);

        if ($request->hasFile("multimedia")) {
            $fields["multimedia"] = base64_encode(
                file_get_contents($request->file("multimedia")->getRealPath()),
            );
        }

        if ($request->hasFile("logo")) {
            $fields["logo"] = base64_encode(
                file_get_contents($request->file("logo")->getRealPath()),
            );
        }

        News::create($fields);

        return redirect()
            ->route("news.index")
            ->with("success", "Vest uspešno kreirana!");
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return redirect()
            ->route("news.index")
            ->with("success", "Vest obrisana.");
    }

    public function show($id)
    {
        return Inertia::render("News/Show", [
            "item" => News::findOrFail($id),
        ]);
    }
}

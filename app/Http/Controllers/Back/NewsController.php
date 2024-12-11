<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\NewsRequest;
use App\Models\News;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = $this->fetchNews($request);

        $newArr = $news->map(function ($item) {
            return [
                'title' => $item->title,
                'date' => $item->date,
                'image' => $item->image,
                'description' => $item->description,
                'is_enabled' => $item->is_enabled,
                'news_type' => $item->news_type,
                'news_type_name' => $item->news_type_name()
            ];
        });


        // 判斷是否為 API 請求
        if ($request->wantsJson()) {
            // Log::info("message");
            return response()->json($newArr);
        }

        return Inertia::render('Back/Store', [
            'news' => $news
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(NewsRequest $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsRequest $request)
    {
        Log::info("message");
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $name = time() . '_' . $request->file('image')->getClientOriginalName();
                $path = '/storage/' . $request->file('image')->storeAs(
                    'sony/news',
                    $name,
                    'public'
                );
                $validated['image'] = $path; 
            }

            $news = News::create($validated);
            return response()->json($news);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsRequest $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function update_news(NewsRequest $request)
    {
        try {
            $id = $request->input('id');
            $store = News::find($id);

            $validated = $request->validated();

            // Log::info($request->input('delete_image'));
            // Log::info($request->has('delete_image'));

            //收到delete_image為true時刪掉，為false時不刪 (例如有某照片誤傳，但又沒有適合的圖時)
            if ($request->has('delete_image') && $request->input('delete_image') == true) {
                $path = str_replace('/storage/', '', $store->image);
                Storage::disk('public')->delete($path);
                $validated['image'] = null; // 將 image 字段設為 null
            } else {
                unset($validated['image']);
            }

            if ($request->hasFile('image')) {
                //有新圖片一律把舊的刪掉
                if ($store->image) {
                    $path = str_replace('/storage/', '', $store->image);
                    Storage::disk('public')->delete($path);
                }

                //上傳新圖
                $name = time() . '_' . $request->file('image')->getClientOriginalName();
                $path = "/storage/" . $request->file('image')->storeAs(
                    'sony/news',
                    $name,
                    'public'
                );
                $validated['image'] = $path;
            }

            $store->update($validated);

            return response()->json($store);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete_news(Request $request)
    {
        try {
            $id = $request->input('id');
            $news = News::find($id);
            if (!$news) {
                return response()->json(['error' => 'news not found'], 404);
            }

            if ($news->image) {
                $path = str_replace('/storage/', '', $news->image);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $news->delete();
            return response()->json(['success' => true]);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function fetchNews(Request $request)
    {
        $newsType = $request->input('news_type');

        $query = News::where('is_enabled', 1);

        if (!is_null($newsType)) {
            $query->where('news_type', $newsType);
        }

        return $query->get();
    }
}

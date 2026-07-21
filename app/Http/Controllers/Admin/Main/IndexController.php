<?php

namespace App\Http\Controllers\Admin\Main;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory as ViewFactory;

class IndexController extends Controller
{
    public function __invoke(ViewFactory $view_factory)
    {
        $data['countPost']     = Post::query()->count();
        $data['countCategory'] = Category::query()->count();
        $data['countTag']      = Tag::query()->count();

        return $view_factory->make('admin.main.index', ['data' => $data]);
    }
}

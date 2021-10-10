<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post extends Model
{
    use HasFactory;

    public $id;

    public $title;

    public $body;

    public $slug;

    public function __construct($id, $title, $body, $slug)
    {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->slug = $slug;
    }

    public static function allPosts()
    {
        return cache()->rememberForever('posts.all', function () {
            return collect(File::files(resource_path("posts")))
                ->map(fn($file) => YamlFrontMatter::parseFile($file))
                ->map(fn($document) => new Post(
                    $document->id,
                    $document->title,
                    $document->body(),
                    $document->slug
                ))
                ->sortByDesc('id');
        });
    }

    public static function find($slug)
    {
        return static::allPosts()->firstWhere('slug', $slug);
    }

    public static function findOrFail($slug)
    {
        $post = static::find($slug);

        if (!$post) {
            throw new ModelNotFoundException();
        }

        return $post;
    }
}

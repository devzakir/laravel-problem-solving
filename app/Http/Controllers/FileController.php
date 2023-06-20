<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\FileUpload;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use FileUpload;

    public function uploadMovieAttachment()
    {
        try {
            $movie = request()->file('file');

            if (isset($movie)) {
                $videoPath = Storage::disk('s3')->put("movies", $movie);
            }

            // $videoPath = $this->uploadFile(request()->file('file'), 'uploads');

            $thumbnail = null;
            if (!is_null(request()->file('thumbnail_file'))) {
                $thumbnail = $this->uploadFile(request()->file('thumbnail_file'), 'uploads');
            }
        } catch( \Throwable $e) {
            \Log::error($e);
            return response()->json(['message' => 'エラーが発生しました。'], 422);
        }

        

        // // clipping 15s preview movie
        // $start = \FFMpeg\Coordinate\TimeCode::fromSeconds(0);
        // $duration = \FFMpeg\Coordinate\TimeCode::fromSeconds(2);
        // $clipFilter = new \FFMpeg\Filters\Video\ClipFilter($start, $duration);

        // FFMpeg::fromDisk('s3')
        //         ->open($videoPath)
        //         ->addFilter($clipFilter)
        //         ->export()
        //         ->toDisk('s3')
        //         ->inFormat(new \FFMpeg\Format\Video\X264)
        //         ->save('preview/'.$videoPath);
        
        

        return response()->json(['path' => $videoPath, 'thumbnail' => $thumbnail]);
    }

    public function uploadMovieThumbnail()
    {
        $thumbnail = $this->uploadFile(request()->file('thumbnail_file'), 'uploads');
        return response()->json(['thumbnail' => $thumbnail]);
    }
}

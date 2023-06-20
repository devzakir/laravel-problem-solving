<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\User;
use App\Models\Creator\Movie;
use App\Models\Category;
use App\Models\Creator\MovieTag;
use App\Models\Creator\MovieCategory;
use App\Models\Creator\Tag;
use App\Models\Buyer\MovieFavorite;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\FileUpload;
use App\Enums\Role;
use Illuminate\Support\Facades\Storage;
/**
 * Class MovieService
 * @package App\Services
 */
class MovieService
{
    use FileUpload;
    
    /** @var int */
    private const PER_PAGE = 10;

    /**
     * @return LengthAwarePaginator
     */

    
    public function create($user, $attributes) {
			// movie create
			$movieAttributes = Arr::get($attributes, 'movie');

			$movieAttributes['creator_id'] = $user->id;
			$movieAttributes['is_public'] = Arr::get($movieAttributes, 'isPublic') ? 1 : 0;
			unset($movieAttributes['isPublic']);

			$movie = Movie::create($movieAttributes);

			// movie relation tags create
			$tags = Arr::get($attributes, 'tags');
			foreach($tags as $tag) {
				$check_tag = Tag::where('name', Arr::get($tag, 'text'))->first();

				if (!$check_tag) {
					$check_tag = Tag::create([
						'name' => Arr::get($tag, 'text')
					]);
				}

				MovieTag::create([
					'movie_id' => $movie->id,
					'tag_id'   => $check_tag->id,
					'user_id'  => $user->id
				]);
			}

			// movie relation category create
			MovieCategory::create([
				'movie_id' => $movie->id,
				'category_id' => Arr::get($attributes, 'category')
			]);

			return true;
		}

		public function getMovieTags($user) {
			$tags = MovieTag::where('user_id', $user->id)->with('tag')->get();
			return $tags;
		}

		public function getMovieCategories($user) {
			$categories = Category::get();
			return $categories;
		}

		public function getMovies($user) {
			return Movie::where('creator_id', $user->id)->where('is_blocked', 0)->with('comments', 'donatePayments', 'buyPayments', 'creator')->get();
		}

		public function getMoviesForBuyer($buyer) {
			return Movie::whereIn('id', function($query) use ($buyer) {
				$query->select('movie_id')
				->from(with(new Payment)->getTable())
				->where('buyer_id', $buyer->id)
				->where('type', 'buy');
			})->where('is_blocked', 0)
			->where('is_public', 1)
			->with('comments', 'donatePayments', 'creator', 'buyPayments')
			->get();
		}

		public function getMovieDetailForBuyer($id) {
			return Movie::where('id', $id)->with('comments', 'donatePayments', 'creator', 'favoriteBuyers', 'payBuyers', 'buyPayments')->first();
		}

		public function movieLikeProc($movie_id, $buyer) {
			$movie_favorite = MovieFavorite::where('movie_id', $movie_id)->where('buyer_id', $buyer->id)->first();

			if (is_null($movie_favorite)) {
				MovieFavorite::create([
					'movie_id' => $movie_id,
					'buyer_id' => $buyer->id
				]);
			} else {
				MovieFavorite::where('movie_id', $movie_id)->where('buyer_id', $buyer->id)->delete();
			}

			return true;
		}

		public function getMovieDetail($id) {
			return Movie::where('id', $id)->with('comments')->first();
		}

		public function getTags($id) {
			return MovieTag::where('movie_id', $id)->with('tag')->get();
		}

		public function getCategory($id) {
			return MovieCategory::where('movie_id', $id)->with('category')->first();
		}

		public function update($user, $attributes) {
			// movie create
			$movieAttributes = Arr::get($attributes, 'movie');

			$movieAttributes['is_public'] = Arr::get($movieAttributes, 'isPublic') ? 1 : 0;
			unset($movieAttributes['isPublic']);
			unset($movieAttributes['movie_url']);
			unset($movieAttributes['created_at']);
			unset($movieAttributes['updated_at']);
			unset($movieAttributes['thumbnail_file']);

			Movie::where('id', Arr::get($movieAttributes, 'id'))->update($movieAttributes);

			// movie relation tags update
			$tags = Arr::get($attributes, 'tags');

			MovieTag::where('movie_id', Arr::get($movieAttributes, 'id'))->delete();

			foreach($tags as $tag) {
				$check_tag = Tag::where('name', Arr::get($tag, 'text'))->first();

				if (!$check_tag) {
					$check_tag = Tag::create([
						'name' => Arr::get($tag, 'text')
					]);
				}

				MovieTag::create([
					'movie_id' => Arr::get($movieAttributes, 'id'),
					'tag_id'   => $check_tag->id,
					'user_id'  => $user->id
				]);
			}

			// delete current cateogries
			MovieCategory::where('movie_id', Arr::get($movieAttributes, 'id'))->delete();
			// movie relation category create
			MovieCategory::create([
				'movie_id' => Arr::get($movieAttributes, 'id'),
				'category_id' => Arr::get($attributes, 'category')
			]);

			return true;
		}

		// get home data
		public function getHomeData() {
			$payedMovies = [];
			if (!is_null(Auth::guard('api')->user())) {
				$payedMovies = Movie::whereIn('id', function($query) {
						$query->select('movie_id')
						->from(with(new Payment)->getTable())
						->where('buyer_id', Auth::guard('api')->user()->id)
						->where('type', 'buy');
					})->where('is_blocked', 0)
					->where('is_public', 1)
					->with('comments', 'donatePayments', 'creator', 'buyPayments')
					->orderByDesc('created_at')
					->get();
			}

			return [
				Movie::with('favoriteBuyers', 'comments', 'donatePayments', 'creator', 'buyPayments')
					->where('is_blocked', 0)
					->where('is_public', 1)
					->where('stick', 1)
					->get()
					->merge(
						Movie::with('favoriteBuyers', 'comments', 'donatePayments', 'creator', 'buyPayments')
						->where('is_blocked', 0)
						->where('is_public', 1)
						->where('stick', 0)
						->get()
						->sortByDesc(function($movie) {
							return $movie->favoriteBuyers->count();
					})->values()->all())->take(6),
				Tag::with('movieTags')->get()
					->filter(function($tag) {
						return $tag->movieTags->count() > 0; })
					->sortByDesc(function($tag) {
						return $tag->movieTags->count();
					})->values()->all(),
				$payedMovies
			];
		}

		public function deleteMovie($id) {
			Movie::where('id', $id)->delete();
		}

		public function getMoviesFree($hash) {
			$creator = User::where('hash', $hash)->first();
			return [Movie::with('favoriteBuyers', 'comments', 'donatePayments', 'creator', 'buyPayments')
					->where('is_blocked', 0)
					->where('is_public', 1)
					->whereIn('creator_id', function($query) use ($hash) {
						$query->select('id')
							  ->from(with(new User)->getTable())
							  ->where('hash', $hash);
					})
					->get()
					->sortByDesc(function($movie) {
						return $movie->favoriteBuyers->count();
					}), $creator];
		}

		public function search($attributes) {
			if (!is_null(Arr::get($attributes, 'tag_id'))) {
				return Movie::with('favoriteBuyers', 'comments', 'donatePayments', 'creator', 'buyPayments', 'movie_tags')
					->where('is_blocked', 0)
					->where('is_public', 1)
					->get()
					->sortByDesc(function($movie) {
						return $movie->favoriteBuyers->count();})
					->filter(function($movie) use ($attributes) {
						return in_array(Arr::get($attributes, 'tag_id'), $movie->movie_tags->pluck('tag_id')->toArray());
					})->values()->all();
			}

			if (!is_null(Arr::get($attributes, 'keyword'))) {
				return Movie::where('title', 'like', '%'.Arr::get($attributes, 'keyword').'%')
						->orWhere('description', 'like', '%'.Arr::get($attributes, 'keyword').'%')
						->where('is_blocked', 0)
						->where('is_public', 1)
						->with('favoriteBuyers', 'comments', 'donatePayments', 'creator', 'buyPayments')
						->get();
			}

			if (!is_null(Arr::get($attributes, 'creator_id'))) {
				return Movie::where('creator_id', Arr::get($attributes, 'creator_id'))
						->where('is_blocked', 0)
						->where('is_public', 1)
						->with('favoriteBuyers', 'comments', 'donatePayments', 'creator', 'buyPayments')
						->get();
			}

			if (is_null(Arr::get($attributes, 'tag_id')) && is_null(Arr::get($attributes, 'keyword'))) {
				return Movie::where('title', 'like', '%'.Arr::get($attributes, 'keyword').'%')
						->where('is_blocked', 0)
						->where('is_public', 1)
						->with('favoriteBuyers', 'comments', 'donatePayments', 'creator', 'buyPayments')
						->get();
			}

			return collect([]);
		}

		public function getAllMovies() {
			return Movie::orderByDesc('created_at')
					->where('is_blocked', 0)
					->with('buyPayments')->get();
		}

		public function getMovieDetailInfo($id) {
			return Movie::where('id', $id)->with('comments', 'movieCategory', 'movie_tags', 'creator', 'buyPayments')->first();
		}

		public function blockMovie($movie_id) {
			Movie::where('id', $movie_id)->update([
				'is_blocked' => 1
			]);

			return true;
		}

		public function pickupMovie($attributes) {
			$movie_id = $attributes['movie_id'];
			$state = $attributes['state'];

			Movie::where('id', $movie_id)->update([
				'stick' => ($state + 1) % 2
			]);
		}
}

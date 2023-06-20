<?php

namespace App\Http\Controllers\Recipienter;

use Illuminate\Http\Request;
use App\Services\InviteService;
use App\Jobs\EmailVerification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\User;
use App\Models\Admin;
use App\Models\Recipienter;
use App\Models\ProductColumn;
use App\Models\Product;
use App\Models\Permission;
use App\Models\OrderedProduct;
use App\Models\ProductValue;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;

class ProductController extends BaseController
{
	use FileUpload;

	public function getColumnInfo(Request $request) {
		$columns = ProductColumn::orderBy('order')->get();
		$recipienter = Auth::guard('recipienter')->user();
		$userColumns = ProductUserColumn::where('recipienter_id', $recipienter->id)->with('productColumn')->get();

		return response()->json([
			'columns' => $columns,
			'userColumns' => $userColumns
		]);
	}

	public function changeProductColumn(Request $request) {
		$recipienter = Auth::guard('recipienter')->user();
		$productUserColumn = ProductUserColumn::where('recipienter_id', $recipienter->id)->where('product_column_id', $request->input('id'))->first();
		if (is_null($productUserColumn)) {
			ProductUserColumn::create([
				'recipienter_id' => $recipienter->id,
				'product_column_id' => $request->input('id'),
				'order' => $request->input('order'),
				'picked' => $request->input('picked'),
				'nickname' => $request->input('name'),
				'tax' => $request->input('tax'),
				'tax_type' => $request->input('tax_type'),
			]);
		} else {
			$productUserColumn->order = $request->input('order');
			$productUserColumn->picked = $request->input('picked');
			$productUserColumn->nickname = $request->input('name');
			$productUserColumn->tax = $request->input('tax');
			$productUserColumn->tax_type = $request->input('tax_type');
			$productUserColumn->save();
		}
		return response()->json([
			'flag' => true
		]);
	}

	public function saveProducts(Request $request) {
		$recipienter = Auth::guard('recipienter')->user();
		$product = Product::create([
			'is_public' => $request->input('is_public'),
			'mark' => $request->input('mark'),
			'tax' => $request->input('tax'),
			'tax_type' => $request->input('tax_type'),
			'recipienter_id' => $recipienter->id
		]);

		if (!is_null($request->file('image'))) {
			$column = ProductColumn::where('slug', 'image')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			$url = $this->uploadFile($request->file('image'), 'upload');
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $url
			]);
		}

		if (!is_null($request->input('name'))) {
			$column = ProductColumn::where('slug', 'name')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('name')
			]);
		}

		if (!is_null($request->input('price'))) {
			$column = ProductColumn::where('slug', 'price')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('price')
			]);
		}

		if (!is_null($request->input('amount'))) {
			$column = ProductColumn::where('slug', 'amount')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('amount')
			]);
		}
		
		if (!is_null($request->input('total_price'))) {
			$column = ProductColumn::where('slug', 'total_price')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('total_price')
			]);
		}

		if (!is_null($request->input('remark'))) {
			$column = ProductColumn::where('slug', 'remark')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('remark')
			]);
		}

		if (!is_null($request->input('color'))) {
			$column = ProductColumn::where('slug', 'color')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('color')
			]);
		}

		if (!is_null($request->input('size'))) {
			$column = ProductColumn::where('slug', 'size')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('size')
			]);
		}

		if (!is_null($request->input('unit'))) {
			$column = ProductColumn::where('slug', 'unit')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('unit')
			]);
		}

		if (!is_null($request->input('jan'))) {
			$column = ProductColumn::where('slug', 'jan')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('jan')
			]);
		}

		if (!is_null($request->input('order'))) {
			$column = ProductColumn::where('slug', 'order')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('order')
			]);
		}

		if (!is_null($request->input('free_space1'))) {
			$column = ProductColumn::where('slug', 'free_space1')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('free_space1')
			]);
		}

		if (!is_null($request->input('free_space2'))) {
			$column = ProductColumn::where('slug', 'free_space2')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('free_space2')
			]);
		}

		if (!is_null($request->input('free_space3'))) {
			$column = ProductColumn::where('slug', 'free_space3')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $product->id,
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('free_space3')
			]);
		}

		return response()->json([
			'flag' => true
		]);
	}

	public function updateProducts(Request $request) {
		$recipienter = Auth::guard('recipienter')->user();
		Product::where('id', $request->input('id'))->update([
			'is_public' => $request->input('is_public'),
			'tax' => $request->input('tax'),
			'tax_type' => $request->input('tax_type'),
			'mark' => $request->input('mark'),
		]);

		ProductValue::where('product_id', $request->input('id'))->delete();

		if ($request->input('image_changed') == 1) {
			if (!is_null($request->file('image'))) {
				$column = ProductColumn::where('slug', 'image')->first();
				$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
				$url = $this->uploadFile($request->file('image'), 'upload');
				ProductValue::create([
					'product_id' => $request->input('id'),
					'product_user_column_id' => $user_column->id,
					'value' => $url
				]);
			}
		}

		if (!is_null($request->input('name'))) {
			$column = ProductColumn::where('slug', 'name')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('name')
			]);
		}

		if (!is_null($request->input('price'))) {
			$column = ProductColumn::where('slug', 'price')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('price')
			]);
		}

		if (!is_null($request->input('amount'))) {
			$column = ProductColumn::where('slug', 'amount')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('amount')
			]);
		}
		
		if (!is_null($request->input('total_price'))) {
			$column = ProductColumn::where('slug', 'total_price')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('total_price')
			]);
		}

		if (!is_null($request->input('remark'))) {
			$column = ProductColumn::where('slug', 'remark')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('remark')
			]);
		}

		if (!is_null($request->input('color'))) {
			$column = ProductColumn::where('slug', 'color')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('color')
			]);
		}

		if (!is_null($request->input('size'))) {
			$column = ProductColumn::where('slug', 'size')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('size')
			]);
		}

		if (!is_null($request->input('unit'))) {
			$column = ProductColumn::where('slug', 'unit')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('unit')
			]);
		}

		if (!is_null($request->input('jan'))) {
			$column = ProductColumn::where('slug', 'jan')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('jan')
			]);
		}

		if (!is_null($request->input('order'))) {
			$column = ProductColumn::where('slug', 'order')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('order')
			]);
		}

		if (!is_null($request->input('free_space1'))) {
			$column = ProductColumn::where('slug', 'free_space1')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('free_space1')
			]);
		}

		if (!is_null($request->input('free_space2'))) {
			$column = ProductColumn::where('slug', 'free_space2')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('free_space2')
			]);
		}

		if (!is_null($request->input('free_space3'))) {
			$column = ProductColumn::where('slug', 'free_space3')->first();
			$user_column = ProductUserColumn::where('product_column_id', $column->id)->where('recipienter_id', $recipienter->id)->first();
			ProductValue::create([
				'product_id' => $request->input('id'),
				'product_user_column_id' => $user_column->id,
				'value' => $request->input('free_space3')
			]);
		}

		return response()->json([
			'flag' => true
		]);
	}

	public function getProductList(Request $request) {
		$recipienter = Auth::guard('recipienter')->user();
		$query = Product::where('recipienter_id', $recipienter->id);
		if (!is_null($request->input('keyword'))) {
			$keyword = $request->input('keyword');
			$query->whereIn('id', function($qry) use ($keyword) {
				$qry->select('product_id')
					->from(with(new ProductValue)->getTable())
					->where('value', 'like', '%'.$keyword.'%');
			});
		}
		if (!is_null($request->input('fromDate'))) {
			$fromDate = $request->input('fromDate');
			$query->where('created_at', '>', $fromDate);
		}
		if (!is_null($request->input('toDate'))) {
			$toDate = $request->input('toDate');
			$query->where('created_at', '<', $toDate);
		}
		if (!is_null($request->input('status'))) {
			$status = $request->input('status');
			$query->where('is_public', $status);
		}
		if (!is_null($request->input('mark'))) {
			$mark = $request->input('mark');
			$query->where('mark', $mark);
		}
		$column = ProductColumn::where('slug', 'amount')->first();
		$userColumn = ProductUserColumn::where('recipienter_id', $recipienter->id)->where('product_column_id', $column->id)->first();
		if (!is_null($request->input('fromAmount'))) {
			$fromAmount = $request->input('fromAmount');
			$query->whereIn('id', function($qry) use ($fromAmount, $userColumn) {
				$qry->select('product_id')
					->from(with(new ProductValue)->getTable())
					->where('product_user_column_id', $userColumn->id)
					->whereRaw('CAST(`value` AS SIGNED) >= ?', [(int)$fromAmount]);
			});
		}
		if (!is_null($request->input('toAmount'))) {
			$toAmount = $request->input('toAmount');
			$query->whereIn('id', function($qry) use ($toAmount, $userColumn) {
				$qry->select('product_id')
					->from(with(new ProductValue)->getTable())
					->where('product_user_column_id', $userColumn->id)
					->whereRaw('CAST(`value` AS SIGNED) <= ?', [(int)$toAmount]);
			});
		}
		$products = $query->orderByDesc('created_at')->with('productValues')->get();

		$recipienter_id = $recipienter->id;
		$query1 = Product::whereIn('recipienter_id', function($qry) use ($recipienter_id) {
			$qry->select('id')
			->from(with(new Recipienter)->getTable())
			->where('parent_id', $recipienter_id);
		});
		if (!is_null($request->input('keyword'))) {
			$keyword = $request->input('keyword');
			$query1->whereIn('id', function($qry) use ($keyword) {
				$qry->select('product_id')
					->from(with(new ProductValue)->getTable())
					->where('value', 'like', '%'.$keyword.'%');
			});
		}
		if (!is_null($request->input('fromDate'))) {
			$fromDate = $request->input('fromDate');
			$query1->where('created_at', '>', $fromDate);
		}
		if (!is_null($request->input('toDate'))) {
			$toDate = $request->input('toDate');
			$query1->where('created_at', '<', $toDate);
		}
		if (!is_null($request->input('status'))) {
			$status = $request->input('status');
			$query1->where('is_public', $status);
		}
		$column = ProductColumn::where('slug', 'amount')->first();
		$userColumn = ProductUserColumn::where('recipienter_id', $recipienter->id)->where('product_column_id', $column->id)->first();
		if (!is_null($request->input('fromAmount'))) {
			$fromAmount = $request->input('fromAmount');
			$query1->whereIn('id', function($qry) use ($fromAmount, $userColumn) {
				$qry->select('product_id')
					->from(with(new ProductValue)->getTable())
					->where('product_user_column_id', $userColumn->id)
					->whereRaw('CAST(`value` AS SIGNED) >= ?', [(int)$fromAmount]);
			});
		}
		if (!is_null($request->input('toAmount'))) {
			$toAmount = $request->input('toAmount');
			$query1->whereIn('id', function($qry) use ($toAmount, $userColumn) {
				$qry->select('product_id')
					->from(with(new ProductValue)->getTable())
					->where('product_user_column_id', $userColumn->id)
					->whereRaw('CAST(`value` AS SIGNED) <= ?', [(int)$toAmount]);
			});
		}
		$products1 = $query1->orderByDesc('created_at')->with('productValues')->get();
		$products_merged = $products->merge($products1);

		$permission = Permission::where('recipienter_id', $recipienter->id)->where('moduleNum', 5)->first();
		if (!is_null($permission)) {
			if ($permission->show == 1) {
				if (!is_null($recipienter->recipienter_id)) {
					$query2 = Product::where('recipienter_id', $recipienter->recipienter_id);
					if (!is_null($request->input('keyword'))) {
						$keyword = $request->input('keyword');
						$query2->whereIn('id', function($qry) use ($keyword) {
							$qry->select('product_id')
								->from(with(new ProductValue)->getTable())
								->where('value', 'like', '%'.$keyword.'%');
						});
					}
					if (!is_null($request->input('fromDate'))) {
						$fromDate = $request->input('fromDate');
						$query2->where('created_at', '>', $fromDate);
					}
					if (!is_null($request->input('toDate'))) {
						$toDate = $request->input('toDate');
						$query2->where('created_at', '<', $toDate);
					}
					if (!is_null($request->input('status'))) {
						$status = $request->input('status');
						$query2->where('is_public', $status);
					}
					$column = ProductColumn::where('slug', 'amount')->first();
					$userColumn = ProductUserColumn::where('recipienter_id', $recipienter->id)->where('product_column_id', $column->id)->first();
					if (!is_null($request->input('fromAmount'))) {
						$fromAmount = $request->input('fromAmount');
						$query2->whereIn('id', function($qry) use ($fromAmount, $userColumn) {
							$qry->select('product_id')
								->from(with(new ProductValue)->getTable())
								->where('product_user_column_id', $userColumn->id)
								->whereRaw('CAST(`value` AS SIGNED) >= ?', [(int)$fromAmount]);
						});
					}
					if (!is_null($request->input('toAmount'))) {
						$toAmount = $request->input('toAmount');
						$query2->whereIn('id', function($qry) use ($toAmount, $userColumn) {
							$qry->select('product_id')
								->from(with(new ProductValue)->getTable())
								->where('product_user_column_id', $userColumn->id)
								->whereRaw('CAST(`value` AS SIGNED) <= ?', [(int)$toAmount]);
						});
					}
					$products2 = $query2->with('productValues')->get();
				}
			}
		}

		if (isset($products2)) {
			$products_merged1 = $products_merged->merge($products2);
		} else {
			$products_merged1 = $products_merged;
		}

		$columns = ProductColumn::orderBy('order')->get();
		$userColumns = ProductUserColumn::where('recipienter_id', $recipienter->id)->with('productColumn')->get();
		$orderedProducts = OrderedProduct::where('recipienter_id', $recipienter->id)->where('order_form_id', $request->input('order_form_id'))->get();

		return response()->json([
			'products' => $products_merged1,
			'columns' => $columns,
			'userColumns' => $userColumns,
			'orderedProducts' => $orderedProducts
		]);
	}

	public function deleteProduct(Request $request) {
		Product::where('id', $request->input('id'))->delete();

		return response()->json([
			'flag' => true
		]);
	}

	public function columnPrepare(Request $request) {
		$recipienter = Auth::guard('recipienter')->user();
		$productUserColumn = ProductUserColumn::where('recipienter_id', $recipienter->id)->first();
		if (is_null($productUserColumn)) {
			$product_columns = ProductColumn::get();
			foreach($product_columns as $product_column) {
				ProductUserColumn::create([
					'recipienter_id' => $recipienter->id,
					'product_column_id' => $product_column->id,
					'order' => $product_column->order,
					'picked' => $product_column->picked,
				]);
			}
		}

		return response()->json([
			'flag' => true
		]);
	}

	public function getProductEditScreenInit(Request $request) {
		$columns = ProductColumn::orderBy('order')->get();
		$recipienter = Auth::guard('recipienter')->user();
		$userColumns = ProductUserColumn::where('recipienter_id', $recipienter->id)->with('productColumn')->get();
		$products = Product::where('id', $request->input('id'))->with('productValues')->get();

		return response()->json([
			'columns' => $columns,
			'userColumns' => $userColumns,
			'products' => $products
		]);
	}
}
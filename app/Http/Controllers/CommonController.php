<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\FileUpload;
use App\Models\History;
use App\Models\HorseHistory;
use App\User;

class CommonController extends Controller
{
  use FileUpload;

  public function importJsonData(Request $request) {
    $date = $request->input('date');
    $file = $this->uploadFile(request()->file('file'), 'uploads');

    $json = Storage::disk('public')->get($file);
    $json = json_decode($json, true);

    foreach($json as $data) {
      History::create([
        'field_code' => $data['場コード'],
        'date' => $date,
        'race_key' => $data['レースキー'],
        'departure_time' => $data['発走時間'],
        'distance' => $data['距離'],
        'fault_code' => $data['芝ダ障害コード'],
        'classification' => $data['種別'],
        'condition' => $data['条件'],
        'symbol' => $data['記号'],
        'weight' => $data['重量'],
        'race_name' => $data['レース名'],
        'race_name_abr' => $data['レース名９文字'],
        'count' => $data['頭数'],
        'weather_code' => $data['天候コード'],
        'prize1' => $data['１着賞金'],
        'prize2' => $data['２着賞金'],
        'prize3' => $data['３着賞金'],
        'prize4' => $data['４着賞金'],
        'prize5' => $data['５着賞金'],
      ]);
    }

    return response()->json([
      'flag' => true,
      'data' => $json
    ]);
  }

  public function getHomeData(Request $request) {
    $histories = History::get();
    $dates = array();
    foreach($histories as $item) {
      if (!in_array($item->date, $dates)) {
        array_push($dates, $item->date);
      }
    }

    sort($dates);
    if (sizeof($dates) >= 2) {
      $dates1 = array($dates[sizeof($dates)-2], $dates[sizeof($dates)-1]);
    } else if (sizeof($dates) == 1) {
      $dates1 = array($dates[0]);
    } else {
      $dates1 = array();
    }

    return response()->json([
      'histories' => $histories,
      'dates' => $dates1
    ]);
  }

  public function importTxtFile(Request $request) {
    $file = $this->uploadFile(request()->file('file'), 'uploads');

    $file = \fopen('storage/'.$file, 'r');
    $horse_lists = [];
    while( !\feof($file)) {
      $data = \fread($file, 388);
      $horseName = trim(mb_strcut($data, 56, 36));
      $raceKey = trim(mb_strcut($data, 0, 8));
      $playerName = trim(mb_strcut($data, 96, 12));
      $playerWeight = trim(mb_strcut($data, 108, 3));
      $horseSex = trim(mb_strcut($data, 92, 1));
      if ($horseName !== "") {
        HorseHistory::create([
          'name' => mb_convert_encoding($horseName, 'UTF-8', 'SHIFT_JIS'),
          'racekey' => mb_convert_encoding($raceKey, 'UTF-8', 'SHIFT_JIS'),
          'player_name' => mb_convert_encoding($playerName, 'UTF-8', 'SHIFT_JIS'),
          'player_weight' => mb_convert_encoding($playerWeight, 'UTF-8', 'SHIFT_JIS'),
          'horse_sex' => mb_convert_encoding($horseSex, 'UTF-8', 'SHIFT_JIS'),
        ]);
      }
    }

    return response()->json([
      'flag' => true
    ]);
  }

  public function getRaceDetail(Request $request) {
    $horses = HorseHistory::where('racekey', $request->input('race_key'))->get();

    return response()->json([
      'horses' => $horses
    ]);
  }

  public function checkAuth(Request $request) {
    $user = User::where('name', $request->input('device_id'))->first();

    return response()->json([
      'user' => $user
    ]);
  }

  public function saveAuthInformation(Request $request) {
    $user = User::where('name', $request->input('device_id'))->first();

    if (!is_null($user)) {
      User::where('name', $request->input('device_id'))->update([
        'uno' => $request->input('uno'),
        'pin' => $request->input('pin'),
        'pno' => $request->input('pno'),
      ]);
    } else {
      User::create([
        'name' => $request->input('device_id'),
        'uno' => $request->input('uno'),
        'pin' => $request->input('pin'),
        'pno' => $request->input('pno'),
      ]);
    }

    return response()->json([
      'flag' => true
    ]);
  }
}
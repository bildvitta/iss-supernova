<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::get('supernova/trigger-event', function (\Illuminate\Http\Request $request) {

        try {
            $event = $request->input('event'); //eloquent.updated: App\User
            $modelName = $request->input('model');
            $id = $request->input('id');

            if ($model = $modelName::find($id)) {
                event($event, $model);
            }
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => '500',
                'error' => (string)$exception,
            ], 500);
        }

        return response()->json([
            'status' => '200',
        ]);
    });
});

<?php
// app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingFormRequest;
use App\Models\Setting;
use Illuminate\Support\Arr;

class SettingController extends Controller
{
{
        // 1. Lấy dữ liệu đã validate
        $data = Arr::dot($request->validated());

        // 2. Sử dụng Transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                // Ép kiểu chuẩn xác cho database (sqlite/mysql/pgsql)
                $formattedValue = is_bool($value) ? ($value ? '1' : '0') : $value;

                // Sử dụng updateOrCreate để tối ưu logic (vừa cập nhật vừa thêm mới nếu thiếu)
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $formattedValue]
                );
            }
        });

        // 3. Xóa cache cài đặt (nếu bạn có dùng cache ở tầng Model hoặc Global Middleware)
        // Cache::forget('settings'); 

        return back()->with('ok', 'Cài đặt hệ thống đã được cập nhật thành công.');
    }
}

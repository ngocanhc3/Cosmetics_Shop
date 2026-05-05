<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRateRequest;
use App\Models\{ShippingRate, ShippingCarrier, ShippingZone};
use Illuminate\Http\Request;

/**
 * Quản lý biểu phí vận chuyển (Shipping Rate) trong trang Admin.
 * Mỗi biểu phí gắn với một hãng vận chuyển và một khu vực địa lý.
 *
 * @author Nguyen Thanh Nga
 */
class ShippingRateController extends Controller
{
    /**
     * Hiển thị danh sách biểu phí kèm thông tin carrier và zone.
     * Load sẵn carrierOptions và zoneOptions cho bộ lọc trên giao diện.
     */
    public function index(Request $r)
    {
        $rates = ShippingRate::query()
            ->with(['carrier:id,name,code', 'zone:id,name'])
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $carrierOptions = ShippingCarrier::orderBy('sort_order')
            ->get(['id', 'name', 'code'])
            ->mapWithKeys(fn($c) => [$c->id => $c->name . ' (' . $c->code . ')'])
            ->all();

        $zoneOptions = ShippingZone::orderBy('name')->pluck('name', 'id')->all();

        return view('admin.shipping.rates.index', compact('rates', 'carrierOptions', 'zoneOptions'));
    }

    /**
     * Hiển thị form tạo mới biểu phí.
     * Load danh sách carrier và zone để hiển thị dropdown.
     */
    public function create()
    {
        $rate           = new ShippingRate();
        $carrierOptions = ShippingCarrier::orderBy('sort_order')->pluck('name', 'id');
        $zoneOptions    = ShippingZone::orderBy('name')->pluck('name', 'id');
        return view('admin.shipping.rates.form', compact('rate', 'carrierOptions', 'zoneOptions'));
    }

    /**
     * Hiển thị form chỉnh sửa biểu phí.
     * Sử dụng Route Model Binding để tự động tìm rate theo ID.
     */
    public function edit(ShippingRate $rate)
    {
        $carrierOptions = ShippingCarrier::orderBy('sort_order')->pluck('name', 'id');
        $zoneOptions    = ShippingZone::orderBy('name')->pluck('name', 'id');
        return view('admin.shipping.rates.form', compact('rate', 'carrierOptions', 'zoneOptions'));
    }

    /**
     * Lưu biểu phí mới vào database.
     */
    public function store(StoreRateRequest $req)
    {
        $data            = $req->validated();
        $data['enabled'] = $req->boolean('enabled');
        ShippingRate::create($data);
        return back()->with('ok', 'Đã thêm biểu phí.');
    }

    /**
     * Cập nhật biểu phí.
     * Hỗ trợ 2 chế độ: cập nhật đầy đủ hoặc chỉ toggle trạng thái enabled.
     */
    public function update(Request $req, ShippingRate $rate)
    {
        if ($req->has('enabled') && !$req->has('name')) {
            $rate->update(['enabled' => $req->boolean('enabled')]);
            return back()->with('ok', 'Đã cập nhật biểu phí.');
        }

        $data            = $req->validate((new StoreRateRules)->rules());
        $data['enabled'] = $req->boolean('enabled');
        $rate->update($data);
        return back()->with('ok', 'Đã cập nhật biểu phí.');
    }

    /**
     * Bật hoặc tắt nhanh trạng thái biểu phí.
     */
    public function toggle(Request $r, ShippingRate $rate)
    {
        $rate->enabled = $r->has('enabled') ? $r->boolean('enabled') : !$rate->enabled;
        $rate->save();
        return back()->with('ok', $rate->enabled ? 'Đã bật biểu phí.' : 'Đã tắt biểu phí.');
    }

    /**
     * Xoá vĩnh viễn biểu phí khỏi database.
     */
    public function destroy(ShippingRate $rate)
    {
        $rate->delete();
        return back()->with('ok', 'Đã xóa biểu phí.');
    }
}
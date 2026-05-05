<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarrierRequest;
use App\Models\ShippingCarrier;
use Illuminate\Http\Request;

/**
 * Quản lý đơn vị vận chuyển (Carrier) trong trang Admin.
 * Hỗ trợ CRUD đầy đủ, tìm kiếm theo tên/mã và bật/tắt nhanh.
 *
 * @author Nguyen Thanh Nga
 */
class ShippingCarrierController extends Controller
{
    /**
     * Hiển thị danh sách carrier, hỗ trợ tìm kiếm theo tên hoặc mã.
     * Kết quả phân trang 12 item/trang.
     */
    public function index(Request $r)
    {
        $carriers = ShippingCarrier::query()
            ->when($r->search, fn($q, $s) => $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
            }))
            ->orderBy('sort_order')->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.shipping.carriers.index', compact('carriers'));
    }

    /**
     * Hiển thị form tạo mới carrier.
     */
    public function create()
    {
        return view('admin.shipping.carriers.form', ['carrier' => new ShippingCarrier]);
    }

    /**
     * Lưu carrier mới vào database.
     * Xử lý thêm: ép kiểu boolean cho supports_cod và enabled,
     * decode JSON cho trường config nếu gửi lên dạng string.
     */
    public function store(StoreCarrierRequest $req)
    {
        $data = $req->validated();
        $data['supports_cod'] = $req->boolean('supports_cod');
        $data['enabled']      = $req->boolean('enabled');

        if (isset($data['config']) && is_string($data['config'])) {
            $json = json_decode($data['config'], true);
            if (json_last_error() === JSON_ERROR_NONE) $data['config'] = $json;
            else unset($data['config']);
        }

        ShippingCarrier::create($data);
        return back()->with('ok', 'Đã tạo đơn vị vận chuyển.');
    }

    /**
     * Hiển thị form chỉnh sửa carrier.
     * Sử dụng Route Model Binding để tự động tìm carrier theo ID.
     */
    public function edit(ShippingCarrier $carrier)
    {
        return view('admin.shipping.carriers.form', compact('carrier'));
    }

    /**
     * Cập nhật thông tin carrier.
     * Xử lý tương tự store(): ép kiểu boolean và decode config JSON.
     */
    public function update(StoreCarrierRequest $req, ShippingCarrier $carrier)
    {
        $data = $req->validated();
        $data['supports_cod'] = $req->boolean('supports_cod');
        $data['enabled']      = $req->boolean('enabled');

        if (isset($data['config']) && is_string($data['config'])) {
            $json = json_decode($data['config'], true);
            if (json_last_error() === JSON_ERROR_NONE) $data['config'] = $json;
            else unset($data['config']);
        }

        $carrier->update($data);
        return back()->with('ok', 'Đã cập nhật.');
    }

    /**
     * Bật hoặc tắt nhanh trạng thái carrier.
     * Nếu form gửi kèm enabled thì dùng giá trị đó, ngược lại thì đảo trạng thái.
     */
    public function toggle(Request $r, ShippingCarrier $carrier)
    {
        $carrier->enabled = $r->has('enabled') ? $r->boolean('enabled') : !$carrier->enabled;
        $carrier->save();
        return back()->with('ok', $carrier->enabled ? 'Đã bật đơn vị.' : 'Đã tắt đơn vị.');
    }

    /**
     * Xoá vĩnh viễn carrier khỏi database.
     */
    public function destroy(ShippingCarrier $carrier)
    {
        $carrier->delete();
        return back()->with('ok', 'Đã xóa.');
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::pending()->count(),
            'processing_orders' => Order::processing()->count(),
            'completed_orders' => Order::delivered()->count(),
            'today_revenue' => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total'),
            'month_revenue' => Order::whereMonth('created_at', now()->month)->where('payment_status', 'paid')->sum('total'),
        ];

        $recent_orders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        $low_stock_products = Product::where('stock_quantity', '<', 10)
            ->where('is_active', true)
            ->take(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'low_stock_products' => $low_stock_products,
        ]);
    }

    public function users(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'phone', 'is_admin', 'status', 'avatar', 'created_at')
            ->withCount('orders')
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'is_admin' => 'boolean',
            'status' => 'in:active,suspended',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        return response()->json(['message' => 'User created!', 'user' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'is_admin' => 'boolean',
            'status' => 'in:active,suspended',
        ]);

        $user->update($validated);
        return response()->json(['message' => 'User updated!', 'user' => $user]);
    }

    public function orders(Request $request)
    {
        $query = Order::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        return response()->json($orders);
    }

    // Public settings
    public function settings()
    {
        $settings = Setting::whereIn('group', ['shipping', 'site'])->get();
        $result = [];
        foreach ($settings as $s) {
            $result[$s->key] = match ($s->type) {
                'number' => (float) $s->value,
                'boolean' => filter_var($s->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($s->value, true),
                default => $s->value,
            };
        }
        return response()->json($result);
    }

    // Admin settings
    public function allSettings()
    {
        return response()->json(Setting::all()->groupBy('group'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required',
            'logo_file' => 'nullable|image|max:2048',
        ]);

        // Parse settings (may be JSON string when sent via FormData)
        $settings = $request->input('settings');
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }

        if (!is_array($settings)) {
            return response()->json(['message' => 'Invalid settings format'], 422);
        }

        // Handle logo upload
        if ($request->hasFile('logo_file')) {
            $logoPath = '/storage/' . $request->file('logo_file')->store('logos', 'public');
            Setting::set('site_logo', $logoPath, 'string', 'site', 'Website Logo');
        }

        foreach ($settings as $item) {
            Setting::set($item['key'], $item['value'], $item['type'], $item['group'], $item['label'] ?? null);
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function uploadSlideImage(Request $request)
    {
        $request->validate([
            'slide_image' => 'required|image|max:2048',
        ]);

        $path = '/storage/' . $request->file('slide_image')->store('slides', 'public');
        return response()->json(['url' => $path]);
    }

    // Reports
    public function salesReport(Request $request)
    {
        $period = $request->get('period', 'month');
        $days = $period === 'year' ? 365 : ($period === 'month' ? 30 : 7);

        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            $dateRaw = "TO_CHAR(created_at, '" . ($period === 'year' ? 'YYYY-MM' : 'YYYY-MM-DD') . "') as date";
        } elseif ($driver === 'mysql') {
            $dateRaw = "DATE_FORMAT(created_at, '" . ($period === 'year' ? '%Y-%m' : '%Y-%m-%d') . "') as date";
        } else {
            $format = $period === 'year' ? '%Y-%m' : '%Y-%m-%d';
            $dateRaw = "strftime('" . $format . "', created_at) as date";
        }

        $sales = Order::where('created_at', '>=', now()->subDays($days))
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw($dateRaw),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $summary = [
            'total_revenue' => Order::where('status', '!=', 'cancelled')->where('created_at', '>=', now()->subDays($days))->sum('total'),
            'total_orders' => Order::where('status', '!=', 'cancelled')->where('created_at', '>=', now()->subDays($days))->count(),
            'average_order' => Order::where('status', '!=', 'cancelled')->where('created_at', '>=', now()->subDays($days))->avg('total'),
        ];

        return response()->json([
            'sales' => $sales,
            'summary' => $summary,
        ]);
    }

    public function ordersReport(Request $request)
    {
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $paymentCounts = Order::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status');

        $topProducts = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        return response()->json([
            'status_counts' => $statusCounts,
            'payment_counts' => $paymentCounts,
            'top_products' => $topProducts,
        ]);
    }

    public function coupons()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return response()->json($coupons);
    }

    public function createCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $coupon = Coupon::create($validated);
        return response()->json(['message' => 'Coupon created!', 'coupon' => $coupon]);
    }

    public function updateCoupon(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|unique:coupons,code,' . $id,
            'description' => 'nullable|string',
            'type' => 'sometimes|in:percentage,fixed',
            'value' => 'sometimes|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);
        return response()->json(['message' => 'Coupon updated!', 'coupon' => $coupon]);
    }

    public function deleteCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(['message' => 'Coupon deleted!']);
    }

    public function exportDb()
    {
        $db = config('database.connections.pgsql');
        $host = $db['host'];
        $port = $db['port'];
        $database = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $tempPath = storage_path('app/' . $filename);

        $env = 'PGPASSWORD=' . escapeshellarg($password);
        $cmd = sprintf(
            '%s pg_dump -h %s -p %s -U %s -d %s -f %s 2>&1',
            $env,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($tempPath)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($tempPath)) {
            return response()->json(['message' => 'Export failed: ' . implode("\n", $output)], 500);
        }

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(true);
    }

    public function importDb(Request $request)
    {
        $request->validate(['sql_file' => 'required|file|mimes:sql,txt']);

        $file = $request->file('sql_file');
        $tempPath = $file->storeAs('temp', 'import_' . time() . '.sql');
        $fullPath = storage_path('app/' . $tempPath);

        $db = config('database.connections.pgsql');
        $host = $db['host'];
        $port = $db['port'];
        $database = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        $env = 'PGPASSWORD=' . escapeshellarg($password);
        $cmd = sprintf(
            '%s psql -h %s -p %s -U %s -d %s -f %s 2>&1',
            $env,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );

        exec($cmd, $output, $returnCode);
        @unlink($fullPath);

        if ($returnCode !== 0) {
            return response()->json(['message' => 'Import failed: ' . implode("\n", $output)], 500);
        }

        return response()->json(['message' => 'Database imported successfully!']);
    }
}

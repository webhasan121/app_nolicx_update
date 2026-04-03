<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class SystemDashboardOverview
{
    public static function get(): array
    {
        $stats = collect(DB::select("
            SELECT 'users' as type, COUNT(*) as total FROM users
            UNION ALL
            SELECT 'vendors', COUNT(*) FROM vendors
            UNION ALL
            SELECT 'active_vendors', COUNT(*) FROM vendors WHERE status = 'active'
            UNION ALL
            SELECT 'resellers', COUNT(*) FROM resellers
            UNION ALL
            SELECT 'active_resellers', COUNT(*) FROM resellers WHERE status = 'active'
            UNION ALL
            SELECT 'riders', COUNT(*) FROM riders
            UNION ALL
            SELECT 'active_riders', COUNT(*) FROM riders WHERE status = 'active'
            UNION ALL
            SELECT 'admin_users', COUNT(*)
                FROM users
                WHERE id IN (SELECT model_id FROM model_has_roles WHERE role_id = (SELECT id FROM roles WHERE name = 'admin'))
            UNION ALL
            SELECT 'products', COUNT(*) FROM products
            UNION ALL
            SELECT 'categories', COUNT(*) FROM categories
        "));

        $map = $stats->pluck('total', 'type');

        return [
            'userCount' => $map['users'] ?? 0,
            'vd' => $map['vendors'] ?? 0,
            'avd' => $map['active_vendors'] ?? 0,
            'rs' => $map['resellers'] ?? 0,
            'ars' => $map['active_resellers'] ?? 0,
            'ri' => $map['riders'] ?? 0,
            'ari' => $map['active_riders'] ?? 0,
            'adm' => $map['admin_users'] ?? 0,
            'vp' => $map['products'] ?? 0,
            'cat' => $map['categories'] ?? 0,
        ];
    }
}

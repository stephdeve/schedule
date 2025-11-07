<?php

namespace App\Helpers;

class DepartmentColors
{
    public static function getColor(string $department): array
    {
        $colors = [
            'informatique' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'hex' => '#3B82F6'],
            'mathématiques' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'hex' => '#10B981'],
            'physique' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-300', 'hex' => '#8B5CF6'],
            'chimie' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'border' => 'border-amber-300', 'hex' => '#F59E0B'],
            'biologie' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'hex' => '#059669'],
            'histoire' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'border' => 'border-rose-300', 'hex' => '#F43F5E'],
            'default' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'hex' => '#6B7280']
        ];

        $key = strtolower($department);
        return $colors[$key] ?? $colors['default'];
    }
}

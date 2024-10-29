<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome');
    }

    // php artisan tinker
    // Cache::get('users_list_' . md5(json_encode(['name' => 'Christian André Steffens', 'email' => 'oficialsteffens@hotmail.com'])));

    public function filter(Request $request)
    {
        $filters = $request->only('name', 'email');
        $perPage = $request->get('perPage', 5);
        $page = $request->get('page', 1);

        $cacheKey = 'users_list_' . md5(json_encode($filters)) . "_page_{$page}_perPage_{$perPage}";

        if (Cache::has($cacheKey)) {
            Log::info('Dados carregados do cache para a chave: ' . $cacheKey);
        } else {
            Log::info('Nenhum dado encontrado no cache, carregando do banco de dados.');
        }

        $users = Cache::remember($cacheKey, 60, function () use ($filters, $perPage) {
            $query = User::with('posts');

            if (!empty($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }

            if (!empty($filters['email'])) {
                $query->where('email', 'like', '%' . $filters['email'] . '%');
            }

            return $query->paginate($perPage);
        });

        return response()->json([
            'users' => $users,
            'filters' => $filters
        ]);
    }
}

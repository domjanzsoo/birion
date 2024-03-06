<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Contract\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    public function getAllUsers(): JsonResponse
    {
        return response()->json(['data' =>$this->repository->getAll()]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Contract\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;

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
        return response()->json(['data' => $this->repository->getAll()]);
    }
}
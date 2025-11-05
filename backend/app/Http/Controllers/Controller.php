<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Schedule API",
 *     version="1.0.0",
 *     description="API de gestion des emplois du temps universitaires"
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="Base path des routes API"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     name="Authorization",
 *     in="header"
 * )
 */
abstract class Controller
{
    //
}

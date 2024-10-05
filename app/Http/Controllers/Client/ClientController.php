<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Repositories\Client\ClientRepository;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function __construct(private readonly ClientRepository $clientRepository)
    {
    }

    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Get all clients",
     *     tags={"Client"},
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              default=1
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Number of items per page",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              default=10
     *          )
     *      ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="List of clients",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="clients", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="email", type="string"),
     *                      @OA\Property(property="phone", type="string"),
     *                      @OA\Property(property="address", type="array",
     *                          @OA\Items(
     *                              @OA\Property(property="street", type="string"),
     *                              @OA\Property(property="number", type="string"),
     *                              @OA\Property(property="complement", type="string"),
     *                              @OA\Property(property="city", type="string"),
     *                              @OA\Property(property="state", type="string"),
     *                              @OA\Property(property="zip_code", type="string"),
     *                          )
     *                      ),
     *                  )
     *             )
     *         )
     *    ),
     *    security={{"sanctum":{}}},
     * )
     *
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $clients = $this->clientRepository->getClientWithAddress($perPage, ['*'], 'page', $page);

        return response()->json([
            'clients' => $clients
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/clients",
     *     summary="Create a new client",
     *     tags={"Client"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Client data",
     *         @OA\JsonContent(
     *             required={"name", "email", "phone", "address"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255),
     *             @OA\Property(property="phone", type="string", maxLength=20),
     *             @OA\Property(property="address", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="street", type="string", maxLength=255),
     *                      @OA\Property(property="number", type="string", maxLength=20),
     *                      @OA\Property(property="complement", type="string", maxLength=255),
     *                      @OA\Property(property="city", type="string", maxLength=255),
     *                      @OA\Property(property="state", type="string", maxLength=2),
     *                      @OA\Property(property="zip_code", type="string", maxLength=9),
     *                  )
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function store(CreateClientRequest $request)
    {
        $client = $this->clientRepository->createClientWithAddress($request->all());

        return response()->json([
            'client' => $client
        ], Response::HTTP_CREATED);
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/clients/{client}",
     *     summary="Get client by id",
     *     tags={"Client"},
     *     @OA\Parameter(
     *          name="client",
     *          in="path",
     *          description="Client id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="client", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function edit(Client $client)
    {
        try {
            $client = $this->clientRepository->getClientWithAddressById($client->id);

            return response()->json([
                'client' => $client
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Client not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/clients/name/{name}",
     *     summary="Get client by name",
     *     tags={"Client"},
     *     @OA\Parameter(
     *          name="name",
     *          in="path",
     *          description="Client name",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="clients", type="array"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function getClientByName(string $name)
    {
        $clients = $this->clientRepository->getClientByName($name);

        return response()->json([
            'clients' => $clients
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/clients/document/{document}",
     *     summary="Get clients by document",
     *     tags={"Client"},
     *     @OA\Parameter(
     *          name="document",
     *          in="path",
     *          description="Client document",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clients found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="clients", type="array"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Clients not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function getClientsByDocument(string $document)
    {
        $clients = $this->clientRepository->getClientsByDocument($document);

        return response()->json([
            'clients' => $clients
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/clients/{client}",
     *     summary="Update client",
     *     tags={"Client"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Client data",
     *         @OA\JsonContent(
     *             required={"name", "email", "phone", "address"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255),
     *             @OA\Property(property="phone", type="string", maxLength=20),
     *             @OA\Property(property="address", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="street", type="string", maxLength=255),
     *                      @OA\Property(property="number", type="string", maxLength=20),
     *                      @OA\Property(property="complement", type="string", maxLength=255),
     *                      @OA\Property(property="city", type="string", maxLength=255),
     *                      @OA\Property(property="state", type="string", maxLength=2),
     *                      @OA\Property(property="zip_code", type="string", maxLength=9),
     *                  )
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client updated successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $client = $this->clientRepository->updateClientWithAddress($request->all(), $client->id);

        return response()->json([
            'client' => $client
        ], Response::HTTP_OK);
    }

    /**
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/api/clients/{client}",
     *     summary="Delete client",
     *     tags={"Client"},
     *     @OA\Parameter(
     *          name="client",
     *          in="path",
     *          description="Client id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(
     *          response=204,
     *          description="Client deleted successfully",
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Client not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *          ),
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function destroy(Client $client)
    {
        try {
            $this->clientRepository->delete($client->id);

            return response()->json([
                'message' => 'Client deleted successfully'
            ], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Client not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}

<?php

namespace App\Http;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as LaravelResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Response implements Responsable
{
    const CODE_SUCCESS = '000';
    const CODE_DATA_CREATED = '001';

    const CODE_TIMEOUT = '111';

    const CODE_ERROR = '300';
    const CODE_ERROR_INVALID_DATA = '301';

    const CODE_ERROR_UNAUTHORIZED = '402';
    const CODE_ERROR_UNAUTHENTICATED = '403';

    const CODE_ERROR_ROUTE_NOT_FOUND = '503';
    const CODE_ERROR_RESOURCE_NOT_FOUND = '504';
    const CODE_ERROR_DATABASE_TRANSACTION = '505';

    const CODE_ERROR_INVALID_SAYEMBARA_OWNER = '601';
    const CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT = '602';
    const CODE_ERROR_INVALID_SAYEMBARA_WINNER = '603';

    const CODE_ERROR_FORBIDDEN_SAYEMBARA_JOIN = '651';
    const CODE_ERROR_FORBIDDEN_SAYEMBARA_OUT = '652';


    const CODE_UNDEFINED_RESPONSE = '999';

    const RESPONSE_GROUP = [
        LaravelResponse::HTTP_OK => [
            self::CODE_SUCCESS
        ],
        LaravelResponse::HTTP_CREATED => [
            self::CODE_DATA_CREATED
        ],
        LaravelResponse::HTTP_UNAUTHORIZED => [
            self::CODE_ERROR_UNAUTHORIZED,
            self::CODE_ERROR_UNAUTHENTICATED
        ],
        LaravelResponse::HTTP_FORBIDDEN =>[
        ],
        LaravelResponse::HTTP_UNPROCESSABLE_ENTITY => [
            self::CODE_ERROR_INVALID_DATA,
            self::CODE_ERROR_RESOURCE_NOT_FOUND,
            self::CODE_ERROR_INVALID_SAYEMBARA_OWNER,
            self::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT,
            self::CODE_ERROR_INVALID_SAYEMBARA_WINNER,
            self::CODE_ERROR_FORBIDDEN_SAYEMBARA_JOIN,
            self::CODE_ERROR_FORBIDDEN_SAYEMBARA_OUT
        ],
        LaravelResponse::HTTP_SERVICE_UNAVAILABLE => [
            self::CODE_UNDEFINED_RESPONSE
        ],
        LaravelResponse::HTTP_NOT_FOUND =>[
          self::CODE_ERROR_ROUTE_NOT_FOUND
        ],
        LaravelResponse::HTTP_INTERNAL_SERVER_ERROR => [
            self::CODE_ERROR_DATABASE_TRANSACTION
        ]
    ];

    /**
     * @var array|Model|LengthAwarePaginator $data
     */
    protected $data;

    /**
     * @var string $code
     */
    protected string $code;

    /**
     *
     * @param string $code
     * @param array|Model $data
     */
    function __construct($code='',$data=[])
    {
        $this->data = $data;
        $this->code = $code;
    }

    public function getAvailableCode():array{
        /** @var \ReflectionClass $oClass */
        $oClass = new \ReflectionClass(__CLASS__);
        $consts = $oClass->getConstants();

        $codes = [];
        foreach ($consts as $key => $value){
            if (strpos($key,'CODE_')>-1){
                $codes[$value] = $key;
            }
        }
        return $codes;
    }

    public function getResponseMessageByCode($code){
        $label = $this->getResponseLabelFromCode($code);
        $label = str_replace('CODE_','',$label);
        $label = str_replace('_',' ',$label);
        $label = strtolower($label);
        return $label;
    }

    public function getResponseGroupByCode($code){
        foreach (self::RESPONSE_GROUP as $group => $value){
            if (in_array($code,$value)){
                return $group;
            }
        }
        return LaravelResponse::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function getResponseLabelFromCode($code){
        $codes = $this->getAvailableCode();
        if (!key_exists($code,$codes)){
            $label = $codes[self::CODE_UNDEFINED_RESPONSE];
        }else{
            $label = $codes[$code];
        }
        return $label;
    }

    public function formatData($data,$code){

        $message = $this->getResponseMessageByCode($code);

        $response = [
            'code'    =>  $code,
            'message' =>  $message,
            'data'    =>  $data,
        ];
        switch (true){
            case $data instanceof Model:
                /** @var Model $data */
                $response['data'] = $data->toArray();
                break;
            case $data instanceof Collection:
                /** @var Collection $data */
                $response['data'] = $data->toArray();
                break;
            case $data instanceof LengthAwarePaginator:
                /** @var LengthAwarePaginator $data */
                $paginationOptions = [
                    'last_item' => $data->lastItem(),
                    'total_item' => $data->total(),
                    'page' => $data->currentPage(),
                    'has_next_page' => $data->hasMorePages(),
                    'total_page' => $data->lastPage(),
                    'per_page' => (int) $data->perPage(),
                ];
                $response['paginator'] = $paginationOptions;

                /** @var Collection $data */
                $data = $data->getCollection();

                $response['data']= $data->toArray();
                break;
            case $data instanceof JsonResource:
                /** @var JsonResource $data */
                $data = $data->response();
                $response['data'] = $data->getData(true);
        }

        return $response;
    }

    public function responseJson($data,$code):JsonResponse
    {
        $self = new self();
        $httpStatus = $self->getResponseGroupByCode($code);
        $response = $self->formatData($data,$code);

        return response()->json($response,$httpStatus);
    }

    /** {@inheritDoc} */
    public function toResponse($request)
    {
        if ($request->expectsJson()){
            return $this->responseJson($this->data,$this->code);
        }
    }
}

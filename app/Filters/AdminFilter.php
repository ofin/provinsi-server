<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('TOKEN_SECRET');
        $header = $request->getServer('HTTP_AUTHORIZATION');
        $token = null;

        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $chipper = base64_decode($matches[1]);
                $config  = new \Config\Encryption();
                $encrypter = \Config\Services::encrypter($config);
                $token = $encrypter->decrypt($chipper);
            }
        }

        if (is_null($token) || empty($token)) {
            $response = [
                'success'   => false,
                'status'    => ResponseInterface::HTTP_UNAUTHORIZED,
                'messages'  => 'Access Token Required',
            ];
            return Services::response()
                ->setJSON($response)
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            if ($decoded->role != "2") {
                $response = [
                    'success'   => false,
                    'status'    => ResponseInterface::HTTP_UNAUTHORIZED,
                    'messages'  => 'Role not Match'
                ];
                return Services::response()
                    ->setJSON($response)
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $ex) {
            $response = [
                'success'   => false,
                'status'    => ResponseInterface::HTTP_UNAUTHORIZED,
                'messages'  => 'Access Token Expired'
            ];
            return Services::response()
                ->setJSON($response)
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}

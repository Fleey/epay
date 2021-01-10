<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller;

use App\Exception\ApiException;
use RuntimeException;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Throwable;
use function trigger_error;
use const E_USER_ERROR;

/**
 * @Controller(prefix="ex")
 */
class ExceptionController
{
    /**
     * @RequestMapping(route="api")
     *
     * @throws ApiException
     */
    public function api(): void
    {
        throw new ApiException('api of ExceptionController');
    }

    /**
     * @RequestMapping("/ex")
     * @throws Throwable
     */
    public function ex(): void
    {
        throw new RuntimeException('exception throw on ' . __METHOD__);
    }

    /**
     * @RequestMapping("/er")
     * @throws Throwable
     */
    public function error(): void
    {
        trigger_error('user error', E_USER_ERROR);
    }

    /**
     * @RequestMapping("/nt")
     * @throws Throwable
     */
    public function notice(): void
    {
        trigger_error('user error');
    }
}

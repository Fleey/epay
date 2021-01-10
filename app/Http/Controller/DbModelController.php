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

use App\Model\Entity\Count;
use App\Model\Entity\User;
use Exception;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Throwable;
use function random_int;

/**
 * Class DbModelController
 *
 * @since 2.0
 *
 * @Controller(prefix="dbModel")
 */
class DbModelController
{
    /**
     * @RequestMapping(route="find")
     *
     * @param Response $response
     *
     * @return Response
     *
     * @throws Throwable
     */
    public function find(Response $response): Response
    {
        $id   = $this->getId();
        $user = User::find($id);

        return $response->withData($user);
    }

    /**
     * @RequestMapping(route="save")
     *
     * @return array
     *
     * @throws Exception
     */
    public function save(): array
    {
        $user = new User();
        $user->setAge(random_int(1, 100));
        $user->setUserDesc('desc');

        $user->save();

        $count = Count::new();
        $count->setUserId($user->getId());
        $count->save();

        return $user->toArray();
    }

    /**
     * @RequestMapping(route="update")
     *
     * @return array
     *
     * @throws Throwable
     */
    public function update(): array
    {
        $id = $this->getId();

        User::updateOrInsert(['id' => $id], ['name' => 'swoft', 'userDesc' => 'swoft']);

        $user = User::find($id);

        return $user->toArray();
    }

    /**
     * @RequestMapping(route="delete")
     *
     * @return array
     *
     * @throws Throwable
     */
    public function delete(): array
    {
        $id     = $this->getId();
        $result = User::find($id)->delete();

        return [$result];
    }

    /**
     * @return int
     * @throws Throwable
     */
    public function getId(): int
    {
        $user = new User();
        $user->setAge(random_int(1, 100));
        $user->setUserDesc('desc');

        $user->save();

        return $user->getId();
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws Throwable
     */
    public function batchUpdate(): array
    {
        // User::truncate();
        User::updateOrCreate(['id' => 1], ['age' => 23]);
        User::updateOrCreate(['id' => 2], ['age' => 23]);

        $values = [
            ['id' => 1, 'age' => 18],
            ['id' => 2, 'age' => 19],
        ];
        $values = array_column($values, null, 'id');

        User::batchUpdateByIds($values);

        $users = User::find(array_column($values, 'id'));

        $updateResults = [];
        /** @var User $user */
        foreach ($users as $user) {
            $updateResults[$user->getId()] = true;
            if ($user->getAge() != $values[$user->getId()]['age']) {
                $updateResults[$user->getId()] = false;
            }
        }


        return $updateResults;
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws Throwable
     */
    public function propWhere(): array
    {
        User::updateOrInsert(['id' => 1000], ['userDesc' => 'swoft']);

        /** @var User|null $user */
        $user = User::whereProp(['userDesc' => 'swoft'])->first();

        return $user ? $user->toArray() : [];
    }
}

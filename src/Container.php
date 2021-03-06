<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace PierreHenry\Container;

use PierreHenry\Container\Exception\Container as ContainerException;
use PierreHenry\Container\Exception\ContainerNotFound as ContainerNotFoundException;
use PierreHenry\Container\Exception\Provider as ProviderException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var Providable[] */
    private $provider = [];

    public function register(string $providerName, Providable $provider): void
    {
        $this->provider[$providerName] = $provider;
    }

    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws ContainerException
     */
    public function get($id)
    {
        if ($this->has($id)) {
            try {
                return $this->retrieve($id);
            } catch (ProviderException $except) {
                throw new ContainerException($id);
            }
        }

        throw new ContainerNotFoundException($id);
    }

    public function has($id): bool
    {
        return isset($this->provider[$id]);
    }

    private function retrieve(string $id)
    {
        static $instance;

        if (empty($instance[$id])) {
            $instance[$id] = $this->provider[$id]->getService();
        }

        return $instance[$id];
    }
}

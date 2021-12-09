<?php

namespace App\Core\Utils;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DIC implements ContainerInterface
{
    private static array $registry = [];
    private static array $instances = [];
    private string $srcDir;
    private string $baseNamespace;

    public function __construct(string $srcDir, string $baseNamespace = 'App')
    {
        $this->srcDir = $srcDir;
        $this->baseNamespace = $baseNamespace;
    }

    /**
     * @param string $id
     * @param callable $resolver
     */
    public function set(string $id, callable $resolver): void
    {
        self::$registry[$id] = $resolver;
    }

    /**
     * @param string $id
     * @return object
     */
    public function get(string $id): object
    {
        return self::autowire($id);
    }

    /**
     * @param string $instance
     * @return object
     */
    public static function autowire(string $id): object
    {
        if (!array_key_exists($id, self::$instances)) {
            self::$instances[$id] = self::$registry[$id]();
        }
        return self::$instances[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, self::$registry);
    }

    /**
     * If an instance implementing this interface
     * exists in the container, it will be returned,
     * returns false otherwise
     * @param string $interfaceName
     * @return false|object
     * @throws \ReflectionException
     */
    public function getWithInterface(string $interfaceName)
    {
        foreach (self::$instances as $instance) {
            $reflexion = new \ReflectionClass($instance);
            if (in_array($interfaceName, $reflexion->getInterfaceNames())) {
                return $instance;
            }
        }

        foreach (self::$registry as $key => $callback) {
            $reflexion = new \ReflectionClass($callback());
            if (in_array($interfaceName, $reflexion->getInterfaceNames())) {
                return $this->get($key);
            }
        }

        return false;
    }

    /**
     * @param string $dirName
     * @throws \ReflectionException
     */
    public function autoInjects(string $dirName): void
    {
        $files = array_diff(scandir(dirname(__DIR__, 3) . '/src/' . $dirName), ['.', '..']);
        foreach ($files as $file) {
            // Don't scan directories
            if (pathinfo($file, PATHINFO_EXTENSION)) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $className = str_replace('.' . $extension, '', $file);
                $withNamespace = '\\App\\' . $dirName . '\\' . $className;

                $reflection = new \ReflectionClass($withNamespace);
                $constructorParameters = $reflection->getConstructor()->getParameters();

                $constructorDependencies = [];

                foreach ($constructorParameters as $constructorParameter) {
                    $interface = $constructorParameter->getType()->getName();
                    $dependency = $this->getWithInterface($interface);
                    $constructorDependencies[] = $dependency;
                }
                $this->set($className,
                    function () use ($reflection, $constructorDependencies) {
                        return $reflection->newInstanceArgs($constructorDependencies);
                    }
                );
            }
        }
    }

    /**
     * Récupère toutes les classes avec le namespace dans
     * les dossiers et sous dossiers du chemin passé en constructeur
     * @param $dir
     * @param array $results
     * @return array
     */
    public function getClasses($dir, &$results = []): array
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $newPath = str_replace(realpath($this->srcDir), '', $path);
                $namespaces = str_replace('/', '\\', $newPath);
                $withNamespace = '\\' . $this->baseNamespace . pathinfo($namespaces)["filename"];
                $results[] = $withNamespace;
            } else if ($value != "." && $value != "..") {
                $this->getClasses($path, $results);
            }
        }

        return $results;
    }

    /**
     * Scanne d'abbord toutes les classes implémentant
     * des interfaces
     * Puis injecte ces dépendances dans les classes
     * qui ont des dépendances non natives
     * @throws \ReflectionException
     */
    public function run(): void
    {
        foreach ($this->getClasses($this->srcDir) as $class) {

            $classes = explode('\\', $class);
            $className = end($classes);

            $reflection = new \ReflectionClass($class);
            if ($reflection->getInterfaceNames()) {
                $this->set($className, function () use ($reflection) {
                    return $reflection->newInstance();
                });
            }
        }

        foreach ($this->getClasses($this->srcDir) as $class) {
            $classes = explode('\\', $class);
            $className = end($classes);

            $reflection = new \ReflectionClass($class);
            if ($reflection->getConstructor()) {

                $constructorDependencies = [];
                foreach ($reflection->getConstructor()->getParameters() as $reflectionParameter) {
                    if (!$reflectionParameter->getType()->isBuiltin()) {
                        $interface = $reflectionParameter->getType()->getName();
                        $dependency = $this->getWithInterface($interface);
                        $constructorDependencies[] = $dependency;
                    }
                }
                $this->set($className,
                    function () use ($reflection, $constructorDependencies) {
                        return $reflection->newInstanceArgs($constructorDependencies);
                    }
                );
            }
        }
    }


}
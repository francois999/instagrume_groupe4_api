<?php

namespace Proxies\__CG__\App\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class User extends \App\Entity\User implements \Doctrine\ORM\Proxy\InternalProxy
{
    use \Symfony\Component\VarExporter\LazyGhostTrait {
        initializeLazyObject as __load;
        setLazyObjectAsInitialized as public __setInitialized;
        isLazyObjectInitialized as private;
        createLazyGhost as private;
        resetLazyObject as private;
    }

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'banned' => [parent::class, 'banned', null],
        "\0".parent::class."\0".'commentaires' => [parent::class, 'commentaires', null],
        "\0".parent::class."\0".'dislikes' => [parent::class, 'dislikes', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'likes' => [parent::class, 'likes', null],
        "\0".parent::class."\0".'password' => [parent::class, 'password', null],
        "\0".parent::class."\0".'posts' => [parent::class, 'posts', null],
        "\0".parent::class."\0".'roles' => [parent::class, 'roles', null],
        "\0".parent::class."\0".'username' => [parent::class, 'username', null],
        'banned' => [parent::class, 'banned', null],
        'commentaires' => [parent::class, 'commentaires', null],
        'dislikes' => [parent::class, 'dislikes', null],
        'id' => [parent::class, 'id', null],
        'likes' => [parent::class, 'likes', null],
        'password' => [parent::class, 'password', null],
        'posts' => [parent::class, 'posts', null],
        'roles' => [parent::class, 'roles', null],
        'username' => [parent::class, 'username', null],
    ];

    public function __construct(?\Closure $initializer = null, ?\Closure $cloner = null)
    {
        if ($cloner !== null) {
            return;
        }

        self::createLazyGhost($initializer, [
            "\0".parent::class."\0".'id' => true,
        ], $this);
    }

    public function __isInitialized(): bool
    {
        return isset($this->lazyObjectState) && $this->isLazyObjectInitialized();
    }

    public function __serialize(): array
    {
        $properties = (array) $this;
        unset($properties["\0" . self::class . "\0lazyObjectState"], $properties['__isCloning']);

        return $properties;
    }
}